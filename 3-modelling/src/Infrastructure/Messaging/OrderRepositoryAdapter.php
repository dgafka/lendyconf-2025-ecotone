<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging;

use App\Domain\Order\Order;
use App\Infrastructure\Persistence\DbalOrderRepository;
use Ecotone\Modelling\Attribute\Repository;
use Ecotone\Modelling\StandardRepository;

#[Repository]
final readonly class OrderRepositoryAdapter implements StandardRepository
{
    public function __construct(private DbalOrderRepository $repository)
    {

    }

    public function canHandle(string $aggregateClassName): bool
    {
        return $aggregateClassName === Order::class;
    }

    public function findBy(string $aggregateClassName, array $identifiers): ?object
    {
        return $this->repository->get($identifiers['orderId']);
    }

    public function save(array $identifiers, object $aggregate, array $metadata, ?int $versionBeforeHandling): void
    {
        $this->repository->save($aggregate);
    }
}
