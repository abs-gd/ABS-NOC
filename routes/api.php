<?php

$routes = [
  '/api/server-stats' => 'ServerStatsController@store', // Where agent stores stats
  '/api/cleanup-stats' => 'ServerStatsController@cleanup', // So easy log cleanup with cron
  '/api/servers' => 'ServerController@getLatestServers', // Table reload for servers page
];

return $routes;