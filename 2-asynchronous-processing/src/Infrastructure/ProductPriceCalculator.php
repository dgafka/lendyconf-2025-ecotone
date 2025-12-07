<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Order\Money;
use App\Domain\Order\OrderLine;
use App\Domain\Order\PriceCalculator;
use App\Domain\Product\ProductRepository;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(PriceCalculator::class)]
final class ProductPriceCalculator implements PriceCalculator
{
    public function __construct(private readonly ProductRepository $productRepository) {}

    public function calculate(array $orderLines): Money
    {
        $productIds = array_map(fn(OrderLine $line) => $line->productId, $orderLines);
        $products = $this->productRepository->findByIds($productIds);

        $productsById = [];
        foreach ($products as $product) {
            $productsById[$product->productId->toRfc4122()] = $product;
        }

        $totalAmount = 0;
        $currency = 'USD';

        foreach ($orderLines as $line) {
            $productId = $line->productId->toRfc4122();
            if (!isset($productsById[$productId])) {
                throw new \InvalidArgumentException("Product with id {$productId} not found");
            }
            $product = $productsById[$productId];
            $totalAmount += $product->price->amount * $line->quantity;
            $currency = $product->price->currency;
        }

        return new Money($totalAmount, $currency);
    }
}

