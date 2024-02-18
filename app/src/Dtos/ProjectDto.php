<?php

namespace App\Dtos;

class ProjectDto
{
    public ?int $id;
    public string $title;
    public string $description;
    public int $statusId;
    public ?int $ownerId;
    public ?string $ownerName;
    public ?string $ownerEmail;
}
