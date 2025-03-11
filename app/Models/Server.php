<?php

namespace App\Models;

use PDO;

class Server {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM servers ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function create(string $name, string $ip_address) {
        $stmt = $this->db->prepare("INSERT INTO servers (name, ip_address) VALUES (:name, :ip_address)");
        return $stmt->execute([
            'name' => $name,
            'ip_address' => $ip_address
        ]);
    }
}
