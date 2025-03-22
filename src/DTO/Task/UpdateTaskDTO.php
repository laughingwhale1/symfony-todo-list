<?php

namespace App\DTO\Task;


use App\DTO\BaseDTO;

class UpdateTaskDTO extends BaseDTO
{
    public function __construct(
        public string $title,
        public bool $completed
    )
    {

    }

    public static function fromArray(array $data): BaseDTO
    {
        return new self(
            title: $data['title'],
            completed: true
        );
    }
}