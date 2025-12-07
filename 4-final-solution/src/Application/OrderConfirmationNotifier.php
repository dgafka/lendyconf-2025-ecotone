<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Customer\Customer;
use App\Domain\Order\Order;
use App\Domain\Order\OrderLine;
use App\Domain\Product\Product;

interface OrderConfirmationNotifier
{
    /**
     * @param OrderLine[] $orderLines
     */
    public function notify(string $orderId, array $orderLines, string $customerId): void;
}

