<?php

namespace App\Contracts;

use App\Common\OrderEnum;
use App\Entities\Project;
use App\Dtos\ProjectDto;
use App\Common\Result;

interface IProjectService
{
    public function getProjectList(int $page = 1, int $pageSize = 10, ?string $status = null, OrderEnum $order = OrderEnum::ASC): array;
    public function getProjectById(int $id): ?Project;
    public function storeProject(ProjectDto $projectDto): Result;
    public function updateProject(ProjectDto $projectDto): Result;
    public function deleteProject(int $id): Result;
    public function getProjectCount(?string $statusKey = null): int;
}
