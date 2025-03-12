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
}
