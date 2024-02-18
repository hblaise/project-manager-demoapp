<?php

namespace App\Services;

use App\Contracts\IStatusService;
use App\Repositories\StatusRepository;
use App\Contracts\IStatusRepository;
use App\Entities\Status;

class StatusService implements IStatusService
{
    private IStatusRepository $statusRepository;

    public function __construct()
    {
        $this->statusRepository = new StatusRepository();
    }

    public function getProjectStatuses(): array
    {
        return $this->statusRepository->getProjectStatuses();
    }

    public function getProjectStatusById(int $id): ?Status
    {
        return $this->statusRepository->getProjectStatusById($id);
    }
}
