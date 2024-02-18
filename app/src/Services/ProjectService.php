<?php

namespace App\Services;

use App\Common\EmailConfig;
use App\Common\OrderEnum as Order;
use App\Contracts\IProjectService;
use App\Dtos\ProjectDto;
use App\Repositories\ProjectRepository;
use App\Contracts\IProjectRepository;
use App\Entities\Project;
use App\Dtos\OwnerDto;
use App\Common\Result;
use Throwable;
use App\Contracts\ILogger;
use App\Common\Logger;
use Exception;
use App\Contracts\IOwnerService;
use App\Contracts\IStatusService;

class ProjectService implements IProjectService
{
    private IProjectRepository $projectRepository;
    private IOwnerService $ownerService;
    private IStatusService $statusService;
    private ILogger $logger;

    public function __construct(IStatusService $statusService, IOwnerService $ownerService)
    {
        $this->projectRepository = new ProjectRepository();
        $this->ownerService = $ownerService;
        $this->statusService = $statusService;
        $this->logger = new Logger();
    }

    /**
     * List projects with pagination
     *
     * @param integer $page
     * @param integer $pageSize
     * @param string|null $status
     * @param Order $order
     * @return array
     */
    public function getProjectList(int $page = 1, int $pageSize = 10, ?string $status = null, Order $order = Order::ASC): array
    {
        $offset = ($page - 1) * $pageSize;
        return $this->projectRepository->getProjects($status, $order, $pageSize, $offset);
    }

    /**
     * Get project by id
     *
     * @param integer $id
     * @return Project|null
     */
    public function getProjectById(int $id): ?Project
    {
        return $this->projectRepository->getProjectById($id);
    }

    /**
     * Store new project
     *
     * @param ProjectDto $projectDto
     * @return Result
     */
    public function storeProject(ProjectDto $projectDto): Result
    {
        $result = new Result();

        try {
            if (empty($projectDto->title)) {
                $result->message = "Hiányzó projekt cím!";
                return $result;
            }

            if (empty($projectDto->description)) {
                $result->message = "Hiányzó projekt leírás!";
                return $result;
            }

            $status = $this->statusService->getProjectStatusById($projectDto->statusId);
            if ($status === null) {
                $result->message = "Nem létező projekt státusz!";
                return $result;
            }

            if ($projectDto->ownerId === null) {
                // Creating new owner
                if (empty($projectDto->ownerName) || empty($projectDto->ownerEmail)) {
                    $result->message = "Hiányzó projekt tulajdonos adatok!";
                    return $result;
                }

                $owner = new OwnerDto();
                $owner->name = $projectDto->ownerName;
                $owner->email = $projectDto->ownerEmail;
                $projectDto->ownerId = $this->ownerService->createProjectOwner($owner);

                if ($projectDto->ownerId === null) {
                    $result->message = "Hiba az új projekt tulajdonos létrehozásakor!";
                    return $result;
                }
            } else {
                // Using existing owner
                $existingOwner = $this->ownerService->getProjectOwnerById($projectDto->ownerId);

                if ($existingOwner === null) {
                    $result->message = "Nem létező projekt tulajdonos!";
                    return $result;
                }
            }

            $this->projectRepository->getPdo()->beginTransaction();

            $project = new Project();
            $project->title = $projectDto->title;
            $project->description = $projectDto->description;
            $projectRes = $this->projectRepository->createProject($project);

            if ($projectRes === null) {
                throw new Exception("Hiba történt a projekt létrehozása közben!");
            }

            $statusRes = $this->projectRepository->createProjectStatusConnection($projectRes, $projectDto->statusId);

            if (!$statusRes) {
                throw new Exception("Hiba történt a projekt státusz kapcsolat létrehozása közben!");
            }

            $ownerRes = $this->projectRepository->createProjectOwnerConnection($projectRes, $projectDto->ownerId);

            if (!$ownerRes) {
                throw new Exception("Hiba történt a projekt tulajdonos kapcsolat létrehozása közben!");
            }

            $this->projectRepository->getPdo()->commit();
            $result->success = true;
            $result->message = "A projekt sikeresen létrehozva!";
        } catch (Throwable $t) {
            $this->projectRepository->getPdo()->rollBack();
            $this->logger->error($t->getMessage() . " " . $t->getTraceAsString());
            $result->message = "Hiba történt a projekt létrehozása közben!";
        }

        return $result;
    }

    /**
     * Update project
     *
     * @param ProjectDto $projectDto
     * @return Result
     */
    public function updateProject(ProjectDto $projectDto): Result
    {
        $result = new Result();

        try {
            if ($projectDto->id === null) {
                $result->message = "Hiányzó projekt azonosító!";
                return $result;
            }

            if (empty($projectDto->title)) {
                $result->message = "Hiányzó projekt cím!";
                return $result;
            }

            if (empty($projectDto->description)) {
                $result->message = "Hiányzó projekt leírás!";
                return $result;
            }

            $status = $this->statusService->getProjectStatusById($projectDto->statusId);
            if ($status === null) {
                $result->message = "Nem létező projekt státusz!";
                return $result;
            }

            if ($projectDto->ownerId === null) {
                // Creating new owner
                if (empty($projectDto->ownerName) || empty($projectDto->ownerEmail)) {
                    $result->message = "Hiányzó projekt tulajdonos adatok!";
                    return $result;
                }

                $owner = new OwnerDto();
                $owner->name = $projectDto->ownerName;
                $owner->email = $projectDto->ownerEmail;
                $projectDto->ownerId = $this->ownerService->createProjectOwner($owner);

                if ($projectDto->ownerId === null) {
                    $result->message = "Hiba az új projekt tulajdonos létrehozásakor!";
                    return $result;
                }
            } else {
                // Using existing owner
                $existingOwner = $this->ownerService->getProjectOwnerById($projectDto->ownerId);

                if ($existingOwner === null) {
                    $result->message = "Nem létező projekt tulajdonos!";
                    return $result;
                }
            }

            $this->projectRepository->getPdo()->beginTransaction();

            $existingProject = $this->projectRepository->getProjectById($projectDto->id);
            $changedProperties = $this->getChangedProperties($existingProject, $projectDto);
            if (empty($changedProperties)) {
                $this->projectRepository->getPdo()->rollBack();
                $result->message = "Nincs változás a projekt adataiban!";
                $result->success = true;
                return $result;
            }

            if (array_key_exists('title', $changedProperties) || array_key_exists('description', $changedProperties)) {
                $project = new Project();
                $project->id = $projectDto->id;
                $project->title = $projectDto->title;
                $project->description = $projectDto->description;
                $projectRes = $this->projectRepository->updateProject($project);

                if (!$projectRes) {
                    throw new Exception("Hiba történt a projekt módosítása közben!");
                }
            }

            if (array_key_exists('statusId', $changedProperties)) {
                $statusRes = $this->projectRepository->updateProjectStatusConnection($projectDto->id, $projectDto->statusId);

                if (!$statusRes) {
                    throw new Exception("Hiba történt a projekt státusz kapcsolat módosítása közben!");
                }
            }

            if (array_key_exists('ownerId', $changedProperties)) {
                $ownerRes = $this->projectRepository->updateProjectOwnerConnection($projectDto->id, $projectDto->ownerId);

                if (!$ownerRes) {
                    throw new Exception("Hiba történt a projekt tulajdonos kapcsolat módosítása közben!");
                }
            }

            $updatedProject = $this->projectRepository->getProjectById($projectDto->id);
            // FIXME: docker containerben nem működik az e-mail küldés mailhoggal
            $this->sendProjectUpdateNotificationEmail($updatedProject, $changedProperties);
            $this->projectRepository->getPdo()->commit();
            $result->success = true;
            $result->message = "A projekt sikeresen módosítva!";
        } catch (Throwable $t) {
            $this->projectRepository->getPdo()->rollBack();
            $this->logger->error($t->getMessage() . " " . $t->getTraceAsString());
            $result->message = "Hiba történt a projekt módosítása közben!";
        }

        return $result;
    }

    private function getChangedProperties(Project $project, ProjectDto $projectDto): array
    {
        $changedProperties = [];

        if ($project->title !== $projectDto->title) {
            $changedProperties['title'] = $projectDto->title;
        }

        if ($project->description !== $projectDto->description) {
            $changedProperties['description'] = $projectDto->description;
        }

        if ($project->statusId !== $projectDto->statusId) {
            $changedProperties['statusId'] = $projectDto->statusId;
        }

        if ($project->ownerId !== $projectDto->ownerId) {
            $changedProperties['ownerId'] = $projectDto->ownerId;
        }

        return $changedProperties;
    }

    private function sendProjectUpdateNotificationEmail(Project $project, array $changedProperties): void
    {
        $senderAddress = EmailConfig::getNotificationSenderAddress();
        $senderName = EmailConfig::getNotificationSenderName();

        $to = EmailConfig::getNotificationRecipientAddress();
        $subject = "Project #{$project->id} frissítve!";
        $headers = "From: {$senderName} <{$senderAddress}>";

        $message = "A #{$project->id} projekt következő adatai frissültek:\n";
        foreach ($changedProperties as $key => $value) {
            switch ($key) {
                case 'title':
                    $message .= "Cím: {$project->title}\n";
                    break;
                case 'description':
                    $message .= "Leírás: {$project->description}\n";
                    break;
                case 'statusId':
                    $message .= "Státusz: {$project->statusName}\n";
                    break;
                case 'ownerId':
                    $message .= "Tulajdonos: {$project->ownerName} ({$project->ownerEmail})\n";
                    break;
            }
        }

        if (!mail($to, $subject, $message, $headers)) {
            $this->logger->error("Hiba történt az értesítő email küldése közben!");
        }
    }

    /**
     * Delete project by id
     *
     * @param integer $id
     * @return Result
     */
    public function deleteProject(int $id): Result
    {
        $result = new Result();

        try {
            $project = $this->projectRepository->getProjectById($id);

            if ($project === null) {
                $result->message = "Nem létező projekt!";
                return $result;
            }

            $this->projectRepository->getPDO()->beginTransaction();

            $deleteProjectOwnerConnectionResult = $this->projectRepository->deleteProjectOwnerConnection($id);
            if (!$deleteProjectOwnerConnectionResult) {
                throw new Exception("Hiba történt a projekt tulajdonos kapcsolat törlése közben!");
            }

            $deleteProjectStatusConnectionResult = $this->projectRepository->deleteProjectStatusConnection($id);
            if (!$deleteProjectStatusConnectionResult) {
                throw new Exception("Hiba történt a projekt státusz kapcsolat törlése közben!");
            }

            $deleteResult = $this->projectRepository->deleteProject($id);

            if (!$deleteResult) {
                throw new Exception("Hiba történt a projekt törlése közben!");
            }

            $this->projectRepository->getPDO()->commit();
            $result->success = true;
            $result->message = "A projekt sikeresen törölve!";
        } catch (Throwable $t) {
            $this->projectRepository->getPDO()->rollBack();
            $this->logger->error($t->getMessage() . " " . $t->getTraceAsString());
            $result->message = "Hiba történt a projekt törlése közben!";
        }

        return $result;
    }

    /**
     * Get the total number of projects
     *
     * @param string|null $statusKey
     * @return integer
     */
    public function getProjectCount(?string $statusKey = null): int
    {
        return $this->projectRepository->getProjectCount($statusKey);
    }
}
