<?php
require 'database.php';
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

include_once 'includes/cors.php';
include_once 'includes/secured.php';

// Handle POST request for adding a server
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name']) || !isset($data['ip_address'])) {
        echo json_encode(["error" => "Missing required fields"]);
        http_response_code(400);
        exit;
    }

    $name = $data['name'];
    $ipAddress = $data['ip_address'];

    // Insert new server
    $stmt = $pdo->prepare("INSERT INTO servers (name, ip_address, status) VALUES (?, ?, 'inactive')");
    if ($stmt->execute([$name, $ipAddress])) {
        echo json_encode(["message" => "Server added successfully"]);
    } else {
        echo json_encode(["error" => "Failed to add server"]);
        http_response_code(500);
    }
    exit;
}