<?php

namespace App\Contracts;

use App\Entities\Owner;

interface IOwnerRepository extends IRepository
{
    public function getProjectOwners(): array;
    public function getProjectOwnerByParams(string $name, string $email): ?Owner;
    public function getProjectOwnerById(int $id): ?Owner;
    public function createProjectOwner(Owner $owner): ?int;
}
