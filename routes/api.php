<?php

$routes = [
  '/api/server-stats' => 'ServerStatsController@store',
  '/api/cleanup-stats' => 'ServerStatsController@cleanup',
];

return $routes;