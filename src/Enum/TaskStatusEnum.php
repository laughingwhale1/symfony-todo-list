<?php

namespace App\Enum;

enum TaskStatusEnum: string
{
    case New = 'new';
    case Completed = 'completed';
    case InProgress = 'in_progress';
}
