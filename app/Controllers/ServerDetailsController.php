<?php

namespace App\Controllers;

use App\Core\Renderer;
use App\Models\Server;
use App\Middleware\Auth;
use App\Helpers\Csrf;

class ServerDetailsController {
    private Server $serverModel;

    public function __construct() {
        Auth::check(); // Ensure only logged-in users can access
        $db = require __DIR__ . '/../../config/database.php';
        $this->serverModel = new Server($db);
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("Invalid server ID.");
        }

        $server = $this->serverModel->findById($id);
        if (!$server) {
            die("Server not found.");
        }

        return Renderer::render('server_details.twig', ['server' => $server]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'])) {
                return Renderer::render('server_details.twig', [
                    'server' => $this->serverModel->findById($_POST['id']),
                    'error' => 'Invalid CSRF token.'
                ]);
            }

            $id = (int) $_POST['id'];
            $name = trim($_POST['name']);
            $ip_address = trim($_POST['ip_address']);
            $os = trim($_POST['os']);
            $location = trim($_POST['location']);
            $status = $_POST['status'];
            $uptime = (int) $_POST['uptime'];

            if (empty($name) || empty($ip_address) || empty($os) || empty($location)) {
                return Renderer::render('server_details.twig', [
                    'server' => $this->serverModel->findById($id),
                    'error' => 'All fields are required.'
                ]);
            }

            if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
                return Renderer::render('server_details.twig', [
                    'server' => $this->serverModel->findById($id),
                    'error' => 'Invalid IP address format.'
                ]);
            }

            if (!in_array($status, ['active', 'inactive'])) {
                return Renderer::render('server_details.twig', [
                    'server' => $this->serverModel->findById($id),
                    'error' => 'Invalid status value.'
                ]);
            }

            $this->serverModel->updateDetails($id, $name, $ip_address, $os, $location, $status, $uptime);

            return Renderer::render('server_details.twig', [
                'server' => $this->serverModel->findById($id),
                'success' => 'Server updated successfully!'
            ]);
        }
    }
}
