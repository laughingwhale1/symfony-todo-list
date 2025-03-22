<?php

namespace App\DTO;

use Symfony\Component\PropertyInfo\Type;

abstract class BaseDTO 
{
    abstract public static function fromArray(array $data): self;
}