<?php

namespace App\DTO\Task;


use Symfony\Component\Validator\Constraints as Assert;
use App\DTO\BaseDTO;

class UpdateTaskDTO extends BaseDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $title,
    )
    {

    }

    public static function fromArray(array $data): BaseDTO
    {
        return new self(
            title: $data['title'],
        );
    }
}