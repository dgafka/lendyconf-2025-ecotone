<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Uid\Uuid;

#[AsAlias(CustomerRepository::class)]
final class DbalCustomerRepository implements CustomerRepository
{
    public function __construct(private readonly Connection $connection) {}

    public function findById(Uuid $customerId): ?Customer
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM customers WHERE customer_id = :customerId',
            ['customerId' => $customerId->toRfc4122()]
        );

        if (!$row) {
            return null;
        }

        return $this->mapRowToCustomer($row);
    }

    public function get(Uuid $customerId): Customer
    {
        $customer = $this->findById($customerId);

        if (!$customer) {
            throw new \RuntimeException("Customer with id {$customerId->toRfc4122()} not found");
        }

        return $customer;
    }

    public function findAll(): array
    {
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM customers ORDER BY name');

        return array_map(fn(array $row) => $this->mapRowToCustomer($row), $rows);
    }

    private function mapRowToCustomer(array $row): Customer
    {
        return new Customer(
            Uuid::fromString($row['customer_id']),
            $row['name'],
            $row['email']
        );
    }
}

