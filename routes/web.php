<?php

$routes = [
  '/' => 'HomeController@index',
  '/login' => 'AuthController@login',
  '/logout' => 'AuthController@logout',
  '/register' => 'AuthController@register',
];

return $routes;
