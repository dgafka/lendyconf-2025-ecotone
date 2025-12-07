<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging;

use Ecotone\Messaging\Attribute\Converter;
use Symfony\Component\Uid\Uuid;

final readonly class UuidConverter
{
    #[Converter]
    public function fromString(string $uuid): Uuid
    {
        return Uuid::fromString($uuid);
    }

    #[Converter]
    public function toString(Uuid $uuid): string
    {
        return $uuid->toRfc4122();
    }
}
