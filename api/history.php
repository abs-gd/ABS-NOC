<?php
require 'database.php';
require 'vendor/autoload.php';

include_once 'includes/cors.php';
include_once 'includes/secured.php';

// ✅ Check if server ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Missing server ID"]);
    http_response_code(400);
    exit;
}

$serverId = $_GET['id'];

// ✅ Fetch historical data from `server_stats`
$stmt = $pdo->prepare("SELECT cpu_usage, ram_usage, disk_usage, network_usage, recorded_at FROM server_stats WHERE server_id = ? ORDER BY recorded_at DESC LIMIT 100");
$stmt->execute([$serverId]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Return JSON response
echo json_encode($history);
exit;
