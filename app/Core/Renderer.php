<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Helpers\Csrf;

class Renderer {
    private static ?Environment $twig = null;

    public static function init() {
        if (self::$twig === null) {
            $loader = new FilesystemLoader(__DIR__ . '/../../resources/views');
            self::$twig = new Environment($loader, [
                'cache' => __DIR__ . '/../../storage/cache/twig',
                'debug' => true
            ]);
        }
        return self::$twig;
    }

    public static function render(string $template, array $data = []) {
              // Inject CSRF token automatically into all templates
        $data['csrf_token'] = Csrf::generateToken();
        return self::init()->render($template, $data);
    }
}
