<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Order\Money;
use App\Domain\Product\Product;
use App\Domain\Product\ProductRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Uid\Uuid;

#[AsAlias(ProductRepository::class)]
final class DbalProductRepository implements ProductRepository
{
    public function __construct(private readonly Connection $connection) {}

    public function findById(Uuid $productId): ?Product
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM products WHERE product_id = :productId',
            ['productId' => $productId->toRfc4122()]
        );

        if ($row === false) {
            return null;
        }

        return $this->mapRowToProduct($row);
    }

    public function findByIds(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $ids = array_map(fn(Uuid $id) => $id->toRfc4122(), $productIds);

        $rows = $this->connection->fetchAllAssociative(
            'SELECT * FROM products WHERE product_id IN (?)',
            [$ids],
            [ArrayParameterType::STRING]
        );

        return array_map(fn(array $row) => $this->mapRowToProduct($row), $rows);
    }

    public function findAll(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT * FROM products ORDER BY name'
        );

        return array_map(fn(array $row) => $this->mapRowToProduct($row), $rows);
    }

    private function mapRowToProduct(array $row): Product
    {
        return new Product(
            Uuid::fromString($row['product_id']),
            $row['name'],
            $row['description'],
            new Money((int)$row['price_amount'], $row['price_currency'])
        );
    }
}

