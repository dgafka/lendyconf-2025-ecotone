<?php

declare(strict_types=1);

namespace App\Domain\Order;

use Symfony\Component\Uid\Uuid;

final readonly class OrderLine
{
    public function __construct(
        public Uuid $productId,
        public int $quantity
    ) {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity must be at least 1');
        }
    }
}

