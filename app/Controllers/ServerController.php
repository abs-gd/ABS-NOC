<?php

namespace App\Controllers;

use App\Core\Renderer;
use App\Models\Server;
use App\Helpers\Csrf;

class ServerController {
    private Server $serverModel;

    public function __construct() {
        $db = require __DIR__ . '/../../config/database.php';
        $this->serverModel = new Server($db);
    }

    public function index() {
        $servers = $this->serverModel->getAll();
        return Renderer::render('servers.twig', ['servers' => $servers]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'])) {
                return Renderer::render('servers.twig', [
                    'servers' => $this->serverModel->getAll(),
                    'error' => 'Invalid CSRF token.'
                ]);
            }

            $name = trim($_POST['name']);
            $ip_address = trim($_POST['ip_address']);

            if (empty($name) || empty($ip_address)) {
                return Renderer::render('servers.twig', [
                    'servers' => $this->serverModel->getAll(),
                    'error' => 'All fields are required.'
                ]);
            }

            if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
                return Renderer::render('servers.twig', [
                    'servers' => $this->serverModel->getAll(),
                    'error' => 'Invalid IP address format.'
                ]);
            }

            $this->serverModel->create($name, $ip_address);

            return Renderer::render('servers.twig', [
                'servers' => $this->serverModel->getAll(),
                'success' => 'Server added successfully!'
            ]);
        }
    }
}
