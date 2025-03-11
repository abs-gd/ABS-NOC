<?php
// Remove any existing headers that might interfere
header_remove("Access-Control-Allow-Origin");
header_remove("Access-Control-Allow-Methods");
header_remove("Access-Control-Allow-Headers");

// Set CORS headers manually
$allowed_origin = "http://noc.abs.test:80";
header("Access-Control-Allow-Origin: $allowed_origin");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PATCH, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// FOR LOCAL TESTING
/*header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' http: https: data: blob:;");*/

// Handle preflight (OPTIONS request)
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(204);
    exit();
}

session_set_cookie_params([
    //'lifetime' => 0,            // Session expires when the browser closes
    //'path' => '/',
    //'domain' => '',             // Set if using a specific domain
    'secure' => false,           // Doesn't require HTTPS (change in production)
    'httponly' => true,         // Prevents JavaScript access to the cookie
    //'samesite' => 'Lax'        // Allows cross-site cookies
    'samesite' => 'Strict'        // Allows cross-site cookies
]);

session_start();
//var_dump($_SESSION);
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\AuthController;

$routes = require __DIR__ . '/../routes/web.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (isset($routes[$requestUri])) {
    [$controller, $method] = explode('@', $routes[$requestUri]);
    
    // Dynamically call the controller with namespace
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
