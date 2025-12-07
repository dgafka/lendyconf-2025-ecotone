<?php

declare(strict_types=1);

namespace App\Infrastructure\Stock;

use App\Domain\Stock\StockService;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Uid\Uuid;

/**
 * This imitates external Stock Service, that normally would expose some HTTP API.
 * Implementation is done on Redis for simplicity.
 */
#[AsAlias(StockService::class)]
final class RedisStockService implements StockService
{
    private \Redis $redis;

    public function __construct(string $redisDsn)
    {
        $this->redis = new \Redis();
        $parsed = parse_url($redisDsn);
        $this->redis->connect($parsed['host'], $parsed['port'] ?? 6379);
    }

    public function getStock(Uuid $productId): int
    {
        $key = $this->getKey($productId);
        $stock = $this->redis->get($key);

        if ($stock === false) {
            return self::DEFAULT_STOCK;
        }

        return (int) $stock;
    }

    public function getStockForProducts(array $productIds): array
    {
        $result = [];
        foreach ($productIds as $productId) {
            $result[$productId->toRfc4122()] = $this->getStock($productId);
        }
        return $result;
    }

    public function decreaseStock(Uuid $productId, int $quantity): void
    {
        $key = $this->getKey($productId);

        // If key doesn't exist, initialize with default and then decrement
        if (!$this->redis->exists($key)) {
            $this->redis->set($key, self::DEFAULT_STOCK);
        }

        $this->redis->decrBy($key, $quantity);
    }

    public function setStock(Uuid $productId, int $quantity): void
    {
        $key = $this->getKey($productId);
        $this->redis->set($key, $quantity);
    }

    private function getKey(Uuid $productId): string
    {
        return 'stock:' . $productId->toRfc4122();
    }
}

