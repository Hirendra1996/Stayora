<?php
namespace App\Models\Admin;

use mysqli;
use Exception;

class SettingsModel {

    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Fetch the site_settings record (id = 1)
     */
    public function getSettings(): array {
        // Ensure SEO columns exist
        $check = $this->db->query("SHOW COLUMNS FROM site_settings LIKE 'meta_keywords'");
        if ($check && $check->num_rows === 0) {
            @$this->db->query("ALTER TABLE site_settings 
                ADD COLUMN meta_keywords TEXT NULL AFTER meta_description,
                ADD COLUMN google_site_verification VARCHAR(255) NULL AFTER meta_keywords,
                ADD COLUMN bing_site_verification VARCHAR(255) NULL AFTER google_site_verification");
        }

        $res = $this->db->query("SELECT * FROM site_settings WHERE id = 1 LIMIT 1");
        if ($res && $res->num_rows > 0) {
            return $res->fetch_assoc();
        }

        // Fallback: create default record if missing
        $this->db->query("INSERT INTO site_settings (id, site_name, mobile_number, whatsapp_number, email) VALUES (1, 'FarmLelo', '8889000399', '8889000399', 'contact@farmlelo.com')");
        $res2 = $this->db->query("SELECT * FROM site_settings WHERE id = 1 LIMIT 1");
        return $res2 ? $res2->fetch_assoc() : [];
    }

    /**
     * Update all settings fields
     */
    public function updateSettings(array $data): bool {
        // Ensure SEO columns exist before saving
        $check = $this->db->query("SHOW COLUMNS FROM site_settings LIKE 'meta_keywords'");
        if ($check && $check->num_rows === 0) {
            @$this->db->query("ALTER TABLE site_settings 
                ADD COLUMN meta_keywords TEXT NULL AFTER meta_description,
                ADD COLUMN google_site_verification VARCHAR(255) NULL AFTER meta_keywords,
                ADD COLUMN bing_site_verification VARCHAR(255) NULL AFTER google_site_verification");
        }

        $fields = [
            'site_name',
            'tagline',
            'mobile_number',
            'whatsapp_number',
            'email',
            'address',
            'google_map_link',
            'facebook_link',
            'instagram_link',
            'youtube_link',
            'twitter_link',
            'upi_id',
            'payment_instructions',
            'footer_text',
            'meta_description',
            'meta_keywords',
            'google_site_verification',
            'bing_site_verification'
        ];

        $setClauses = [];
        $params     = [];
        $types      = '';

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $setClauses[] = "$field = ?";
                $params[]     = $data[$field];
                $types       .= 's';
            }
        }

        // Handle uploaded image filenames if provided
        if (!empty($data['logo'])) {
            $setClauses[] = "logo = ?";
            $params[]     = $data['logo'];
            $types       .= 's';
        }
        if (!empty($data['favicon'])) {
            $setClauses[] = "favicon = ?";
            $params[]     = $data['favicon'];
            $types       .= 's';
        }
        if (!empty($data['payment_qr_code'])) {
            $setClauses[] = "payment_qr_code = ?";
            $params[]     = $data['payment_qr_code'];
            $types       .= 's';
        }

        if (empty($setClauses)) {
            return true;
        }

        $sql = "UPDATE site_settings SET " . implode(', ', $setClauses) . " WHERE id = 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    /**
     * Clear a specific media column (logo, favicon, payment_qr_code)
     */
    public function clearImage(string $field): bool {
        $allowed = ['logo', 'favicon', 'payment_qr_code'];
        if (!in_array($field, $allowed, true)) return false;

        $stmt = $this->db->prepare("UPDATE site_settings SET $field = NULL WHERE id = 1");
        if (!$stmt) return false;
        return $stmt->execute();
    }
}
