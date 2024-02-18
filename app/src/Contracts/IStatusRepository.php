<?php

namespace App\Contracts;

use App\Entities\Status;

interface IStatusRepository extends IRepository
{
    public function getProjectStatuses(): array;
    public function getProjectStatusById(int $id): ?Status;
}
