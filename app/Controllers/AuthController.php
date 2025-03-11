<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\Csrf;
use App\Core\Renderer;

class AuthController {
    private User $userModel;

    public function __construct() {
        $db = require __DIR__ . '/../../config/database.php';
        $this->userModel = new User($db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'])) {
                return Renderer::render('register.twig', ['error' => 'Invalid CSRF token.']);
            }
            
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return Renderer::render('register.twig', ['error' => 'Invalid email format.']);
            }
            if (strlen($password) < 8) {
                return Renderer::render('register.twig', ['error' => 'Password must be at least 8 characters.']);
            }
            if ($this->userModel->findByEmail($email)) {
                return Renderer::render('register.twig', ['error' => 'Email already exists.']);
            }

            $this->userModel->create($email, $password);
            return Renderer::render('register.twig', ['success' => 'Registration successful! You can now log in.']);
            header('Location: /login');
        }

        return Renderer::render('register.twig');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'])) {
                return Renderer::render('login.twig', ['error' => 'Invalid CSRF token.']);
            }
            
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            $user = $this->userModel->findByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                return Renderer::render('login.twig', ['error' => 'Invalid email or password.']);
            }

            $_SESSION['user'] = ['id' => $user['id'], 'email' => $user['email']];
            header('Location: /');
            exit;
        }

        return Renderer::render('login.twig');
    }

    public function logout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validateToken($_POST['csrf_token'])) {
            die("Invalid CSRF token.");
        }
        
        session_destroy();
        header('Location: /');
        exit;
    }
}

