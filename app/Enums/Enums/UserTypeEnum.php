<?php

declare(strict_types=1);

namespace App\Enums\Enums;

enum UserTypeEnum: int
{
    case ADMINISTRADOR = 1;
    case CLIENT = 2;
    case COLLABORATOR = 3;
}
