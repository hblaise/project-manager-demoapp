<?php

namespace App\Contracts;

use App\Entities\Status;

interface IStatusService
{
    public function getProjectStatuses(): array;
    public function getProjectStatusById(int $id): ?Status;
}
