<?php

namespace App\Models;

use PDO;

class Server {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM servers ORDER BY ip_address ASC");
        return $stmt->fetchAll();
    }

    public function getAllWithLatestStats() {
        $stmt = $this->db->prepare("
            SELECT s.id, s.name, s.ip_address, s.created_at,
                COALESCE(ss.cpu_usage, 'N/A') AS cpu_usage,
                COALESCE(ss.ram_usage, 'N/A') AS ram_usage,
                COALESCE(ss.disk_usage, 'N/A') AS disk_usage,
                COALESCE(ss.network_usage, 'N/A') AS network_usage,
                COALESCE(ss.recorded_at, 'N/A') AS last_updated
            FROM servers s
            LEFT JOIN server_stats ss ON s.id = ss.server_id
            AND ss.recorded_at = (
                SELECT MAX(recorded_at) FROM server_stats WHERE server_id = s.id
            )
            ORDER BY s.ip_address ASC
        ");
        
        $stmt->execute();
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
