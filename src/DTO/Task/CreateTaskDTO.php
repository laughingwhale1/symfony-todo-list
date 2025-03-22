<?php

namespace App\DTO\Task;

class CreateTaskDTO
{
    public function __construct(
        public string $title,
        public bool $completed
    )
    {

    }
}