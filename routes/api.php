<?php

$routes = [
  '/api/server-stats' => 'ServerStatsController@store', // Where agent stores stats
  '/api/cleanup-stats' => 'ServerStatsController@cleanup', // So easy log cleanup with cron
  '/api/servers' => 'ServerController@getLatestServers', // Get all servers and their latest stats for table refresh on servers page
  '/api/server/{id}/stats' => 'ServerStatsController@getLatestStats',  // Fetch latest stats for one server
  '/api/server/{id}/history' => 'ServerStatsController@getHistoricalStats',  // Fetch historical stats for one server
];

// Handle dynamic routes
$requestUri = strtok($_SERVER['REQUEST_URI'], '?'); // Remove query string

 
if (preg_match('#^/api/server/(\d+)/history$#', $requestUri, $matches)) {
    $routes[$requestUri] = 'ServerStatsController@getHistoricalStats';
    $_GET['id'] = $matches[1]; // Store ID for controller access
}
if (preg_match('#^/api/server/(\d+)/stats$#', $requestUri, $matches)) {
    $routes[$requestUri] = 'ServerStatsController@getLatestStats';
    $_GET['id'] = $matches[1]; // Store ID for controller access
}

return $routes;