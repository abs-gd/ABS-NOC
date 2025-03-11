<?php

$routes = [
    '/' => 'HomeController@index',
    '/login' => 'AuthController@login',
    '/logout' => 'AuthController@logout',
    '/register' => 'AuthController@register',
    '/servers' => 'ServerController@index',
    '/servers/create' => 'ServerController@create',
];

return $routes;
