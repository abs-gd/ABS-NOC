<?php
require 'database.php';
require 'vendor/autoload.php';

include_once 'includes/cors.php';
include_once 'includes/secured.php';

// Fetch all servers
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['id'])) {
    $stmt = $pdo->query("SELECT * FROM servers");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Fetch a single server by ID
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

// Send monitoring data (CPU, RAM, Disk, Network)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $serverId = $_GET['id'];
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate input
    if (!isset($data['cpu_usage']) || !isset($data['ram_usage']) || !isset($data['disk_usage']) || !isset($data['network_usage'])) {
        echo json_encode(["error" => "Missing parameters"]);
        http_response_code(400);
        exit;
    }

    // Store new server metrics in `server_stats`
    $stmt = $pdo->prepare("INSERT INTO server_stats (server_id, cpu_usage, ram_usage, disk_usage, network_usage) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$serverId, $data['cpu_usage'], $data['ram_usage'], $data['disk_usage'], $data['network_usage']]);

    echo json_encode(["message" => "Metrics updated successfully"]);
/*
    $stmt = $pdo->prepare("UPDATE servers SET cpu_usage = ?, ram_usage = ?, disk_usage = ?, network_usage = ? WHERE id = ?");
    $stmt->execute([$data['cpu_usage'], $data['ram_usage'], $data['disk_usage'], $data['network_usage'], $serverId]);

    echo json_encode(["message" => "Metrics updated successfully"]);
*/
    exit;
}

echo json_encode(["error" => "Invalid request"]);
http_response_code(400);