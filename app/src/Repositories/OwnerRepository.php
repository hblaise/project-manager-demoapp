<?php

namespace App\Repositories;

use App\Contracts\IOwnerRepository;
use App\Entities\Owner;
use PDO;

class OwnerRepository extends Repository implements IOwnerRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get a list of owners
     *
     * @return Owner[]
     */
    public function getProjectOwners(): array
    {
        $sql = "SELECT * FROM owners";

        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS, Owner::class);
    }

    /**
     * Get owner by name and email
     *
     * @param string $name
     * @param string $email
     * @return Owner|null
     */
    public function getProjectOwnerByParams(string $name, string $email): ?Owner
    {
        $sql = "SELECT * FROM owners WHERE name = :name AND email = :email";

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':name', $name);
        $statement->bindValue(':email', $email);
        $statement->setFetchMode(PDO::FETCH_CLASS, Owner::class);
        $statement->execute();
        $result = $statement->fetch();

        return $result ? $result : null;
    }

    /**
     * Get project owner by id
     *
     * @param integer $id
     * @return Owner|null
     */
    public function getProjectOwnerById(int $id): ?Owner
    {
        $sql = "SELECT * FROM owners WHERE id = :id";

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->setFetchMode(PDO::FETCH_CLASS, Owner::class);
        $statement->execute();
        $result = $statement->fetch();

        return $result ? $result : null;
    }

    /**
     * Create new project owner
     *
     * @param Owner $owner
     * @return integer|null
     */
    public function createProjectOwner(Owner $owner): ?int
    {
        $sql = "INSERT INTO owners (name, email) VALUES (:name, :email)";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':name', $owner->name);
        $statement->bindValue(':email', $owner->email);
        $statement->execute();

        return $this->pdo->lastInsertId() ? (int)$this->pdo->lastInsertId() : null;
    }
}
