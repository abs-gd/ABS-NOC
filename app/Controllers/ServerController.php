<?php

namespace App\Controllers;

use App\Core\Renderer;
use App\Models\Server;
use App\Helpers\Csrf;
use App\Middleware\Auth;

class ServerController {
    private Server $serverModel;

    public function __construct() {
        /*session_start();*/
        Auth::check();
        $db = require __DIR__ . '/../../config/database.php';
        $this->serverModel = new Server($db);
    }

    public function index() {
        $servers = $this->serverModel->getAll();
        return Renderer::render('servers.twig', [
            'servers' => $servers,
            'user' => $_SESSION['user'] ?? null
        ]);
    }

    public function create() {
        Auth::check();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'])) {
                return Renderer::render('servers.twig', [
                    'servers' => $this->serverModel->getAll(),
                    'user' => $_SESSION['user'] ?? null,
                    'error' => 'Invalid CSRF token.'
                ]);
            }

            $name = trim($_POST['name']);
            $ip_address = trim($_POST['ip_address']);

            if (empty($name) || empty($ip_address)) {
                return Renderer::render('servers.twig', [
                    'servers' => $this->serverModel->getAll(),
                    'user' => $_SESSION['user'] ?? null,
                    'error' => 'All fields are required.'
                ]);
            }

            if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
                return Renderer::render('servers.twig', [
                    'servers' => $this->serverModel->getAll(),
                    'user' => $_SESSION['user'] ?? null,
                    'error' => 'Invalid IP address format.'
                ]);
            }

            $this->serverModel->create($name, $ip_address);

            return Renderer::render('servers.twig', [
                'servers' => $this->serverModel->getAll(),
                'success' => 'Server added successfully!',
                'user' => $_SESSION['user'] ?? null
            ]);
        }
    }

    /*public function edit() {
        Auth::check();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'])) {
                return Renderer::render('servers.twig', [
                    'servers' => $this->serverModel->getAll(),
                    'user' => $_SESSION['user'] ?? null,
                    'error' => 'Invalid CSRF token.'
                ]);
            }

            $id = (int) $_POST['id'];
            $name = trim($_POST['name']);
            $ip_address = trim($_POST['ip_address']);

            if (empty($name) || empty($ip_address)) {
                return Renderer::render('servers.twig', [
                    'servers' => $this->serverModel->getAll(),
                    'user' => $_SESSION['user'] ?? null,
                    'error' => 'All fields are required.'
                ]);
            }

            if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
                return Renderer::render('servers.twig', [
                    'servers' => $this->serverModel->getAll(),
                    'user' => $_SESSION['user'] ?? null,
                    'error' => 'Invalid IP address format.'
                ]);
            }

            $this->serverModel->update($id, $name, $ip_address);

            return Renderer::render('servers.twig', [
                'servers' => $this->serverModel->getAll(),
                'user' => $_SESSION['user'] ?? null,
                'success' => 'Server updated successfully!'
            ]);
        }
    }*/

    public function delete() {
        Auth::check();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'])) {
                return Renderer::render('servers.twig', [
                    'servers' => $this->serverModel->getAll(),
                    'user' => $_SESSION['user'] ?? null,
                    'error' => 'Invalid CSRF token.'
                ]);
            }

            $id = (int) $_POST['id'];
            $this->serverModel->delete($id);

            return Renderer::render('servers.twig', [
                'servers' => $this->serverModel->getAll(),
                'user' => $_SESSION['user'] ?? null,
                'success' => 'Server deleted successfully!'
            ]);
        }
    }
}
