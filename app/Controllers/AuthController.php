<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\Csrf;

class AuthController {
    private User $userModel;

    public function __construct() {
        $db = require __DIR__ . '/../../config/database.php';
        $this->userModel = new User($db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'])) {
                die("Invalid CSRF token.");
            }

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "Invalid email format.";
            }

            if (strlen($password) < 8) {
                return "Password must be at least 8 characters.";
            }

            if ($this->userModel->findByEmail($email)) {
                return "Email already exists.";
            }

            $this->userModel->create($email, $password);
            return "Registration successful! <a href='/login'>Login</a>";
        }

        $csrf_token = Csrf::generateToken();
        return "<form method='POST'>
                    <input type='hidden' name='csrf_token' value='$csrf_token'>
                    <p>CSRF Token: $csrf_token</p>  <!-- Debugging -->
                    <input type='email' name='email' required placeholder='Email'>
                    <input type='password' name='password' required placeholder='Password'>
                    <button type='submit'>Register</button>
                </form>";
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'])) {
                die("Invalid CSRF token.");
            }

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $user = $this->userModel->findByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                return "Invalid email or password.";
            }

            $_SESSION['user'] = ['id' => $user['id'], 'email' => $user['email']];
            header('Location: /');
            exit;
        }
        $csrf_token = Csrf::generateToken();
        return "<form method='POST'>
                    <input type='hidden' name='csrf_token' value='$csrf_token'>
                    <input type='email' name='email' required placeholder='Email'>
                    <input type='password' name='password' required placeholder='Password'>
                    <button type='submit'>Login</button>
                </form>";
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

