<?php

declare(strict_types=1);

namespace App\Domain\Order\Event;

use App\Domain\Order\OrderLine;

final readonly class OrderWasPlaced
{
    /**
     * @param OrderLine[] $orderLines
     */
    public function __construct(
        public string $orderId,
        public array $orderLines,
    ) {}
}
