<?php

namespace App\Repositories;

use App\Contracts\IProjectRepository;
use App\Common\OrderEnum as Order;
use App\Entities\Project;
use PDO;

class ProjectRepository extends Repository implements IProjectRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get a filterable, orderable, paginable list of projects
     *
     * @param string|null $statusKey
     * @param Order $order
     * @param integer|null $limit
     * @param integer|null $offset
     * @return Project[]
     */
    public function getProjects(?string $statusKey = null, Order $order = Order::ASC, ?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT
                p.*,
                o.name AS ownerName,
                o.email AS ownerEmail,
                s.name AS statusName,
                s.key AS statusKey
            FROM projects p
            LEFT JOIN project_owner_pivot pop ON p.id = pop.project_id
            LEFT JOIN owners o ON pop.owner_id = o.id
            LEFT JOIN project_status_pivot psp ON p.id = psp.project_id
            LEFT JOIN statuses s ON psp.status_id = s.id";

        if ($statusKey !== null) {
            $sql .= " WHERE s.key = :statusKey";
        }

        $sql .= " ORDER BY id {$order->value}";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        $statement = $this->pdo->prepare($sql);

        if ($statusKey !== null) {
            $statement->bindValue(':statusKey', $statusKey);
        }

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, Project::class);
    }

    /**
     * Get a project by id
     *
     * @param integer $id
     * @return Project|null
     */
    public function getProjectById(int $id): ?Project
    {
        $sql = "SELECT
                p.*,
                o.id AS ownerId,
                o.name AS ownerName,
                o.email AS ownerEmail,
                s.id AS statusId,
                s.name AS statusName,
                s.key AS statusKey
            FROM projects p
            LEFT JOIN project_owner_pivot pop ON p.id = pop.project_id
            LEFT JOIN owners o ON pop.owner_id = o.id
            LEFT JOIN project_status_pivot psp ON p.id = psp.project_id
            LEFT JOIN statuses s ON psp.status_id = s.id
            WHERE p.id = :id";

        $statement = $this->pdo->prepare($sql);

        $statement->bindValue(':id', $id);
        $statement->setFetchMode(PDO::FETCH_CLASS, Project::class);
        $statement->execute();
        $result = $statement->fetch();

        return $result ? $result : null;
    }

    /**
     * Get the count of projects with optional status filter
     *
     * @param string|null $statusKey
     * @return integer
     */
    public function getProjectCount(?string $statusKey = null): int
    {
        $sql = "SELECT
                    COUNT(*)
                FROM projects p
                LEFT JOIN project_status_pivot psp ON p.id = psp.project_id
                LEFT JOIN statuses s ON psp.status_id = s.id";

        if ($statusKey !== null) {
            $sql .= " WHERE s.key = :statusKey";
        }

        $statement = $this->pdo->prepare($sql);

        if ($statusKey !== null) {
            $statement->bindValue(':statusKey', $statusKey);
        }

        $statement->execute();

        return $statement->fetchColumn();
    }

    /**
     * Create a new project
     *
     * @param Project $project
     * @return integer|null
     */
    public function createProject(Project $project): ?int
    {
        $sql = "INSERT INTO projects (title, description) VALUES (:title, :description)";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':title', $project->title);
        $statement->bindValue(':description', $project->description);
        $statement->execute();

        return $this->pdo->lastInsertId() ? (int)$this->pdo->lastInsertId() : null;
    }

    /**
     * Create connection between project and status
     *
     * @param integer $projectId
     * @param integer $statusId
     * @return boolean
     */
    public function createProjectStatusConnection(int $projectId, int $statusId): bool
    {
        $sql = "INSERT INTO project_status_pivot (project_id, status_id) VALUES (:projectId, :statusId)";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':projectId', $projectId);
        $statement->bindValue(':statusId', $statusId);
        $result = $statement->execute();

        return $result && $statement->rowCount() > 0;
    }

    /**
     * Create connection between project and owner
     *
     * @param integer $projectId
     * @param integer $ownerId
     * @return boolean
     */
    public function createProjectOwnerConnection(int $projectId, int $ownerId): bool
    {
        $sql = "INSERT INTO project_owner_pivot (project_id, owner_id) VALUES (:projectId, :ownerId)";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':projectId', $projectId);
        $statement->bindValue(':ownerId', $ownerId);
        $result = $statement->execute();

        return $result && $statement->rowCount() > 0;
    }

    /**
     * Update a project
     *
     * @param Project $project
     * @return boolean
     */
    public function updateProject(Project $project): bool
    {
        $sql = "UPDATE projects SET title = :title, description = :description WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':title', $project->title);
        $statement->bindValue(':description', $project->description);
        $statement->bindValue(':id', $project->id);

        return $statement->execute();
    }

    /**
     * Update a project's status connection
     *
     * @param integer $projectId
     * @param integer $statusId
     * @return boolean
     */
    public function updateProjectStatusConnection(int $projectId, int $statusId): bool
    {
        $sql = "UPDATE project_status_pivot SET status_id = :statusId WHERE project_id = :projectId";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':statusId', $statusId);
        $statement->bindValue(':projectId', $projectId);

        return $statement->execute();
    }

    /**
     * Update a project's owner connection
     *
     * @param integer $projectId
     * @param integer $ownerId
     * @return boolean
     */
    public function updateProjectOwnerConnection(int $projectId, int $ownerId): bool
    {
        $sql = "UPDATE project_owner_pivot SET owner_id = :ownerId WHERE project_id = :projectId";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':ownerId', $ownerId);
        $statement->bindValue(':projectId', $projectId);

        return $statement->execute();
    }

    /**
     * Delete a project by id
     *
     * @param integer $id
     * @return boolean
     */
    public function deleteProject(int $id): bool
    {
        $sql = "DELETE FROM projects WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id);
        $result = $statement->execute();

        return $result && $statement->rowCount() > 0;
    }

    /**
     * Delete a project's status connection
     *
     * @param integer $projectId
     * @return boolean
     */
    public function deleteProjectStatusConnection(int $projectId): bool
    {
        $sql = "DELETE FROM project_status_pivot WHERE project_id = :projectId";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':projectId', $projectId);
        $result = $statement->execute();

        return $result && $statement->rowCount() > 0;
    }

    /**
     * Delete a project's owner connection
     *
     * @param integer $projectId
     * @return boolean
     */
    public function deleteProjectOwnerConnection(int $projectId): bool
    {
        $sql = "DELETE FROM project_owner_pivot WHERE project_id = :projectId";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':projectId', $projectId);
        $result = $statement->execute();

        return $result && $statement->rowCount() > 0;
    }
}
