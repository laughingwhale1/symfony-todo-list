<?php

namespace App\DTO\Task;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $title
    )
    {

    }
}