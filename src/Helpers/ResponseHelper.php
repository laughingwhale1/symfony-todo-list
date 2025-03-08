<?php

namespace App\Helpers;

use App\Contracts\IResponseHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class ResponseHelper implements IResponseHelper
{
    public function __construct(private SerializerInterface $serializer)
    {

    }
    public function json(mixed $content, ?int $statusCode = 200): Response
    {
        $jsonContent = $this->serializer->serialize($content, 'json');

        return new Response($jsonContent, $statusCode);
    }
}