<?php
// Remove any existing headers that might interfere
header_remove("Access-Control-Allow-Origin");
header_remove("Access-Control-Allow-Methods");
header_remove("Access-Control-Allow-Headers");

// Set CORS headers manually
$allowed_origin = "http://localhost:3000";  // ✅ Set the allowed frontend origin

header("Access-Control-Allow-Origin: $allowed_origin");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight (OPTIONS request)
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(204);
    exit();
}


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






// PERMANENT TOKEN FOR AGENTS
// ✅ Function to generate a permanent API key
function generateAgentToken($serverId) {
    $payload = [
        "server_id" => $serverId,  // ✅ Link token to a server
        "iat" => time(),  // Issued at time
    ];
    return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
}

// ✅ Register an agent (one-time API key generation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['register_agent'])) {
    $data = json_decode(file_get_contents("php://input"), true);
    $serverId = $data['server_id'] ?? null;

    if (!$serverId) {
        echo json_encode(["error" => "Missing server ID"]);
        http_response_code(400);
        exit();
    }

    // ✅ Generate a permanent token for this agent
    $agentToken = generateAgentToken($serverId);

    // ✅ Store the token in the database
    $stmt = $pdo->prepare("UPDATE servers SET agent_token = ? WHERE id = ?");
    $stmt->execute([$agentToken, $serverId]);

    echo json_encode(["agent_token" => $agentToken]);
    exit();
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