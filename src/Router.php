<?php

namespace App;

use App\Attributes\Route;
use ReflectionClass;

class Router
{
    private array $routes = [];

    public function registerRoutes(array $controllerClasses): void
    {
        foreach ($controllerClasses as $controllerClass)
        {
            $reflectionController = new ReflectionClass($controllerClass);
            $reflectionMethods = $reflectionController->getMethods();
            $pathController = $this->getControllerRouteAttributePath($reflectionController);

            foreach ($reflectionMethods as $method)
            {
                $routeAttributes = $method->getAttributes(Route::class);

                if ($routeAttributes)
                {
                    $route = $routeAttributes[0]->newInstance();
                    $fullPath = $pathController . '/' . ltrim($route->path, '/');

                    $this->routes[$route->method][$fullPath] = [$controllerClass, $method->getName(), $route->requirements];
                }
            }
        }
    }

    private function getControllerRouteAttributePath(ReflectionClass $reflectionController): string
    {
        $routeAttributes = $reflectionController->getAttributes(Route::class);

        if (!$routeAttributes)
            return '';

        $baseRoute = $routeAttributes[0]->newInstance();
        return rtrim($baseRoute->path, '/');
    }


    public function dispatch(string $uri, string $method): void
    {
        foreach ($this->routes[$method] as $route => [$controllerClass, $methodName, $requirements])
        {
            $pattern = preg_replace('#\{([^}]+)\}#', '(?P<\1>[^/]+)', $route);
            $routeMatch = preg_match("#^$pattern$#", $uri, $matches);

            if ($routeMatch)
            {
                $params = $this->validateParameters($matches, $requirements);

                if ($params === null)
                {
                    http_response_code(400);
                    echo "400 Bad Request: Invalid parameter types.";
                    return;
                }

                $controller = new $controllerClass();
                $controller->$methodName(...$params);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private function validateParameters(array $matches, array $requirements): ?array
    {
        $params = [];

        foreach ($requirements as $param => $regex)
        {
            if (!isset($matches[$param]) || !preg_match($regex, $matches[$param]))
                return null;

            $params[] = $matches[$param];
        }

        return $params;
    }
}