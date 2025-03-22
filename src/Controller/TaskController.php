<?php

namespace App\Controller;

use App\Contracts\IResponseHelper;
use App\DTO\Task\UpdateTaskDTO;
use App\Entity\Task;
use App\Enum\TaskStatusEnum;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class TaskController extends AbstractController
{
    public function __construct(
        private readonly IResponseHelper $responseHelper,
        private readonly TaskRepository $taskRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    #[Route('/api/tasks', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of tasks'
    )]
    #[OA\Tag(name: 'tasks')]
    public function index(): Response
    {
        $tasks = $this->taskRepository->getAllTasks();

        return $this->responseHelper->json($tasks);
    }

    #[Route('/api/tasks/{id}', name: 'update_task', methods: ['PATCH'])]
    #[OA\Tag(name: 'tasks')]
    #[OA\RequestBody(
        content: new Model(type: UpdateTaskDTO::class)
    )]
    public function updateTask(Request $request, int $id): Response
    {
        $data = json_decode($request->getContent(), true);

        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            throw $this->createNotFoundException('No task found.');
        }

        $task->setTitle($data['title']);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

//        $task->setTitle();

        return $this->responseHelper->json($task);
    }

    #[Route('/api/tasks/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'tasks')]
    public function getById(int $id): Response
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('No task found.');
        }

        return $this->responseHelper->json($task);
    }

    #[Route('/api/tasks', name: 'create_task', methods: ['POST'])]
    #[OA\Tag(name: 'tasks')]
    public function createTask(Request $request): Response
    {
        $task = new Task();

        $task->setTitle('Task 1');
        $task->setStatus(TaskStatusEnum::New);
        $task->setCreatedAt(\DateTimeImmutable::createFromMutable(new DateTime()));

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->responseHelper->json($task, Response::HTTP_CREATED);
    }

    #[Route('/api/tasks/{id}/delete', name: 'delete_task', methods: ['DELETE'])]
    #[OA\Tag(name: 'tasks')]
    public function deleteTask(int $id): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            throw $this->createNotFoundException('No task found.');
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->responseHelper->json([], Response::HTTP_NO_CONTENT);
    }
}
