<?php
namespace App\Models\User;

class DashboardModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Get basic stats for the user dashboard tiles
     */
    public function getStats($userId) {
        $stats = [
            'total_bookings' => 0,
            'pending_requests' => 0,
            'approved_requests' => 0,
            'wishlist_count' => 0
        ];

        // Total bookings (confirmed / approved reservations)
        $stmt1 = $this->db->prepare("SELECT COUNT(*) as total FROM booking_requests WHERE user_id = ? AND status = 'approved'");
        if ($stmt1) {
            $stmt1->bind_param("i", $userId);
            $stmt1->execute();
            $stats['total_bookings'] = (int)($stmt1->get_result()->fetch_assoc()['total'] ?? 0);
        }

        // Pending requests
        $stmt2 = $this->db->prepare("SELECT COUNT(*) as total FROM booking_requests WHERE user_id = ? AND status = 'pending'");
        if ($stmt2) {
            $stmt2->bind_param("i", $userId);
            $stmt2->execute();
            $stats['pending_requests'] = (int)($stmt2->get_result()->fetch_assoc()['total'] ?? 0);
        }

        // Approved requests
        $stmt3 = $this->db->prepare("SELECT COUNT(*) as total FROM booking_requests WHERE user_id = ? AND status = 'approved'");
        if ($stmt3) {
            $stmt3->bind_param("i", $userId);
            $stmt3->execute();
            $stats['approved_requests'] = (int)($stmt3->get_result()->fetch_assoc()['total'] ?? 0);
        }

        // Wishlist Count
        $stmt4 = $this->db->prepare("SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?");
        if ($stmt4) {
            $stmt4->bind_param("i", $userId);
            $stmt4->execute();
            $stats['wishlist_count'] = (int)($stmt4->get_result()->fetch_assoc()['total'] ?? 0);
        }

        return $stats;
    }

    /**
     * Get ALL booking requests with full details and farmhouse names
     */
    public function getAllRequests($userId) {
        $sql = "SELECT br.id, br.check_in, br.check_out, br.guests, br.price, br.status, br.created_at, br.start_date, br.end_date, br.message, 
                       COALESCE(f.title, CONCAT('Farmhouse #', br.farmhouse_id)) as farmhouse_name, COALESCE(f.location, '—') as location, COALESCE(f.category, 'Farmhouse') as farmhouse_category,
                       (SELECT image_url FROM images WHERE farmhouse_id = f.id ORDER BY id ASC LIMIT 1) as main_image
                FROM booking_requests br
                LEFT JOIN farmhouses f ON br.farmhouse_id = f.id
                WHERE br.user_id = ?
                ORDER BY br.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get user's wishlisted farmhouses
     */
    public function getUserWishlist($userId) {
        $sql = "SELECT w.id as wishlist_id, w.farmhouse_id, 
                       COALESCE(f.title, CONCAT('Farmhouse #', w.farmhouse_id)) as farmhouse_name, COALESCE(f.location, '—') as location, COALESCE(f.category, 'Farmhouse') as farmhouse_category, COALESCE(f.price, 0) as farmhouse_price,
                       (SELECT image_url FROM images WHERE farmhouse_id = f.id ORDER BY id ASC LIMIT 1) as main_image
                FROM wishlist w
                LEFT JOIN farmhouses f ON w.farmhouse_id = f.id
                WHERE w.user_id = ?
                ORDER BY w.id DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get current profile data
     */
    public function getUserInfo($userId) {
        $sql = "SELECT name, email, phone, profile_image, status FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}