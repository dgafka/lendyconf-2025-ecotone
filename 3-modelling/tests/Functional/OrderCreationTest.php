<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Domain\Order\OrderRepository;
use App\Domain\Order\OrderStatus;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OrderCreationTest extends WebTestCase
{
    // Use existing seeded product IDs from migration
    private const string LAPTOP_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const string SMARTPHONE_ID = '550e8400-e29b-41d4-a716-446655440002';
    // Use existing seeded customer ID from migration (Alice Johnson)
    private const string CUSTOMER_ID = '550e8400-e29b-41d4-a716-446655440101';

    public function test_order_can_be_created(): void
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

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('orderId', $response);

        $orderId = $response['orderId'];

        /** @var OrderRepository $orderRepository */
        $orderRepository = static::getContainer()->get(OrderRepository::class);
        $order = $orderRepository->get($orderId);

        $this->assertEquals(self::CUSTOMER_ID, $order->getCustomerId());
        $this->assertEquals(OrderStatus::IN_PROGRESS, $order->getStatus());

        // 1 laptop ($1299) + 2 smartphones ($899 each) = $3097 = 309700 cents
        $this->assertEquals(309700, $order->getTotalPrice()->amount);
        $this->assertCount(2, $order->getOrderLines());
    }
}

