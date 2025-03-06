<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO servers (name, ip_address) VALUES (?, ?)");
    $stmt->execute([$data['name'], $data['ip_address']]);
    echo json_encode(["message" => "Server added successfully"]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM servers");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}