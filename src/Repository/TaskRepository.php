<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getAllTasks(): array
    {
        return $this->createQueryBuilder('t')
            ->getQuery()
            ->getArrayResult();
    }

    public function deleteTask()
    {
        return $this->createQueryBuilder()
            ->where('');
    }
}
