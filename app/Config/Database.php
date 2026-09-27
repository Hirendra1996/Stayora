<?php

namespace App\Config;

use mysqli;
use Exception;

// Enforce Indian Standard Time (IST – UTC+5:30) system-wide
if (date_default_timezone_get() !== 'Asia/Kolkata') {
    date_default_timezone_set('Asia/Kolkata');
}

class Database {
    public static function connect() {
        $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
        $user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
        $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? '');
        $name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'Stayora');
        $port = (int)(getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? 3306));

        // Validate environment variables
        if (!$host || !$user || !$name) {
            throw new Exception("Database configuration is incomplete. Please check your .env file.");
        }

        // Suppress direct error output — handle manually
        if (function_exists('mysqli_report')) {
            \mysqli_report(MYSQLI_REPORT_OFF);
        }

        $conn = @new mysqli($host, $user, $pass, $name, $port);

        if ($conn->connect_errno) {
            error_log("Database connection error: " . $conn->connect_error);
            $appDebug = getenv('APP_DEBUG') ?: ($_ENV['APP_DEBUG'] ?? 'false');
            if (filter_var($appDebug, FILTER_VALIDATE_BOOLEAN)) {
                throw new Exception("Database connection failed: (" . $conn->connect_errno . ") " . $conn->connect_error);
            }
            throw new Exception("Unable to connect to the database. Please try again later.");
        }

        // Use secure charset
        if (!$conn->set_charset("utf8mb4")) {
            error_log("Charset setting failed: " . $conn->error);
            throw new Exception("Internal database configuration error.");
        }

        // Enforce Indian Standard Time (IST – UTC+5:30) for database session
        $conn->query("SET time_zone = '+05:30'");

        return $conn;
    }
}
