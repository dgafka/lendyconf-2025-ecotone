<?php

declare(strict_types=1);

namespace App\UI\Http;

use App\Application\OrderService;
use App\Domain\Order\PlaceOrder;
use App\Domain\Customer\CustomerRepository;
use App\Domain\Order\OrderLine;
use App\Domain\Order\OrderRepository;
use App\Domain\Product\ProductRepository;
use App\Domain\Stock\StockService;
use Ecotone\Modelling\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Throwable;

final class OrderController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository  $productRepository,
        private readonly OrderRepository    $orderRepository,
        private readonly CustomerRepository $customerRepository,
        private readonly StockService       $stockService,
        private readonly CommandBus         $commandBus,
    )
    {
    }

    #[Route('/customers/{customerId}/order', name: 'customer_place_order', methods: ['POST'])]
    public function placeOrder(string $customerId, Request $request): Response
    {
        $orderId = $this->commandBus->sendWithRouting(
            routingKey: 'placeOrder',
            command: $request->getContent(),
            commandMediaType: 'application/json',
            metadata: ['customer.id' => $customerId],
        );

        return new JsonResponse([
            'orderId' => $orderId,
            'redirectUrl' => $this->generateUrl('customer_order_show', [
                'customerId' => $customerId,
                'orderId' => $orderId
            ])
        ], Response::HTTP_CREATED);
    }

    #[Route('/customers/{customerId}/shop', name: 'customer_shop', methods: ['GET'])]
    public function showShop(string $customerId): Response
    {
        $customer = $this->customerRepository->get(Uuid::fromString($customerId));
        $products = $this->productRepository->findAll();

        $productIds = array_map(fn($p) => $p->productId, $products);
        $stock = $this->stockService->getStockForProducts($productIds);

        return $this->render('order/shop.html.twig', [
            'customer' => $customer,
            'products' => $products,
            'stock' => $stock,
        ]);
    }

    #[Route('/customers/{customerId}/orders', name: 'customer_orders', methods: ['GET'])]
    public function listOrders(string $customerId): Response
    {
        $customer = $this->customerRepository->get(Uuid::fromString($customerId));
        $orders = $this->orderRepository->findByCustomerId($customerId);

        return $this->render('order/list.html.twig', [
            'customer' => $customer,
            'orders' => $orders,
        ]);
    }

    #[Route('/customers/{customerId}/orders/{orderId}', name: 'customer_order_show', methods: ['GET'])]
    public function showOrder(string $customerId, string $orderId): Response
    {
        $customer = $this->customerRepository->get(Uuid::fromString($customerId));
        $order = $this->orderRepository->get($orderId);

        $productIds = array_map(fn(OrderLine $line) => $line->productId, $order->getOrderLines());
        $products = $this->productRepository->findByIds($productIds);

        $productsById = [];
        foreach ($products as $product) {
            $productsById[$product->productId->toRfc4122()] = $product;
        }

        $orderItems = [];
        foreach ($order->getOrderLines() as $line) {
            $product = $productsById[$line->productId->toRfc4122()];
            $orderItems[] = [
                'product' => $product,
                'quantity' => $line->quantity,
                'lineTotal' => $product->price->amount * $line->quantity,
            ];
        }

        return $this->render('order/show.html.twig', [
            'customer' => $customer,
            'order' => $order,
            'orderItems' => $orderItems,
        ]);
    }

    public function prepareCommand(Request $request): PlaceOrder
    {
        $data = json_decode($request->getContent(), true);
        $orderLines = [];

        foreach ($data['orderLines'] ?? [] as $line) {
            $orderLines[] = new OrderLine(
                Uuid::fromString($line['productId']),
                (int)$line['quantity']
            );
        }

        return new PlaceOrder($orderLines);
    }
}

