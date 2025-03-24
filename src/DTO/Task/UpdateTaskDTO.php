<?php

namespace App\DTO\Task;


use App\Enum\TaskStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;
use App\DTO\BaseDTO;

class UpdateTaskDTO extends BaseDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $title,

        #[Assert\NotBlank]
        public TaskStatusEnum $newStatus
    )
    {

    }

    public static function fromArray(array $data): BaseDTO
    {
        return new self(
            title: $data['title'],
            newStatus: $data['newStatus']
        );
    }
}