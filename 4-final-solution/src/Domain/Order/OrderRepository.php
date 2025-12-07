<?php

declare(strict_types=1);

namespace App\Domain\Order;

interface OrderRepository
{
    public function save(Order $order): void;

    public function get(string $orderId): Order;

    /** @return Order[] */
    public function findByCustomerId(string $customerId): array;
}

