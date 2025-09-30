<?php

namespace App\Enums;

enum UserTypeEnum: int
{
    case ADMINISTRADOR = 1;
    case CLIENT = 2;
    case COLLABORATOR = 3;
}
