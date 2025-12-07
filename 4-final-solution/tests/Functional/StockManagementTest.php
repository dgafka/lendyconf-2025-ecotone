<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Infrastructure\Stock\RedisStockService;
use Doctrine\DBAL\Connection;
use Ecotone\Messaging\Config\ConfiguredMessagingSystem;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Uid\Uuid;

final class StockManagementTest extends WebTestCase
{
    private const string LAPTOP_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const string SMARTPHONE_ID = '550e8400-e29b-41d4-a716-446655440002';
    private const string CUSTOMER_ID = '550e8400-e29b-41d4-a716-446655440101';

    public function test_stock_is_decreased_when_order_is_placed(): void
    {
        $client = static::createClient();

        $this->assertAsyncConsumerIsAvailable();
        $this->clearAsyncQueue();

        /** @var RedisStockService $stockService */
        $stockService = static::getContainer()->get(RedisStockService::class);

        // Initialize stock
        $laptopId = Uuid::fromString(self::LAPTOP_ID);
        $smartphoneId = Uuid::fromString(self::SMARTPHONE_ID);

        $stockService->setStock($laptopId, 50);
        $stockService->setStock($smartphoneId, 30);

        // Verify initial stock
        $this->assertEquals(50, $stockService->getStock($laptopId));
        $this->assertEquals(30, $stockService->getStock($smartphoneId));

        // Place order via HTTP request: 2 laptops, 5 smartphones
        $client->request(
            'POST',
            '/customers/' . self::CUSTOMER_ID . '/order',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'orderLines' => [
                    ['productId' => self::LAPTOP_ID, 'quantity' => 2],
                    ['productId' => self::SMARTPHONE_ID, 'quantity' => 5],
                ]
            ])
        );

        $this->assertResponseStatusCodeSame(201);

        // Verify stock was NOT decreased yet (async processing should defer this)
        if ($stockService->getStock($laptopId) !== 50 || $stockService->getStock($smartphoneId) !== 30) {
            throw new \RuntimeException(
                'Stock was decreased synchronously. Please enable asynchronous processing on the event handler.'
            );
        }

        $this->runConsumer($client);

        // Verify stock was decreased
        $this->assertEquals(48, $stockService->getStock($laptopId)); // 50 - 2
        $this->assertEquals(25, $stockService->getStock($smartphoneId)); // 30 - 5
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

