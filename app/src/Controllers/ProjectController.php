<?php

namespace App\Controllers;

use App\Contracts\ILogger;
use App\Contracts\IProjectService;
use App\Contracts\IStatusService;
use App\Contracts\IOwnerService;
use App\Common\View;
use App\Common\HttpMethod;
use App\Common\JsonResponse;
use App\Services\ProjectService;
use App\Common\Request;
use App\Common\Logger;
use App\Dtos\ProjectDto;
use App\Services\StatusService;
use App\Services\OwnerService;
use App\Common\Result;

class ProjectController extends BaseController
{
    private IProjectService $projectService;
    private IStatusService $statusService;
    private IOwnerService $ownerService;
    private ILogger $logger;

    public function __construct()
    {
        $this->statusService = new StatusService();
        $this->ownerService = new OwnerService();
        $this->projectService = new ProjectService($this->statusService, $this->ownerService);
        $this->logger = new Logger();
    }

    #[HttpMethod('GET')]
    public function index(int $page = 1, ?string $status = null): View
    {
        $pageSize = 10;
        $projectList = $this->projectService->getProjectList($page, $pageSize, $status);
        $statuses = $this->statusService->getProjectStatuses();
        $projectCount = $this->projectService->getProjectCount($status);
        $totalPages = ceil($projectCount / $pageSize);

        if (empty($statuses)) {
            $this->logger->error("Nem találhatóak a projekt státuszok!");
            return new View("500");
        }

        return new View("list", ['projects' => $projectList, 'statuses' => $statuses, 'totalPages' => $totalPages, 'projectCount' => $projectCount]);
    }

    #[HttpMethod('GET')]
    public function show(int $id): View
    {
        $project = $this->projectService->getProjectById($id);

        if (!$project) {
            return new View("404");
        }

        return new View("show", ['project' => $project]);
    }

    #[HttpMethod('GET')]
    public function create(): View
    {
        $statuses = $this->statusService->getProjectStatuses();
        $owners = $this->ownerService->getProjectOwners();

        if (empty($statuses)) {
            $this->logger->error("Nem találhatóak a projekt státuszok!");
            return new View("500");
        }

        return new View("create_or_edit", ['statuses' => $statuses, 'owners' => $owners]);
    }

    #[HttpMethod('POST')]
    public function store(Request $request): View
    {
        $errors = [];
        $formData = $request->post();
        $statuses = $this->statusService->getProjectStatuses();
        $owners = $this->ownerService->getProjectOwners();

        if (empty($formData)) {
            $errors[] = "Nem érkezett adat a mentéshez!";
            return new View("create_or_edit", ['errors' => $errors]);
        }

        $errors = $this->validateFormData($formData, $errors);
        if (!empty($errors)) {
            return new View("create_or_edit", ['formData' => $formData, 'errors' => $errors, 'statuses' => $statuses, 'owners' => $owners]);
        }

        $projectDto = new ProjectDto();
        $projectDto->title = $formData['projekt_cim'];
        $projectDto->description = $formData['projekt_leiras'];
        $projectDto->statusId = (int)$formData['projekt_statusz'];

        if ($formData['projekt_owner'] === "new") {
            $projectDto->ownerId = null;
        } else {
            $projectDto->ownerId = (int)$formData['projekt_owner'];
        }

        $projectDto->ownerName = $formData['projekt_kapcsolattarto_neve'] ?? null;
        $projectDto->ownerEmail = $formData['projekt_kapcsolattarto_email'] ?? null;

        $storeResult = $this->projectService->storeProject($projectDto);

        if (!$storeResult->success) {
            $errors[] = $storeResult->message;
            return new View("create_or_edit", ['formData' => $formData, 'errors' => $errors, 'statuses' => $statuses, 'owners' => $owners]);
        }

        return new View("create_or_edit", ['formData' => null, 'statuses' => $statuses, 'owners' => $owners, 'successMessage' => 'A projekt sikeresen elmentve!']);
    }

    #[HttpMethod('GET')]
    public function edit(int $id): View
    {
        $project = $this->projectService->getProjectById($id);
        $statuses = $this->statusService->getProjectStatuses();
        $owners = $this->ownerService->getProjectOwners();

        if (!$project) {
            return new View("404");
        }

        if (empty($statuses)) {
            $this->logger->error("Nem találhatóak a projekt státuszok!");
            return new View("500");
        }

        return new View("create_or_edit", ['project' => $project, 'statuses' => $statuses, 'owners' => $owners]);
    }

    #[HttpMethod('POST')]
    public function update(Request $request, int $id): View
    {
        $errors = [];
        $formData = $request->post();
        $project = $this->projectService->getProjectById($id);
        $statuses = $this->statusService->getProjectStatuses();
        $owners = $this->ownerService->getProjectOwners();

        if (!$project) {
            $this->logger->error("Nem sikerült frissíteni a projektet, nem található projekt ezzel az azonosítóval: {$id}!");
            return new View("500");
        }

        if (empty($formData)) {
            $errors[] = "Nem érkezett adat a mentéshez!";
            return new View("create_or_edit", ['errors' => $errors]);
        }

        $errors = $this->validateFormData($formData, $errors);
        if (!empty($errors)) {
            return new View("create_or_edit", ['formData' => $formData, 'project' => $project, 'errors' => $errors, 'statuses' => $statuses, 'owners' => $owners]);
        }

        $projectDto = new ProjectDto();
        $projectDto->id = $id;
        $projectDto->title = $formData['projekt_cim'];
        $projectDto->description = $formData['projekt_leiras'];
        $projectDto->statusId = (int)$formData['projekt_statusz'];

        if ($formData['projekt_owner'] === "new") {
            $projectDto->ownerId = null;
        } else {
            $projectDto->ownerId = (int)$formData['projekt_owner'];
        }

        $projectDto->ownerName = $formData['projekt_kapcsolattarto_neve'] ?? null;
        $projectDto->ownerEmail = $formData['projekt_kapcsolattarto_email'] ?? null;

        $updateResult = $this->projectService->updateProject($projectDto);

        if (!$updateResult->success) {
            $errors[] = $updateResult->message;
            return new View("create_or_edit", ['formData' => $formData, 'errors' => $errors, 'statuses' => $statuses, 'owners' => $owners]);
        }

        return new View("create_or_edit", ['formData' => null, 'statuses' => $statuses, 'owners' => $owners, 'successMessage' => 'A projekt sikeresen módosítva!']);
    }

    private function validateFormData(array $formData, array $errors): array
    {
        if ($formData['projekt_cim'] === "") {
            $errors[] = "A cím megadása kötelező!";
        }

        if ($formData['projekt_leiras'] === "") {
            $errors[] = "A leírás megadása kötelező!";
        }

        if ($formData['projekt_statusz'] === "none") {
            $errors[] = "A státusz kiválasztása kötelező!";
        }

        if ($formData['projekt_owner'] === "none") {
            $errors[] = "A kapcsolattartó kiválasztása kötelező!";
        }

        if ($formData["projekt_owner"] === "new") {
            if ($formData["projekt_kapcsolattarto_neve"] === "") {
                $errors[] = "Az új kapcsolattartó nevének megadása kötelező!";
            }

            if ($formData["projekt_kapcsolattarto_email"] === "") {
                $errors[] = "Az új kapcsolattartó email címének megadása kötelező!";
            } else if (!filter_var($formData["projekt_kapcsolattarto_email"], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Az új kapcsolattartó email címe nem megfelelő formátumú!";
            }
        }

        return $errors;
    }

    #[HttpMethod('GET')]
    public function delete(int $id): JsonResponse
    {
        $result = new Result();
        $project = $this->projectService->getProjectById($id);

        if (!$project) {
            $this->logger->error("Nem található projekt ezzel az azonosítóval: {$id}!");
            $result->message = "Nem található projekt ezzel az azonosítóval: {$id}!";
            return new JsonResponse($result);
        }

        $deleteResult = $this->projectService->deleteProject($id);
        if (!$deleteResult->success) {
            $this->logger->error("Nem sikerült törölni a #{$id} projektet: {$deleteResult->message}");
            $result->message = "Nem sikerült törölni a #{$id} projektet: {$deleteResult->message}";
            return new JsonResponse($result);
        }

        $result->success = true;
        $result->message = "A projekt #{$id} sikeresen törölve!";
        return new JsonResponse($result);
    }
}
