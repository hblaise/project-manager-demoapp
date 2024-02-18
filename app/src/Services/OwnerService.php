<?php

namespace App\Services;

use App\Repositories\OwnerRepository;
use App\Entities\Owner;
use App\Contracts\IOwnerService;
use App\Contracts\IOwnerRepository;
use App\Dtos\OwnerDto;

class OwnerService implements IOwnerService
{
    private IOwnerRepository $ownerRepository;

    public function __construct()
    {
        $this->ownerRepository = new OwnerRepository();
    }

    /**
     * Get all project owners
     *
     * @return array
     */
    public function getProjectOwners(): array
    {
        return $this->ownerRepository->getProjectOwners();
    }

    /**
     * Get a project owner by name and email
     *
     * @param string $name
     * @param string $email
     * @return Owner|null
     */
    public function getProjectOwnerByParams(string $name, string $email): ?Owner
    {
        return $this->ownerRepository->getProjectOwnerByParams($name, $email);
    }

    /**
     * Get project owner by id
     *
     * @param integer $id
     * @return Owner|null
     */
    public function getProjectOwnerById(int $id): ?Owner
    {
        return $this->ownerRepository->getProjectOwnerById($id);
    }

    /**
     * Create project owner
     *
     * @param OwnerDto $ownerDto
     * @return integer|null
     */
    public function createProjectOwner(OwnerDto $ownerDto): ?int
    {
        $owner = new Owner();
        $owner->name = $ownerDto->name;
        $owner->email = $ownerDto->email;

        return $this->ownerRepository->createProjectOwner($owner);
    }
}
