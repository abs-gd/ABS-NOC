<?php
/*
namespace App\Controllers;

class AuthController {
  public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $_SESSION['user'] = $_POST['username'];
      header('Location: /');
      exit;
    }

    return '<form method="POST"><input type="text" name="username"><button type="submit">Login</button></form>';
  }

  public function logout() {
    session_destroy();
    header('Location: /');
    exit;
  }
}
*/


namespace App\Controllers;

use App\Models\User;

class AuthController {
    private User $userModel;

    public function __construct() {
        $db = require __DIR__ . '/../../config/database.php';
        $this->userModel = new User($db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        return '<form method="POST">
                    <input type="email" name="email" required placeholder="Email">
                    <input type="password" name="password" required placeholder="Password">
                    <button type="submit">Register</button>
                </form>';
    }

    public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return "Invalid email or password.";
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email']
        ];

        header('Location: /');
        exit;
    }

    return '<form method="POST">
                <input type="email" name="email" required placeholder="Email">
                <input type="password" name="password" required placeholder="Password">
                <button type="submit">Login</button>
            </form>';
}

public function logout() {
    session_destroy();
    header('Location: /');
    exit;
}
}

