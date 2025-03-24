<?php

namespace App\Controller;

use App\DTO\User\RegisterUserRequestDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class AccountController extends BaseApiController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $entityManager,
    )
    {

    }

    #[Route('/auth/register', name: 'create_account', methods: ['POST'])]
    #[OA\RequestBody(
        content: new Model(type: RegisterUserRequestDTO::class)
    )]
    #[OA\Tag(name: 'auth')]
    public function register(#[MapRequestPayload] RegisterUserRequestDTO $requestDTO): JsonResponse
    {
        $user = new User();

        $user->setEmail($requestDTO->email);
        $hashedPassword = $this->hasher->hashPassword(
            $user,
            $requestDTO->password
        );
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->apiResponse(['id' => $user->getId()], Response::HTTP_CREATED, 'Account created.');
    }
}