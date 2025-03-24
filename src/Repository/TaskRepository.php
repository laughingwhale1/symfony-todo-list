<?php

namespace App\Repository;

use App\DTO\Task\PaginatedResponseDTO;
use App\Entity\Task;
use App\Enum\TaskStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepositoryProxy<Task>
 */
class TaskRepository extends ServiceEntityRepositoryProxy
{
    public const TASKS_PER_PAGE = 5;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getTasksPaginator(int $offset): PaginatedResponseDTO
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.status = :new')
            ->setParameter('new', TaskStatusEnum::New)
            ->orderBy('t.created_at', 'DESC')
            ->setMaxResults(self::TASKS_PER_PAGE)
            ->setFirstResult($offset * self::TASKS_PER_PAGE)
            ->getQuery();

        $totalCount = $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginatedResponseDTO(new Paginator($qb), $totalCount);
    }

    public function getAllTasks(): array
    {
        return $this->createQueryBuilder('t')
            ->getQuery()
            ->getArrayResult();
    }

    public function deleteTask(int $id): void
    {
        $this->createQueryBuilder('t')
            ->where('t.id = :id')
            ->setParameter('id', 1)
            ->delete('Task', 'u');
    }
}
