<?php

namespace App\DTO\Task;

use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatedResponseDTO
{
    public function __construct(
        public Paginator $data,
        public int $totalCount
    )
    {

    }
}