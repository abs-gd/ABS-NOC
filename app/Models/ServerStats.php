<?php

namespace App\Models;

use PDO;
use App\Helpers\EmailHelper;

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
    }

    public function checkAndSendAlerts(int $server_id, float $cpu, float $ram, float $disk) {
        $threshold = getenv('ALERT_THRESHOLD');
        
        // Fetch server name
        $stmt = $this->db->prepare("SELECT name FROM servers WHERE id = :server_id");
        $stmt->execute(['server_id' => $server_id]);
        $server = $stmt->fetch();
        $serverName = $server ? $server['name'] : "Unknown Server";

        if ($cpu > $threshold || $ram > $threshold || $disk > $threshold) {
            $subject = "NOC ALERT: $serverName";
            $message = "
                <h2>Server: $serverName</h2>
                <p><strong>CPU Usage:</strong> $cpu%</p>
                <p><strong>RAM Usage:</strong> $ram%</p>
                <p><strong>Disk Usage:</strong> $disk%</p>
                <p>One or more metrics have exceeded the threshold of $threshold%.</p>
            ";
            EmailHelper::sendAlert($subject, $message);
        }
    }
}
