<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Clock;
use App\Domain\Customer\CustomerRepository;
use App\Domain\Order\Event\OrderWasPlaced;
use App\Domain\Order\Order;
use App\Domain\Order\OrderLine;
use App\Domain\Order\OrderRepository;
use App\Domain\Order\PlaceOrder;
use App\Domain\Order\PriceCalculator;
use App\Domain\Product\ProductRepository;
use App\Domain\Stock\StockService;
use Ecotone\Messaging\Attribute\Parameter\Header;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\EventBus;

final class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly PriceCalculator $priceCalculator,
        private readonly Clock $clock,
        private readonly OrderConfirmationNotifier $notifier,
        private readonly StockService $stockService
    ) {}

    public function place(
        PlaceOrder $command,
        string $customerId,
    ): string
    {
        $order = Order::place(
            $command,
            $customerId,
            $this->priceCalculator,
            $this->clock,
        );
        $this->orderRepository->save($order);

        $this->sendConfirmationNotification($order->getOrderId(), $order->getOrderLines(), $customerId);
        $this->decreaseStock($command->orderLines);

        return $order->getOrderId();
    }

    /**
     * @param OrderLine[] $orderLines
     */
    public function decreaseStock(array $orderLines): void
    {
        foreach ($orderLines as $line) {
            $this->stockService->decreaseStock($line->productId, $line->quantity);
        }
    }

    /**
     * @param OrderLine[] $orderLines
     */
    private function sendConfirmationNotification(string $orderId, array $orderLines, string $customerId): void
    {
        $this->notifier->notify($orderId, $orderLines, $customerId);
    }
}

