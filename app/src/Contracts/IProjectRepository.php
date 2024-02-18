<?php

namespace App\Contracts;

use App\Common\OrderEnum as Order;
use App\Entities\Project;

interface IProjectRepository extends IRepository
{
    public function getProjects(?string $statusKey = null, Order $order = Order::ASC, ?int $limit = null, ?int $offset = null): array;
    public function getProjectById(int $id): ?Project;
    public function getProjectCount(?string $statusKey = null): int;
    public function createProject(Project $project): ?int;
    public function createProjectStatusConnection(int $projectId, int $statusId): bool;
    public function createProjectOwnerConnection(int $projectId, int $ownerId): bool;
    public function updateProject(Project $project): bool;
    public function updateProjectStatusConnection(int $projectId, int $statusId): bool;
    public function updateProjectOwnerConnection(int $projectId, int $ownerId): bool;
    public function deleteProject(int $id): bool;
    public function deleteProjectStatusConnection(int $projectId): bool;
    public function deleteProjectOwnerConnection(int $projectId): bool;
}
