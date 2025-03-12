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

    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM servers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(string $name, string $ip_address) {
        $stmt = $this->db->prepare("INSERT INTO servers (name, ip_address) VALUES (:name, :ip_address)");
        return $stmt->execute([
            'name' => $name,
            'ip_address' => $ip_address
        ]);
    }

    /*public function update(int $id, string $name, string $ip_address) {
        $stmt = $this->db->prepare("UPDATE servers SET name = :name, ip_address = :ip_address WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'ip_address' => $ip_address
        ]);
    }*/

    public function updateDetails(int $id, string $name, string $ip_address, string $os, string $location, string $status, int $uptime) {
        $stmt = $this->db->prepare("
            UPDATE servers 
            SET name = :name, ip_address = :ip_address, os = :os, location = :location, status = :status, uptime = :uptime
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'ip_address' => $ip_address,
            'os' => $os,
            'location' => $location,
            'status' => $status,
            'uptime' => $uptime
        ]);
    }

    public function delete(int $id) {
        $stmt = $this->db->prepare("DELETE FROM servers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
