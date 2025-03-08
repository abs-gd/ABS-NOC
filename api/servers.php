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

// DELETE Server
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    if (!isset($data['id'])) {
        echo json_encode(["error" => "Missing server ID"]);
        http_response_code(400);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM servers WHERE id = ?");
    if ($stmt->execute([$data['id']])) {
        echo json_encode(["message" => "Server deleted"]);
    } else {
        echo json_encode(["error" => "Failed to delete server"]);
        http_response_code(500);
    }
    exit;
}

// UPDATE Server (Rename or Change IP)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['ip_address'])) {
        echo json_encode(["error" => "Missing required fields"]);
        http_response_code(400);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE servers SET name = ?, ip_address = ? WHERE id = ?");
    if ($stmt->execute([$data['name'], $data['ip_address'], $data['id']])) {
        echo json_encode(["message" => "Server updated"]);
    } else {
        echo json_encode(["error" => "Failed to update server"]);
        http_response_code(500);
    }
    exit;
}

// TOGGLE Server Status (Active/Inactive)
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    parse_str(file_get_contents("php://input"), $data);
    if (!isset($data['id']) || !isset($data['status'])) {
        echo json_encode(["error" => "Missing server ID or status"]);
        http_response_code(400);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE servers SET status = ? WHERE id = ?");
    if ($stmt->execute([$data['status'], $data['id']])) {
        echo json_encode(["message" => "Server status updated"]);
    } else {
        echo json_encode(["error" => "Failed to update status"]);
        http_response_code(500);
    }
    exit;
}

echo json_encode(["error" => "Invalid request"]);
http_response_code(400);