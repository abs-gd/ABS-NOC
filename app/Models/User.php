<?php

namespace App\Models;

use PDO;

class User {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findByEmail(string $email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function create(string $email, string $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
        return $stmt->execute(['email' => $email, 'password' => $hashedPassword]);
    }
}
