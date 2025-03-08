<?php
require 'database.php';
require 'vendor/autoload.php';

include_once 'includes/cors.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/*
 * PERMANENT TOKEN FOR AGENTS
 *****************************/
// Generate a permanent API key
function generateAgentToken($serverId) {
    $payload = [
        "server_id" => $serverId,  // Link token to a server
        "iat" => time(),  // Issued at time
    ];
    return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
}

// Register an agent (one-time API key generation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['register_agent'])) {
    $data = json_decode(file_get_contents("php://input"), true);
    $serverId = $data['server_id'] ?? null;

    if (!$serverId) {
        echo json_encode(["error" => "Missing server ID"]);
        http_response_code(400);
        exit();
    }

    // Generate a permanent token for this agent
    $agentToken = generateAgentToken($serverId);

    // Store the token in the database
    $stmt = $pdo->prepare("UPDATE servers SET agent_token = ? WHERE id = ?");
    $stmt->execute([$agentToken, $serverId]);

    echo json_encode(["agent_token" => $agentToken]);
    exit();
}

/*
 * Token that expires for other logins
 **************************************/
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