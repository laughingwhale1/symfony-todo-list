<?php

namespace App\Controller;

use App\Contracts\IResponseHelper;
use App\DTO\Task\UpdateTaskDTO;
use App\Entity\Task;
use App\Enum\TaskStatusEnum;
use App\Exceptions\EntityNotFoundException;
use App\Helpers\ResponseHelper;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
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
    #[OA\Tag(name: 'tasks')]
    public function index(): Response
    {
        $tasks = $this->taskRepository->getAllTasks();

        return $this->apiResponse($tasks);
    }

    #[Route('/api/tasks/{id}', name: 'update_task', methods: ['PATCH'])]
    #[OA\Tag(name: 'tasks')]
    #[OA\RequestBody(
        content: new Model(type: UpdateTaskDTO::class)
    )]
    public function updateTask(Request $request, int $id): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $task = $this->entityManager->getRepository(Task::class)->find($id);

            if (!$task) {
                throw new EntityNotFoundException('No task found.');
            }

            $task->setTitle($data['title']);

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
    public function getById(int $id): Response
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
    #[OA\Tag(name: 'tasks')]
    public function createTask(Request $request): Response
    {
        try {
            $task = new Task();

            $task->setTitle('Task 1');
            $task->setStatus(TaskStatusEnum::New);
            $task->setCreatedAt(\DateTimeImmutable::createFromMutable(new DateTime()));

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->apiResponse($task, Response::HTTP_CREATED);
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);

            return $this->apiResponseError($throwable);
        }
    }

    #[Route('/api/tasks/{id}/delete', name: 'delete_task', methods: ['DELETE'])]
    #[OA\Tag(name: 'tasks')]
    public function deleteTask(int $id): Response
    {
        try {
            $task = $this->entityManager->getRepository(Task::class)->find($id);

            if (!$task) {
                throw new EntityNotFoundException();
            }

            $this->entityManager->remove($task);
            $this->entityManager->flush();

            return $this->apiResponse([], Response::HTTP_NO_CONTENT);
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);

            return $this->apiResponseError($throwable);
        }
    }
}
