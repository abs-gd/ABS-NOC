<?php

namespace App\Models;

use PDO;

class ServerStats {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function store(int $server_id, float $cpu_usage, float $ram_usage, float $disk_usage, float $network_usage): bool {
        $stmt = $this->db->prepare("
            INSERT INTO server_stats (server_id, cpu_usage, ram_usage, disk_usage, network_usage) 
            VALUES (:server_id, :cpu_usage, :ram_usage, :disk_usage, :network_usage)
        ");

        return $stmt->execute([
            'server_id' => $server_id,
            'cpu_usage' => $cpu_usage,
            'ram_usage' => $ram_usage,
            'disk_usage' => $disk_usage,
            'network_usage' => $network_usage
        ]);
    }

    public function cleanupOldStats(int $days = 7) {
        $stmt = $this->db->prepare("
            DELETE FROM server_stats WHERE recorded_at < NOW() - INTERVAL :days DAY
        ");
        $stmt->execute(['days' => $days]);
    }

    public function getLatestStatsByServer(int $server_id) {
        $stmt = $this->db->prepare("
            SELECT cpu_usage, ram_usage, disk_usage, network_usage, recorded_at 
            FROM server_stats 
            WHERE server_id = :server_id 
            ORDER BY recorded_at DESC 
            LIMIT 1
        ");
        $stmt->execute(['server_id' => $server_id]);
        return $stmt->fetch();
    }

    public function getHistoricalStatsByServer(int $server_id, int $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT cpu_usage, ram_usage, disk_usage, network_usage, recorded_at 
            FROM server_stats 
            WHERE server_id = :server_id 
            ORDER BY recorded_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':server_id', $server_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return array_reverse($stmt->fetchAll());
        /*$stmt->execute(['server_id' => $server_id]);
        return $stmt->fetchAll();*/
    }
}
