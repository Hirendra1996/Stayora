<?php
// Enforce Indian Standard Time (IST – UTC+5:30) system-wide
date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/vendor/autoload.php';

// Safe load dotenv
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

// Populate environment variables
foreach ($_ENV as $key => $value) {
    if (is_scalar($value) && $key !== '') {
        putenv("{$key}={$value}");
    }
}

// Error reporting configuration
$appDebug = getenv('APP_DEBUG') ?: ($_ENV['APP_DEBUG'] ?? 'true');
if (filter_var($appDebug, FILTER_VALIDATE_BOOLEAN) !== false) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

// Load helpers
require_once __DIR__ . '/app/Helpers/Helper.php';

// Dispatch routing
require_once __DIR__ . '/app/routes.php';