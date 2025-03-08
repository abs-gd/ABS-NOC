<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    echo json_encode(["error" => "Unauthorized"]);
    http_response_code(401);
    exit;
}

$token = $matches[1];
try {
    $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    $userId = $decoded->user_id;
} catch (Exception $e) {
    echo json_encode(["error" => "Invalid token"]);
    http_response_code(401);
    exit;
}