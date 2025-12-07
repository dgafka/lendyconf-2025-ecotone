<?php

declare(strict_types=1);

namespace App\Infrastructure\Email;

use App\Application\OrderConfirmationNotifier;
use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerRepository;
use App\Domain\Order\Order;
use App\Domain\Order\OrderLine;
use App\Domain\Order\OrderRepository;
use App\Domain\Product\Product;
use App\Domain\Product\ProductRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Uid\Uuid;

#[AsAlias(OrderConfirmationNotifier::class)]
final readonly class SmtpOrderConfirmationNotifier implements OrderConfirmationNotifier
{
    public function __construct(
        private MailerInterface    $mailer,
        private CustomerRepository $customerRepository,
        private ProductRepository  $productRepository,
        private OrderRepository    $orderRepository
    ) {}

    /**
     * @param OrderLine[] $orderLines
     * @param Product[] $products
     */
    public function notify(string $orderId, array $orderLines, string $customerId): void
    {
        $customer = $this->customerRepository->get(Uuid::fromString($customerId));

        $productIds = array_map(fn(OrderLine $line) => $line->productId, $orderLines);
        $productsById = [];
        foreach ($this->productRepository->findByIds($productIds) as $product) {
            $productsById[$product->productId->toRfc4122()] = $product;
        }

        $orderItems = [];
        foreach ($orderLines as $line) {
            $product = $productsById[$line->productId->toRfc4122()];
            $orderItems[] = [
                'product' => $product,
                'quantity' => $line->quantity,
                'lineTotal' => $product->price->amount * $line->quantity,
            ];
        }

        $email = (new TemplatedEmail())
            ->from(new Address('noreply@electronics-store.com', 'Electronics Store'))
            ->to(new Address($customer->email, $customer->name))
            ->subject('Order Confirmation #' . substr($orderId, 0, 8))
            ->htmlTemplate('email/order_confirmation.html.twig')
            ->context([
                'order' => $this->orderRepository->get($orderId),
                'customer' => $customer,
                'orderItems' => $orderItems,
            ]);

        $this->mailer->send($email);
    }
}

