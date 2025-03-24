<?php

namespace App\Controller;

use App\DTO\Task\CreateTaskRequestDTO;
use App\DTO\Task\UpdateTaskRequestDTO;
use App\Entity\Task;
use App\Enum\TaskStatusEnum;
use App\Exceptions\EntityNotFoundException;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

final class TaskController extends BaseApiController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/api/tasks', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of tasks'
    )]
    #[OA\Parameter(
        name: 'offset',
        description: 'Number of items to skip for pagination',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 0)
    )]
    #[OA\Tag(name: 'tasks')]
    #[Security(name: 'Bearer')]
    public function index(Request $request): JsonResponse
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $tasks = $this->taskRepository->getTasksPaginator($offset);

        return $this->apiResponse($tasks);
    }

    #[Route('/api/tasks/{id}', name: 'update_task', methods: ['PATCH'])]
    #[OA\Tag(name: 'tasks')]
    #[OA\RequestBody(
        content: new Model(type: UpdateTaskRequestDTO::class)
    )]
    public function updateTask(#[MapRequestPayload] UpdateTaskRequestDTO $request, int $id): JsonResponse
    {
        try {
            $task = $this->entityManager->getRepository(Task::class)->find($id);

            if (!$task) {
                throw new EntityNotFoundException('No task found.');
            }

            $task->setTitle($request->title);
            $task->setStatus($request->newStatus);

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->apiResponse($task);
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);

            return $this->apiResponseError($throwable);
        }
    }

    #[Route('/api/tasks/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'tasks')]
    public function getById(int $id): JsonResponse
    {
        try {
            $task = $this->taskRepository->find($id);

            if (!$task) {
                throw new EntityNotFoundException('No task found.');
            }

            return $this->apiResponse($task);
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);

            return $this->apiResponseError($throwable);
        }
    }

    #[Route('/api/tasks', name: 'create_task', methods: ['POST'])]
    #[OA\RequestBody(
        content: new Model(type: CreateTaskRequestDTO::class)
    )]
    #[OA\Tag(name: 'tasks')]
    public function createTask(#[MapRequestPayload] CreateTaskRequestDTO $request): JsonResponse
    {
        try {
            $task = new Task();

            $task->setTitle($request->title);
            $task->setStatus(TaskStatusEnum::New);
            $task->setCreatedAt(\DateTimeImmutable::createFromMutable(new DateTime()));

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->apiResponse(['id' => $task->getId()], Response::HTTP_CREATED);
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);

            return $this->apiResponseError($throwable);
        }
    }

    #[Route('/api/tasks/{id}/delete', name: 'delete_task', methods: ['DELETE'])]
    #[OA\Tag(name: 'tasks')]
    public function deleteTask(int $id): JsonResponse
    {
        try {
            $this->taskRepository->deleteTask($id);

            return $this->apiResponse([], Response::HTTP_NO_CONTENT);
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);

            return $this->apiResponseError($throwable);
        }
    }
}
