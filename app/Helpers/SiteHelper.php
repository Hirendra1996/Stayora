<?php
namespace App\Helpers;

use App\Config\Database;

class SiteHelper {
    // This will hold our settings so we only query the DB once per page load
    private static $settings = null;

    public static function getSettings() {
        // If settings are already fetched, return them immediately
        if (self::$settings !== null) {
            return self::$settings;
        }

        // Otherwise, fetch from the database safely
        try {
            $db = Database::connect();
            $result = $db->query("SELECT * FROM site_settings LIMIT 1");
            
            if ($result && $result->num_rows > 0) {
                self::$settings = $result->fetch_assoc();
            } else {
                self::$settings = []; // Fallback empty array
            }
        } catch (\Throwable $e) {
            self::$settings = []; // Fallback if DB connection fails
        }

        return self::$settings;
    }
}