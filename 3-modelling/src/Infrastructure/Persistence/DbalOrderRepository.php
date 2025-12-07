<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Order\Money;
use App\Domain\Order\Order;
use App\Domain\Order\OrderLine;
use App\Domain\Order\OrderRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Uid\Uuid;

#[AsAlias(OrderRepository::class)]
final class DbalOrderRepository implements OrderRepository
{
    public function __construct(private readonly Connection $connection) {}

    public function save(Order $order): void
    {
        $orderLinesData = array_map(
            fn(OrderLine $line) => [
                'productId' => $line->productId->toRfc4122(),
                'quantity' => $line->quantity,
            ],
            $order->getOrderLines()
        );

        $this->connection->insert('orders', [
            'order_id' => $order->getOrderId(),
            'order_lines' => json_encode($orderLinesData),
            'customer_id' => $order->getCustomerId(),
            'status' => $order->getStatus()->value,
            'total_price_amount' => $order->getTotalPrice()->amount,
            'total_price_currency' => $order->getTotalPrice()->currency,
            'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function get(string $orderId): Order
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM orders WHERE order_id = :orderId',
            ['orderId' => $orderId]
        );

        if (!$row) {
            throw new \RuntimeException("Order with id {$orderId} not found");
        }

        return $this->mapRowToOrder($row);
    }

    public function findByCustomerId(string $customerId): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT * FROM orders WHERE customer_id = :customerId ORDER BY created_at DESC',
            ['customerId' => $customerId]
        );

        return array_map(fn(array $row) => $this->mapRowToOrder($row), $rows);
    }

    private function mapRowToOrder(array $row): Order
    {
        $orderLinesData = json_decode($row['order_lines'], true);
        $orderLines = array_map(
            fn(array $lineData) => new OrderLine(
                Uuid::fromString($lineData['productId']),
                $lineData['quantity']
            ),
            $orderLinesData
        );

        return new Order(
            $row['order_id'],
            $orderLines,
            $row['customer_id'],
            new Money((int)$row['total_price_amount'], $row['total_price_currency']),
            new \DateTimeImmutable($row['created_at'])
        );
    }
}

