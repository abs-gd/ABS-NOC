<?php
require 'database.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateToken($userId) {
    $payload = [
        "user_id" => $userId,
        "exp" => time() + 3600
    ];
    return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(["token" => generateToken($user['id'])]);
    } else {
        echo json_encode(["error" => "Unauthorized"]);
    }
}