<?php

namespace App\Controller;

use App\Contracts\IResponseHelper;
use App\Entity\Task;
use App\Enum\TaskStatusEnum;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    public function __construct(
        private readonly IResponseHelper $responseHelper,
        private readonly TaskRepository $taskRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    #[Route('/tasks', name: 'app_task')]
    public function index(): Response
    {
        $tasks = $this->taskRepository->getAllTasks();

        return $this->responseHelper->json($tasks);
    }

    #[Route('/tasks/{id}', name: 'app_task')]
    public function getById(int $id): Response
    {
        $task = $this->taskRepository->find($id);

        return $this->responseHelper->json($task);
    }

    #[Route('/tasks', name: 'create_task', methods: ['POST'])]
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

    #[Route('/tasks/{id}/delete', name: 'create_task', methods: ['DELETE'])]
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
