<?php
namespace App\Models\User;

class BookingModel {
    private $db;

    public function __construct($dbConnection) {
        // $dbConnection is the mysqli object returned by Database::connect()
        $this->db = $dbConnection;
    }

    /**
     * Get ALL specific details for a single Booking to show in the view.
     * Joins with farmhouses to get title, price, location.
     * Grabs dynamic images and settings for Admin Contact Numbers.
     */
   public function getBookingDetails($bookingId) {
    $sql = "SELECT 
            br.*, 
            COALESCE(f.title, CONCAT('Farmhouse #', br.farmhouse_id)) AS farm_title, 
            COALESCE(f.location, '—') AS location, 
            COALESCE(f.price, br.price) AS price,
            (SELECT image_url FROM images WHERE farmhouse_id = f.id ORDER BY id ASC LIMIT 1) AS main_image,
            (SELECT mobile_number FROM site_settings LIMIT 1) AS admin_phone,
            (SELECT whatsapp_number FROM site_settings LIMIT 1) AS admin_whatsapp
        FROM booking_requests br
        LEFT JOIN farmhouses f ON br.farmhouse_id = f.id
        WHERE br.id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return ($result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    /**
     * Create a new booking request in the booking_requests table
     */
    public function createRequest($data) {
        $sql = "INSERT INTO booking_requests 
                (user_id, farmhouse_id, booking_type, check_in, check_in_time, check_out, check_out_time, guests, rooms, price, message, status, start_date, end_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $userId       = !empty($data['user_id']) ? (int)$data['user_id'] : null;
        $farmhouseId  = (int)$data['farmhouse_id'];
        $bookingType  = in_array(strtolower($data['booking_type'] ?? ''), ['complete', 'per_room'], true) ? strtolower($data['booking_type']) : 'complete';
        $checkIn      = $data['check_in'] ?? $data['start_date'] ?? date('Y-m-d');
        $checkInTime  = $data['check_in_time'] ?? null;
        $checkOut     = $data['check_out'] ?? $data['end_date'] ?? date('Y-m-d', strtotime('+1 day'));
        $checkOutTime = $data['check_out_time'] ?? null;
        $guests       = max(1, (int)($data['guests'] ?? 1));
        $rooms        = max(1, (int)($data['rooms'] ?? 1));
        $price        = (float)($data['price'] ?? 0);
        $message      = $data['message'] ?? null;
        $startDate    = $checkIn;
        $endDate      = $checkOut;

        $stmt->bind_param(
            "iisssssiidsss", 
            $userId, 
            $farmhouseId, 
            $bookingType,
            $checkIn,
            $checkInTime,
            $checkOut,
            $checkOutTime,
            $guests,
            $rooms,
            $price,
            $message,
            $startDate,
            $endDate
        );

        return $stmt->execute();
    }

    /**
     * Check if a farmhouse is available for selected dates
     * Checks against both 'booking_requests' (approved) and 'blocked_dates'
     */
    public function isAvailable($farmhouseId, $startDate, $endDate) {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM booking_requests WHERE farmhouse_id = ? AND status IN ('approved', 'completed') AND (COALESCE(check_in, start_date) < ? AND COALESCE(check_out, end_date) > ?)) AS booking_count,
                (SELECT COUNT(*) FROM blocked_dates WHERE farmhouse_id = ? AND (start_date < ? AND end_date > ?)) AS blocked_count";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("ississ", $farmhouseId, $endDate, $startDate, $farmhouseId, $endDate, $startDate);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return ($result['booking_count'] == 0 && $result['blocked_count'] == 0);
    }

    /**
     * Get bookings for a specific user to show in their dashboard
     */
    public function getBookingsByUser($userId) {
        $sql = "SELECT br.*, COALESCE(f.title, CONCAT('Farmhouse #', br.farmhouse_id)) as farmhouse_title, COALESCE(f.location, '—') as location,
                       (SELECT image_url FROM images WHERE farmhouse_id = f.id ORDER BY id ASC LIMIT 1) AS main_image
                FROM booking_requests br
                LEFT JOIN farmhouses f ON br.farmhouse_id = f.id
                WHERE br.user_id = ? 
                ORDER BY br.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get farmhouse details for the booking page
     */
    public function getFarmhouseById($id) {
        $sql = "SELECT * FROM farmhouses WHERE id = ? AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}