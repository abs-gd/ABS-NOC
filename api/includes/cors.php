<?php
// Remove any existing headers that might interfere
header_remove("Access-Control-Allow-Origin");
header_remove("Access-Control-Allow-Methods");
header_remove("Access-Control-Allow-Headers");

// Set CORS headers manually
$allowed_origin = "http://localhost:3000";
header("Access-Control-Allow-Origin: $allowed_origin");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PATCH, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight (OPTIONS request)
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(204);
    exit();
}