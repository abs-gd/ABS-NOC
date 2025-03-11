<?php
namespace App\Controllers;

use App\Core\Renderer;

class HomeController {
  public function index() {
    /*if (!isset($_SESSION['user'])) {
        return '<p>You are not logged in. <a href="/login">Login here</a></p>';
    }

    return "<p>Welcome, " . htmlspecialchars($_SESSION['user']['email']) . "!</p>"
    . "<form method='POST' action='/logout'>"
    . "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>"
    . "<button type='submit'>Logout</button>"
    . "</form>"
    ;*/
    return Renderer::render('home.twig', [
            'user' => $_SESSION['user'] ?? null
        ]);
}
}
