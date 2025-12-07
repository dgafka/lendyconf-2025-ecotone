<?php

declare(strict_types=1);

namespace App\Domain\Order;

enum OrderStatus: string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}

