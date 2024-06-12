<?php

use App\Controllers\ExampleController;
use App\Controllers\HomeController;
use App\Router;

require '../vendor/autoload.php';

$router = new Router();

$router->registerRoutes([
    HomeController::class,
    ExampleController::class
]);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $method);
