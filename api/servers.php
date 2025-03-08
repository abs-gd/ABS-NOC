<?php
// Remove any existing headers that might interfere
header_remove("Access-Control-Allow-Origin");
header_remove("Access-Control-Allow-Methods");
header_remove("Access-Control-Allow-Headers");

// Set CORS headers manually
$allowed_origin = "http://localhost:3000";  // ✅ Set the allowed frontend origin

header("Access-Control-Allow-Origin: $allowed_origin");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight (OPTIONS request)
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(204);
    exit();
}

require 'database.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// ✅ Secure API by requiring Authorization
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

// ✅ Fetch all servers (only for authorized users)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['id'])) {
    $stmt = $pdo->query("SELECT * FROM servers");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ✅ Fetch a single server by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $serverId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM servers WHERE id = ?");
    $stmt->execute([$serverId]);
    $server = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$server) {
        echo json_encode(["error" => "Server not found"]);
        http_response_code(404);
        exit;
    }

    echo json_encode($server);
    exit;
}

// ✅ Send monitoring data (CPU, RAM, Disk, Network)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $serverId = $_GET['id'];
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate input
    if (!isset($data['cpu_usage']) || !isset($data['ram_usage']) || !isset($data['disk_usage']) || !isset($data['network_usage'])) {
        echo json_encode(["error" => "Missing parameters"]);
        http_response_code(400);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE servers SET cpu_usage = ?, ram_usage = ?, disk_usage = ?, network_usage = ? WHERE id = ?");
    $stmt->execute([$data['cpu_usage'], $data['ram_usage'], $data['disk_usage'], $data['network_usage'], $serverId]);

    echo json_encode(["message" => "Metrics updated successfully"]);
    exit;
}

echo json_encode(["error" => "Invalid request"]);
http_response_code(400);