<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging;

use Ecotone\Dbal\DbalBackedMessageChannelBuilder;
use Ecotone\Messaging\Attribute\ServiceContext;
use Ecotone\SymfonyBundle\Config\SymfonyConnectionReference;

final readonly class DbalConfiguration
{
    #[ServiceContext]
    public function connectionReference()
    {
        return SymfonyConnectionReference::defaultConnection('default');
    }

    #[ServiceContext]
    public function dbalConnectionModule()
    {
        return DbalBackedMessageChannelBuilder::create('async');
    }
}
