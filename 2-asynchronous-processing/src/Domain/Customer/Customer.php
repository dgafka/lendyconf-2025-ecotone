<?php

declare(strict_types=1);

namespace App\Domain\Customer;

use Symfony\Component\Uid\Uuid;

final readonly class Customer
{
    public function __construct(
        public Uuid $customerId,
        public string $name,
        public string $email
    ) {}
}

