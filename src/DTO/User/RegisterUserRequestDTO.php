<?php

namespace App\DTO\User;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $email,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $password
    )
    {

    }
}