<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use ScssPhp\ScssPhp\Compiler;

$scssFile = __DIR__ . '/main.scss';
$cssFile = __DIR__ . '/../css/style.css';
$cacheFile = __DIR__ . '/../css/style.cache';

// Debugging: Check if SCSS file exists
if (!file_exists($scssFile)) {
    die("Error: SCSS file not found!");
}

// Get last modified time of SCSS files (including imports)
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

$lastModified = getLatestModifiedTime(__DIR__); // Get latest timestamp from all SCSS files
$lastCached = file_exists($cacheFile) ? (int) file_get_contents($cacheFile) : 0;

// Debugging: Log modification times
error_log("SCSS Last Modified: " . $lastModified);
error_log("Cache Timestamp: " . $lastCached);

// Force recompile if SCSS changed
if ($lastModified > $lastCached) {
    try {
        $compiler = new Compiler();
        $compiler->setImportPaths(__DIR__);
        $compiledCss = $compiler->compileString(file_get_contents($scssFile))->getCss();

        file_put_contents($cssFile, $compiledCss);
        file_put_contents($cacheFile, $lastModified); // Store new timestamp

        error_log("SCSS Recompiled: " . date("Y-m-d H:i:s"));
    } catch (Exception $e) {
        die("SCSS Compilation Error: " . $e->getMessage());
    }
}

// Serve the compiled CSS
header("Content-Type: text/css");
readfile($cssFile);