<?php

namespace App\Models;

use PDO;

class ApiKey {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function isValidKey(string $key): bool {
        $stmt = $this->db->prepare("SELECT id FROM api_keys WHERE key_value = :key");
        $stmt->execute(['key' => $key]);
        return $stmt->fetch() !== false;
    }
}
