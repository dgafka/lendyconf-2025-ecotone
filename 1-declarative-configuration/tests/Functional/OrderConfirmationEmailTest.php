<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $this->assertEmailHeaderSame($email, 'To', 'Alice Johnson <' . self::CUSTOMER_EMAIL . '>');
        $this->assertEmailHeaderSame($email, 'From', 'Electronics Store <noreply@electronics-store.com>');
        $this->assertEmailTextBodyContains($email, 'Order Confirmed');
        $this->assertEmailTextBodyContains($email, 'Laptop Pro 15');
        $this->assertEmailTextBodyContains($email, 'Smartphone X12');
        $this->assertEmailTextBodyContains($email, '$3,097.00'); // Total price
    }
}

