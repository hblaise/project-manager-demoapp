<?php

require_once '../vendor/autoload.php';

use App\Exceptions\ControllerActionNotFoundException;
use App\Exceptions\HttpMethodNotAllowedException;
use App\Common\View;
use App\Common\DatabaseConfig;
use App\Common\EmailConfig;
use App\Common\Logger;
use App\Controllers\ProjectController;

$logger = new Logger();

try {
    $databaseConfig = json_decode(file_get_contents('../config/database.json'), true);
    DatabaseConfig::set($databaseConfig);
    $emailConfig = json_decode(file_get_contents('../config/email.json'), true);
    EmailConfig::set($emailConfig);

    $routes = [
        '/' => [ProjectController::class, 'index'],
        '/show' => [ProjectController::class, 'show'],
        '/create' => [ProjectController::class, 'create'],
        '/store' => [ProjectController::class, 'store'],
        '/edit' => [ProjectController::class, 'edit'],
        '/update' => [ProjectController::class, 'update'],
        '/delete' => [ProjectController::class, 'delete'],
    ];

    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (isset($routes[$requestUri])) {
        [$controllerName, $action] = $routes[$requestUri];
        $controller = new $controllerName();
        $controller->callAction($action);
    } else {
        $notFoundView = new View("404");
        $notFoundView->render();
    }
} catch (ControllerActionNotFoundException $controllerActionNotFoundEx) {
    $notFoundView = new View("404");
    $notFoundView->render();
} catch (HttpMethodNotAllowedException $httpMethodNotAllowedEx) {
    $notAllowedView = new View("405");
    $notAllowedView->render();
} catch (Exception $ex) {
    $logger->error($ex->getMessage() . ', ' . $ex->getTraceAsString());
    $errorView = new View("500");
    $errorView->render();
}
