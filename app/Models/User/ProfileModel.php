<?php
namespace App\Models\User;

class ProfileModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Get full profile details of the user
     */
    public function getUserDetails($userId) {
        $sql = "SELECT id, name, email, phone, profile_image, status, created_at FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update basic profile information (Name, Email, Phone)
     */
    public function updateBasicInfo($userId, $data) {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $data['name'], $data['email'], $data['phone'], $userId);
        return $stmt->execute();
    }

    /**
     * Update profile picture path
     */
    public function updateProfileImage($userId, $imagePath) {
        $sql = "UPDATE users SET profile_image = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $imagePath, $userId);
        return $stmt->execute();
    }

    /**
     * Securely update password
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $userId);
        return $stmt->execute();
    }

    /**
     * Verify if the current password is correct before allowing change
     */
    public function verifyPassword($userId, $currentPassword) {
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        
        return password_verify($currentPassword, $res['password']);
    }
}