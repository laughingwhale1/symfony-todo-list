<?php

namespace App\DTO\Task;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $title
    )
    {

    }
}