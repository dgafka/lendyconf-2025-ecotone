<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Clock;
use Symfony\Component\Uid\Uuid;

final class Order
{
    private string $orderId;
    /** @var OrderLine[] */
    private array $orderLines;
    private string $customerId;
    private OrderStatus $status;
    private Money $totalPrice;
    private \DateTimeImmutable $createdAt;

    /**
     * @param OrderLine[] $orderLines
     */
    public function __construct(
        string $orderId,
        array $orderLines,
        string $customerId,
        Money $totalPrice,
        \DateTimeImmutable $createdAt
    ) {
        $this->assertOrderLinesNotEmpty($orderLines);

        $this->orderId = $orderId;
        $this->orderLines = $orderLines;
        $this->customerId = $customerId;
        $this->totalPrice = $totalPrice;
        $this->status = OrderStatus::IN_PROGRESS;
        $this->createdAt = $createdAt;
    }

    public static function place(
        PlaceOrder $command,
        string $customerId,
        PriceCalculator $priceCalculator,
        Clock $clock,
    ): self {
        return new self(
            Uuid::v4()->toRfc4122(),
            $command->orderLines,
            $customerId,
            $priceCalculator->calculate($command->orderLines),
            $clock->now()
        );
    }

    /**
     * @param OrderLine[] $orderLines
     */
    private function assertOrderLinesNotEmpty(array $orderLines): void
    {
        if (empty($orderLines)) {
            throw new \InvalidArgumentException('Order must contain at least one product');
        }
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @return OrderLine[]
     */
    public function getOrderLines(): array
    {
        return $this->orderLines;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getTotalPrice(): Money
    {
        return $this->totalPrice;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}

