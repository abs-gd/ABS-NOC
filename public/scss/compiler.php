<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use ScssPhp\ScssPhp\Compiler;

$scssFile = __DIR__ . '/main.scss';
$cssFile = __DIR__ . '/../css/style.css';
$cacheFile = __DIR__ . '/../css/style.cache';

// Detect environment (default to 'production' if not set)
$env = getenv('APP_ENV') ?: 'production';

// Get last modified time of SCSS files (including partials)
function getLatestModifiedTime($dir) {
    $latestTime = 0;
    foreach (glob($dir . '/*.scss') as $file) {
        $fileTime = filemtime($file);
        if ($fileTime > $latestTime) {
            $latestTime = $fileTime;
        }
    }
    return $latestTime;
}

$lastModified = getLatestModifiedTime(__DIR__);
$lastCached = file_exists($cacheFile) ? (int) file_get_contents($cacheFile) : 0;

if ($lastModified > $lastCached || $env === 'development') {
    try {
        $compiler = new Compiler();
        $compiler->setImportPaths(__DIR__);

        // Optimize for production
        if ($env === 'production') {
            $compiledCss = $compiler->compileString(file_get_contents($scssFile), 'compressed')->getCss();
        } else {
            $compiledCss = $compiler->compileString(file_get_contents($scssFile))->getCss();
        }

        file_put_contents($cssFile, $compiledCss);
        file_put_contents($cacheFile, $lastModified);
    } catch (Exception $e) {
        die("SCSS Compilation Error: " . $e->getMessage());
    }
}

// Serve the compiled CSS
header("Content-Type: text/css");
readfile($cssFile);