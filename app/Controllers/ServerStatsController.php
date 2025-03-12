<?php

namespace App\Controllers;

use App\Models\ServerStats;
use App\Middleware\ApiAuth;

class ServerStatsController {
    private ServerStats $statsModel;

    public function __construct() {
        $db = require __DIR__ . '/../../config/database.php';
        $this->statsModel = new ServerStats($db);
    }

    public function store() {
        header('Content-Type: application/json');
        ApiAuth::check();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Method Not Allowed"]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['server_id'], $data['cpu_usage'], $data['ram_usage'], $data['disk_usage'], $data['network_usage'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
            exit;
        }

        $server_id = (int) $data['server_id'];
        $cpu_usage = (float) $data['cpu_usage'];
        $ram_usage = (float) $data['ram_usage'];
        $disk_usage = (float) $data['disk_usage'];
        $network_usage = (float) $data['network_usage'];

        if ($this->statsModel->store($server_id, $cpu_usage, $ram_usage, $disk_usage, $network_usage)) {
            echo json_encode(["success" => "Stats recorded successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to record stats"]);
        }
    }

    public function cleanup() {
        header('Content-Type: application/json');

        $this->statsModel->cleanupOldStats(7);
        echo json_encode(["success" => "Old stats cleaned up"]);
    }
}
