<?php
namespace App\Models;
use App\Config\Database;

class WishlistModel {

    public static function isWishlisted($userId, $farmhouseId) {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND farmhouse_id = ?");
        $stmt->bind_param("ii", $userId, $farmhouseId);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $conn->close();
        return $exists;
    }

    public static function toggleWishlist($userId, $farmhouseId) {
        $db = Database::connect();
        
        $check = $db->prepare("SELECT id FROM wishlist WHERE user_id = ? AND farmhouse_id = ?");
        if (!$check) die("SQL ERROR: " . $db->error);

        $check->bind_param("ii", $userId, $farmhouseId);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;

        if ($exists) {
            $stmt = $db->prepare("DELETE FROM wishlist WHERE user_id = ? AND farmhouse_id = ?");
            $stmt->bind_param("ii", $userId, $farmhouseId);
            $stmt->execute();
            $status = 'removed';
        } else {
            $stmt = $db->prepare("INSERT INTO wishlist (user_id, farmhouse_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $farmhouseId);
            $stmt->execute();
            $status = 'added';
        }

        $db->close();
        return ['status' => $status];
    }

    public static function getUserWishlist($userId) {
        $db = Database::connect();
        $sql = "SELECT f.* FROM farmhouses f 
                JOIN wishlist w ON f.id = w.farmhouse_id 
                WHERE w.user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}