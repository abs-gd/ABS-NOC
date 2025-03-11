<?php
namespace App\Controllers;

use App\Core\Renderer;
use App\Middleware\Auth;

class HomeController {
    public function index() {
        Auth::check();
        return Renderer::render('home.twig', [
            'user' => $_SESSION['user'] ?? null
        ]);
    }
}
