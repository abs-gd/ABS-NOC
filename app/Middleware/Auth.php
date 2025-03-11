<?php

namespace App\Middleware;

class Auth {
    public static function check() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
}