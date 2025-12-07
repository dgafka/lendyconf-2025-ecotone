<?php

declare(strict_types=1);

namespace App\Domain\Customer;

use Symfony\Component\Uid\Uuid;

interface CustomerRepository
{
    public function findById(Uuid $customerId): ?Customer;

    public function get(Uuid $customerId): Customer;

    /** @return Customer[] */
    public function findAll(): array;
}

