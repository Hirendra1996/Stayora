<?php
namespace App\Models\Admin;

use mysqli;

class BookingModel {
    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Fetch bookings with Farmhouse details and the FIRST image
     */
    public function getFilteredBookings($status = 'all') {
        $sql = "SELECT b.*, COALESCE(f.title, CONCAT('Farmhouse #', b.farmhouse_id)) as title, COALESCE(f.location, '—') as location, 
                (SELECT image_url FROM images WHERE farmhouse_id = f.id LIMIT 1) as thumb
                FROM bookings b
                LEFT JOIN farmhouses f ON b.farmhouse_id = f.id";
        
        if ($status !== 'all') {
            $sql .= " WHERE b.status = '" . $this->db->real_escape_string($status) . "'";
        }
        
        $sql .= " ORDER BY b.start_date DESC";
        
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
}