<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use App\Helpers\Csrf;
use App\Helpers\JSLoader;

class Renderer {
    private static ?Environment $twig = null;

    public static function init() {
        if (self::$twig === null) {
            $loader = new FilesystemLoader(__DIR__ . '/../../resources/views');
            self::$twig = new Environment($loader, [
                'cache' => __DIR__ . '/../../storage/cache/twig',
                'debug' => true
            ]);

            self::$twig->addFunction(new TwigFunction('jsfile', function ($filename) {
                return JSLoader::load($filename);
            }, ['is_safe' => ['html']]));
        }
        
        return self::$twig;
    }



    public static function render(string $template, array $data = []) {
        // Inject CSRF token into all templates
        $data['csrf_token'] = Csrf::generateToken();
        return self::init()->render($template, $data);
    }
}
