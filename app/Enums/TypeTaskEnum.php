<?php

namespace App\Enums;

enum TypeTaskEnum: string

{
    case EPIC = 'epic';
    case BUG = 'bug';
    case TASK = 'task';
    case IMPROVE = 'improvement';
    case FEATURE = 'feature';
}
