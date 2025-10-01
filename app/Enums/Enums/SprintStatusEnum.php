<?php

declare(strict_types=1);

namespace App\Enums\Enums;

enum SprintStatusEnum: string
{
    case BACKLOG = 'BACKLOG';
    case PLANNING = 'PLANNING';
    case ACTIVE = 'ACTIVE';
    case PAUSED = 'PAUSED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
}
