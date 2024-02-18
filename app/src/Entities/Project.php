<?php

namespace App\Entities;

class Project
{
    public int $id;
    public string $title;
    public string $description;
    public ?int $statusId;
    public ?string $statusKey;
    public ?string $statusName;
    public ?int $ownerId;
    public ?string $ownerName;
    public ?string $ownerEmail;
}
