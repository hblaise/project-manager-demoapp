<?php

namespace App\Contracts;

use App\Entities\Owner;
use App\Dtos\OwnerDto;

interface IOwnerService
{
    public function getProjectOwners(): array;
    public function getProjectOwnerByParams(string $name, string $email): ?Owner;
    public function getProjectOwnerById(int $id): ?Owner;
    public function createProjectOwner(OwnerDto $ownerDto): ?int;
}
