<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Domain\Order\Money;
use Symfony\Component\Uid\Uuid;

final readonly class Product
{
    public function __construct(
        public Uuid $productId,
        public string $name,
        public string $description,
        public Money $price
    ) {}
}

