<?php
namespace App\Models\Admin;

use mysqli;

class ProfileModel {
    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * ── ADMIN ACCOUNT LOGIC ──
     */
    public function getAdminData($id) {
        $stmt = $this->db->prepare("SELECT id, name, email, created_at FROM admins WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateAdminProfile($id, $name, $email) {
        $stmt = $this->db->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);
        return $stmt->execute();
    }

    public function updatePassword($id, $hashedPassword) {
        $stmt = $this->db->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);
        return $stmt->execute();
    }

    public function getHashedPassword($id) {
        $stmt = $this->db->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res['password'] ?? null;
    }

    /**
     * ── GLOBAL SITE SETTINGS LOGIC ──
     */
    public function getSiteSettings() {
        // Query modified from `settings` to `site_settings`
        $result = $this->db->query("SELECT * FROM site_settings LIMIT 1");
        return $result ? $result->fetch_assoc() : null;
    }

    public function updateSiteSettings($data) {
        // Updates mapped columns on the assumption the row ID is 1. Uses NOW() for the updated_at timestamp
        $sql = "UPDATE site_settings SET 
                mobile_number = ?, 
                whatsapp_number = ?, 
                email = ?, 
                address = ?, 
                google_map_link = ?, 
                facebook_link = ?, 
                instagram_link = ?, 
                site_name = ?, 
                footer_text = ?, 
                updated_at = NOW() 
                WHERE id = 1";

        $stmt = $this->db->prepare($sql);
        
        // 9 strings bound "s"
        $stmt->bind_param(
            "sssssssss", 
            $data['mobile_number'], 
            $data['whatsapp_number'], 
            $data['email'], 
            $data['address'], 
            $data['google_map_link'], 
            $data['facebook_link'], 
            $data['instagram_link'], 
            $data['site_name'], 
            $data['footer_text']
        );
        
        return $stmt->execute();
    }
    
    // NOTE: If you add functionality later to update logos/favicon separately:
    public function updateSiteLogos($logoPath, $faviconPath) {
        $stmt = $this->db->prepare("UPDATE site_settings SET logo = ?, favicon = ?, updated_at = NOW() WHERE id = 1");
        $stmt->bind_param("ss", $logoPath, $faviconPath);
        return $stmt->execute();
    }
}