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
use Ecotone\Messaging\Attribute\Asynchronous;
use Ecotone\Messaging\Attribute\Parameter\Header;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\EventBus;

final class OrderService
{
    public function __construct(
        private readonly OrderConfirmationNotifier $notifier,
        private readonly StockService $stockService
    ) {}

    #[Asynchronous('async')]
    #[EventHandler(endpointId: 'decreaseStock')]
    public function decreaseStock(OrderWasPlaced $event): void
    {
        foreach ($event->orderLines as $line) {
            $this->stockService->decreaseStock($line->productId, $line->quantity);
        }
    }

    #[Asynchronous('async')]
    #[EventHandler(endpointId: 'sendConfirmationNotification')]
    public function sendConfirmationNotification(
        OrderWasPlaced $event,
        #[Header('customer.id')] string $customerId,
    ): void
    {
        $this->notifier->notify(
            $event->orderId,
            $event->orderLines,
            $customerId,
        );
    }
}

