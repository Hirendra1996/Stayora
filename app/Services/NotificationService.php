<?php
namespace App\Services;

use App\Config\Database;
use mysqli;

class NotificationService {
    private mysqli $db;

    public function __construct(?mysqli $db = null) {
        $this->db = $db ?? Database::connect();
    }

    /**
     * Dispatch an in-app notification
     */
    public function notify(?int $userId, string $userType, string $title, string $message, ?string $link = null, string $type = 'info'): bool {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, user_type, title, message, link, type, is_read, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 0, NOW())
        ");
        if (!$stmt) return false;
        $stmt->bind_param("isssss", $userId, $userType, $title, $message, $link, $type);
        return $stmt->execute();
    }

    /**
     * Get unread count for current user
     */
    public function getUnreadCount(?int $userId, string $userType): int {
        if ($userId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND user_type = ? AND is_read = 0");
            $stmt->bind_param("is", $userId, $userType);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM notifications WHERE user_type = ? AND is_read = 0");
            $stmt->bind_param("s", $userType);
        }
        if (!$stmt) return 0;
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['cnt'] ?? 0);
    }

    /**
     * Get recent notifications
     */
    public function getRecent(?int $userId, string $userType, int $limit = 15): array {
        if ($userId) {
            $stmt = $this->db->prepare("
                SELECT * FROM notifications 
                WHERE (user_id = ? OR user_id IS NULL) AND user_type = ?
                ORDER BY created_at DESC LIMIT ?
            ");
            $stmt->bind_param("isi", $userId, $userType, $limit);
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM notifications 
                WHERE user_type = ?
                ORDER BY created_at DESC LIMIT ?
            ");
            $stmt->bind_param("si", $userType, $limit);
        }
        if (!$stmt) return [];
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(int $notificationId): bool {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $notificationId);
        return $stmt->execute();
    }

    /**
     * Mark all as read for current user
     */
    public function markAllAsRead(?int $userId, string $userType): bool {
        if ($userId) {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE (user_id = ? OR user_id IS NULL) AND user_type = ?");
            $stmt->bind_param("is", $userId, $userType);
        } else {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_type = ?");
            $stmt->bind_param("s", $userType);
        }
        if (!$stmt) return false;
        return $stmt->execute();
    }
}
