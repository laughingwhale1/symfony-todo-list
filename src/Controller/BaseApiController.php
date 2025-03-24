<?php

namespace App\Controller;

use App\Contracts\IResponseHelper;
use App\Exceptions\EntityCreationFailException;
use App\Exceptions\EntityNotFoundException;
use App\Exceptions\EntityUpdateFailException;
use App\Exceptions\LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseApiController extends AbstractController
{

    public function apiResponse(mixed $data, ?int $status = 200, ?string $message = ''): JsonResponse
    {
        return $this->json(['message' => $message, 'value' => $data], $status);
    }

    public function apiResponseError(\Throwable $throwable): JsonResponse
    {
        $body = [
            'message' => $throwable->getMessage(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => $throwable->getTrace(),
        ];

        switch ($throwable) {
            case $throwable instanceof EntityNotFoundException:
                return $this->json(
                    ['message' => 'Entity not found.', 'value' => $body],
                    Response::HTTP_NOT_FOUND
                );
            case $throwable instanceof EntityUpdateFailException:
                return $this->json(
                    ['message' => 'Error while updating entity.', 'value' => $body],
                    Response::HTTP_BAD_REQUEST
                );
            case $throwable instanceof EntityCreationFailException:
                return $this->json(
                    ['message' => 'Error while creating entity.', 'value' => $body],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            case $throwable instanceof LogicException:
                return $this->json(
                    ['message' => $throwable->getMessage(), 'value' => $body],
                    Response::HTTP_BAD_REQUEST
                ); // here you can pass your custom message
            default:
                return $this->json(
                    ['message' => 'Unknown exception.', 'value' => $body],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
        }
    }
}