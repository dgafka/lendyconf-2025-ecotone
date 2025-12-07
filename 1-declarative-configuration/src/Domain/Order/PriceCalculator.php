<?php

declare(strict_types=1);

namespace App\Domain\Order;

interface PriceCalculator
{
    /**
     * @param OrderLine[] $orderLines
     */
    public function calculate(array $orderLines): Money;
}

