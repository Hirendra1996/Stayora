<?php
/**
 * Farmlelo - Built-in PHP Development Server Router
 * 
 * Usage: php -S localhost:8000 router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$filePath = __DIR__ . $uri;

// If the requested URI corresponds to an existing static file, serve it directly
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// Otherwise, route through the application entry point
require_once __DIR__ . '/index.php';
