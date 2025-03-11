<?php
use App\Middleware\Cors;
use App\Controllers\HomeController;
use App\Controllers\AuthController;

require_once __DIR__ . '/../vendor/autoload.php';

Cors::handle(); // Apply CORS handling

session_start();
//var_dump($_SESSION);

$routes = require __DIR__ . '/../routes/web.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (isset($routes[$requestUri])) {
    [$controller, $method] = explode('@', $routes[$requestUri]);
    
    $controllerClass = "App\\Controllers\\$controller";
    
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        echo $controllerInstance->$method();
    } else {
        http_response_code(500);
        echo "Controller not found.";
    }
} else {
    http_response_code(404);
    echo "Page not found";
}
