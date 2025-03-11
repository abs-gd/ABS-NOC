<?php

namespace App\Middleware;

class Cors {
    public static function handle() {
    $allowedOrigins = [
      'https://noc.abs.gd',
      'http://noc.abs.test',
      'http://localhost:3000'
    ];
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    // Remove any existing headers that might interfere
    header_remove("Access-Control-Allow-Origin");
    header_remove("Access-Control-Allow-Methods");
    header_remove("Access-Control-Allow-Headers");
    


      // Set CORS headers manually
      if (in_array($origin, $allowedOrigins)) {
      header("Access-Control-Allow-Origin: $origin");
    }
      header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PATCH, DELETE, PUT");
      header("Access-Control-Allow-Headers: Content-Type, Authorization");
      header("Access-Control-Allow-Credentials: true");
      /*header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' http: https: data: blob:;");*/

      // Handle preflight (OPTIONS request)
      if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
        http_response_code(204);
        exit();
      }

      session_set_cookie_params([
          'lifetime' => 0,            // Session expires when the browser closes
          'secure' => false,           // Doesn't require HTTPS (change in production)
          'httponly' => true,
          //'samesite' => 'Lax' 
          'samesite' => 'Strict'
      ]);

   
    }
}







