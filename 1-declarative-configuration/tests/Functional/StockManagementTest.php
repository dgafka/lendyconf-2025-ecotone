<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Infrastructure\Stock\RedisStockService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

final class StockManagementTest extends WebTestCase
{
    private const string LAPTOP_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const string SMARTPHONE_ID = '550e8400-e29b-41d4-a716-446655440002';
    private const string CUSTOMER_ID = '550e8400-e29b-41d4-a716-446655440101';

    public function test_stock_is_decreased_when_order_is_placed(): void
    {
        $client = static::createClient();

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

        // Verify stock was decreased
        $this->assertEquals(48, $stockService->getStock($laptopId)); // 50 - 2
        $this->assertEquals(25, $stockService->getStock($smartphoneId)); // 30 - 5
    }

    public function test_stock_defaults_to_100_when_not_initialized(): void
    {
        static::createClient();

        /** @var RedisStockService $stockService */
        $stockService = static::getContainer()->get(RedisStockService::class);

        $randomProductId = Uuid::v4();

        // Should return default stock of 100
        $this->assertEquals(100, $stockService->getStock($randomProductId));
    }
}

