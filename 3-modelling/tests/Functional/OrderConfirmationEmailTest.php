<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\DBAL\Connection;
use Ecotone\Messaging\Config\ConfiguredMessagingSystem;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

final class OrderConfirmationEmailTest extends WebTestCase
{
    use \Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;

    private const string LAPTOP_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const string SMARTPHONE_ID = '550e8400-e29b-41d4-a716-446655440002';
    private const string CUSTOMER_ID = '550e8400-e29b-41d4-a716-446655440101';
    private const string CUSTOMER_EMAIL = 'alice.johnson@example.com';

    public function test_order_confirmation_email_is_sent_when_order_is_placed(): void
    {
        $client = static::createClient();

        $this->assertAsyncConsumerIsAvailable();
        $this->clearAsyncQueue();

        $client->request(
            'POST',
            '/customers/' . self::CUSTOMER_ID . '/order',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'orderLines' => [
                    ['productId' => self::LAPTOP_ID, 'quantity' => 1],
                    ['productId' => self::SMARTPHONE_ID, 'quantity' => 2],
                ]
            ])
        );

        $this->assertResponseStatusCodeSame(201);

        // Verify no email was sent yet (async processing should defer this)
        if (\count(self::getMailerMessages()) > 0) {
            throw new \RuntimeException(
                'Email was sent synchronously. Please enable asynchronous processing on the event handler.'
            );
        }

        $this->runConsumer($client);

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $this->assertEmailHeaderSame($email, 'To', 'Alice Johnson <' . self::CUSTOMER_EMAIL . '>');
        $this->assertEmailHeaderSame($email, 'From', 'Electronics Store <noreply@electronics-store.com>');
        $this->assertEmailTextBodyContains($email, 'Order Confirmed');
        $this->assertEmailTextBodyContains($email, 'Laptop Pro 15');
        $this->assertEmailTextBodyContains($email, 'Smartphone X12');
        $this->assertEmailTextBodyContains($email, '$3,097.00'); // Total price
    }

    private function runConsumer(\Symfony\Bundle\FrameworkBundle\KernelBrowser $client): void
    {
        $kernel = $client->getKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'ecotone:run',
            'consumerName' => 'async',
            '--executionTimeLimit' => 2000,
        ]);
        $application->run($input, new NullOutput());
    }

    private function clearAsyncQueue(): void
    {
        try {
            /** @var Connection $connection */
            $connection = static::getContainer()->get(Connection::class);
            $connection->executeStatement("DELETE FROM enqueue WHERE queue = 'async'");
        }catch (\Exception $e) {}
    }

    private function assertAsyncConsumerIsAvailable(): void
    {
        /** @var ConfiguredMessagingSystem $messagingSystem */
        $messagingSystem = static::getContainer()->get(ConfiguredMessagingSystem::class);
        $consumers = $messagingSystem->list();

        if (!in_array('async', $consumers, true)) {
            throw new \RuntimeException(
                'The "async" consumer is not available. Please add asynchronous Message Channel first.'
            );
        }
    }
}

