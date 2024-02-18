<?php

namespace App\Repositories;

use App\Contracts\IStatusRepository;
use App\Entities\Status;
use PDO;

class StatusRepository extends Repository implements IStatusRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get project status list
     *
     * @return Status[]
     */
    public function getProjectStatuses(): array
    {
        $sql = "SELECT * FROM statuses";

        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS, Status::class);
    }

    /**
     * Get project status by id
     *
     * @param integer $id
     * @return Status|null
     */
    public function getProjectStatusById(int $id): ?Status
    {
        $sql = "SELECT * FROM statuses WHERE id = :id";

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->setFetchMode(PDO::FETCH_CLASS, Status::class);
        $statement->execute();
        $result = $statement->fetch();

        return $result ? $result : null;
    }
}
