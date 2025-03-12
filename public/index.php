<?php

use App\Middleware\Cors;
use App\Controllers\HomeController;
use App\Controllers\AuthController;

require_once __DIR__ . '/../vendor/autoload.php';
/*
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

error_log("SMTP_HOST: " . getenv('SMTP_HOST'));
error_log("SMTP_FROM: " . getenv('SMTP_FROM'));
error_log("ALERT_RECIPIENT: " . getenv('ALERT_RECIPIENT'));
*/
Cors::handle();

session_start();
//var_dump($_SESSION);

/*$routes = require __DIR__ . '/../routes/web.php';*/
$webRoutes = require __DIR__ . '/../routes/web.php';
$apiRoutes = require __DIR__ . '/../routes/api.php';
$routes = array_merge($webRoutes, $apiRoutes);

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

