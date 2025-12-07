<?php

declare(strict_types=1);

namespace App\Domain\Stock;

use Symfony\Component\Uid\Uuid;

interface StockService
{
    public const int DEFAULT_STOCK = 100;

    public function getStock(Uuid $productId): int;

    /**
     * @param Uuid[] $productIds
     * @return array<string, int> productId => stock
     */
    public function getStockForProducts(array $productIds): array;

    public function decreaseStock(Uuid $productId, int $quantity): void;
}

