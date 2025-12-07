<?php

declare(strict_types=1);

namespace App\Domain\Order;

final readonly class PlaceOrder
{
    /**
     * @param OrderLine[] $orderLines
     */
    public function __construct(
        public array $orderLines,
    ) {}
}

