<?php

$routes = [
    '/' => 'HomeController@index',
    '/login' => 'AuthController@login',
    '/logout' => 'AuthController@logout',
    '/register' => 'AuthController@register',
    '/servers' => 'ServerController@index',
    '/servers/create' => 'ServerController@create',
    '/servers/edit' => 'ServerController@edit',
    '/servers/delete' => 'ServerController@delete',
];

return $routes;
