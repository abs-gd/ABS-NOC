<?php

namespace App\Helpers;

class JSLoader {
    public static function load($filename) {
        $filePath = dirname(dirname(__DIR__)) . '/public/js/' . $filename;

        if (!file_exists($filePath)) {
            error_log("JSLoader: File not found - " . $filePath);
            return "<!-- JS file not found: $filename -->";
        }

        // Read and process JavaScript
        $jsCode = file_get_contents($filePath);
        return "<script>\n" . $jsCode . "\n</script>";
    }
}
