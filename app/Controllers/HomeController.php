<?php
namespace App\Controllers;

class HomeController {
  public function index() {
    if (!isset($_SESSION['user'])) {
        return '<p>You are not logged in. <a href="/login">Login here</a></p>';
    }

    return "<p>Welcome, " . htmlspecialchars($_SESSION['user']['email']) . "! <a href='/logout'>Logout</a></p>";
}
}
