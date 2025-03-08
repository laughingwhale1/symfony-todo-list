<?php

namespace App\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface IResponseHelper
{
    public function json(mixed $content, ?int $statusCode = 200): Response;
}