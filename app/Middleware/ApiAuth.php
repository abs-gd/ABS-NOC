<?php

namespace App\Middleware;

use App\Models\ApiKey;

class ApiAuth {
    public static function check() {
        $db = require __DIR__ . '/../../config/database.php';
        $apiKeyModel = new ApiKey($db);

        $headers = getallheaders();
        /*$apiKey = $headers['X-API-KEY'] ?? null;*/
        $apiKey = $headers['X-Api-Key'] ?? null;

        if (!$apiKey || !$apiKeyModel->isValidKey($apiKey)) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized - Invalid API Key"]);
            exit;
        }
    }
}