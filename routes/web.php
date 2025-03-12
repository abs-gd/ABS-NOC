<?php

$routes = [
    '/' => 'HomeController@index',
    
    '/servers' => 'ServerController@index',
    '/servers/create' => 'ServerController@create',
    '/servers/delete' => 'ServerController@delete',

    '/servers/{id}' => 'ServerDetailsController@show',
    '/servers/update' => 'ServerDetailsController@update',

    '/login' => 'AuthController@login',
    '/logout' => 'AuthController@logout',
    '/register' => 'AuthController@register',
];

// Handle dynamic routes (like /servers/{id})
$requestUri = strtok($_SERVER['REQUEST_URI'], '?'); // Remove query string
if (preg_match('#^/servers/(\d+)$#', $requestUri, $matches)) {
    $routes[$requestUri] = 'ServerDetailsController@show';
    $_GET['id'] = $matches[1]; // Store ID for controller access
}

return $routes;
