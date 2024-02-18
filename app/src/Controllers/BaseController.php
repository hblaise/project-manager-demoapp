<?php

namespace App\Controllers;

use ReflectionClass;
use Exception;
use App\Exceptions\ControllerActionNotFoundException;
use App\Exceptions\HttpMethodNotAllowedException;
use App\Common\View;
use App\Common\HttpMethod;
use App\Common\JsonResponse;
use App\Common\Request;
use App\Common\Logger;
use ReflectionException;

class BaseController
{
    /**
     * Call the action method with the given name
     *
     * @param string $name
     * @throws Exception
     * @throws ControllerActionNotFoundException
     * @throws ReflectionException
     * @throws HttpMethodNotAllowedException
     */
    public function callAction(string $name): void
    {
        $logger = new Logger();

        $reflectionClass = new ReflectionClass($this);

        if (!$reflectionClass->hasMethod($name)) {
            throw new ControllerActionNotFoundException("Action does not exist");
        }

        $reflectionMethod = $reflectionClass->getMethod($name);

        // Handle HTTP methods
        $attributes = $reflectionMethod->getAttributes(HttpMethod::class);
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $params = $_REQUEST;

        foreach ($attributes as $attribute) {
            if ($attribute->newInstance() instanceof HttpMethod) {
                $httpMethodAttr = $attribute->newInstance();
                if (!in_array($httpMethod, $httpMethodAttr->getMethods())) {
                    throw new HttpMethodNotAllowedException("Method not allowed");
                }
            }
        }

        // Handle request parameters
        $request = new Request($_GET, $_POST, $_SERVER, $_FILES);
        $args = [];
        foreach ($reflectionMethod->getParameters() as $parameter) {
            if ($parameter->getType() && (string)$parameter->getType() === 'App\Common\Request') {
                $args[] = $request;
            } else {
                $paramName = $parameter->getName();
                if (isset($params[$paramName])) {
                    $args[] = $params[$paramName];
                } else if ($parameter->isOptional()) {
                    $args[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Missing parameter: $paramName");
                }
            }
        }

        // Handle response
        $response = $reflectionMethod->invokeArgs($this, $args);
        if ($response instanceof View) {
            $response->render();
        } elseif ($response instanceof JsonResponse) {
            $response->send();
        } elseif (is_array($response) || is_object($response)) {
            $jsonResponse = new JsonResponse($response);
            $jsonResponse->send();
        } else {
            $type = gettype($response);
            $logger->error("Invalid response type: {$type} for action: {$name} in class: " . get_class($this));
            $response = new View("500");
            $response->render();
        }
    }
}
