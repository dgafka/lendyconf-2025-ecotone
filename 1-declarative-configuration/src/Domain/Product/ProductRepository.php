<?php

declare(strict_types=1);

namespace App\Domain\Product;

use Symfony\Component\Uid\Uuid;

interface ProductRepository
{
    public function findById(Uuid $productId): ?Product;

    /**
     * @param Uuid[] $productIds
     * @return Product[]
     */
    public function findByIds(array $productIds): array;

    /**
     * @return Product[]
     */
    public function findAll(): array;
}

