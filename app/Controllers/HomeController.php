<?php
namespace App\Controllers;

use App\Core\Renderer;

class HomeController {
    public function index() {
        return Renderer::render('home.twig', [
            'user' => $_SESSION['user'] ?? null
        ]);
    }
}
