<?php

namespace App\Controller;

use App\Contracts\IResponseHelper;
use App\Exceptions\EntityCreationFailException;
use App\Exceptions\EntityNotFoundException;
use App\Exceptions\EntityUpdateFailException;
use App\Exceptions\LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseApiController extends AbstractController
{

    public function __construct(
        private readonly IResponseHelper $responseHelper
    )
    {

    }

    public function apiResponse(mixed $data): Response
    {
        return $this->responseHelper->json($data);
    }

    public function apiResponseError(\Throwable $throwable): Response
    {
        $body = [
            'message' => $throwable->getMessage(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => $throwable->getTrace(),
        ];

        switch ($throwable) {
            case $throwable instanceof EntityNotFoundException:
                return $this->responseHelper->json(
                    ['message' => 'Entity not found.', 'value' => $body],
                    Response::HTTP_NOT_FOUND
                );
            case $throwable instanceof EntityUpdateFailException:
                return $this->responseHelper->json(
                    ['message' => 'Error while updating entity.', 'value' => $body],
                    Response::HTTP_BAD_REQUEST
                );
            case $throwable instanceof EntityCreationFailException:
                return $this->responseHelper->json(
                    ['message' => 'Error while creating entity.', 'value' => $body],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            case $throwable instanceof LogicException:
                return $this->responseHelper->json(
                    ['message' => $throwable->getMessage(), 'value' => $body],
                    Response::HTTP_BAD_REQUEST
                ); // here you can pass your custom message
            default:
                return $this->responseHelper->json(
                    ['message' => 'Unknown exception.', 'value' => $body],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
        }
    }
}