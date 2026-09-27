<?php
namespace App\Models\Admin;
use App\Helpers\CryptoHelper;
use mysqli;

class DashboardModel {
    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Get overarching counts for the dashboard cards
     */
    public function getStatistics() {
        $stats = [
            'total_farmhouses' => 0,
            'active_farmhouses' => 0,
            'total_owners' => 0,
            'new_inquiries' => 0,
            'pending_booking_requests' => 0,
            'active_bookings' => 0
        ];

        // Safe query helper for counts
        $getCount = function($query) {
            $result = $this->db->query($query);
            return $result ? (int) $result->fetch_row()[0] : 0;
        };

        $stats['total_farmhouses']         = $getCount("SELECT COUNT(*) FROM farmhouses");
        $stats['active_farmhouses']        = $getCount("SELECT COUNT(*) FROM farmhouses WHERE status = 'active'");
        $stats['total_owners']             = $getCount("SELECT COUNT(*) FROM owners");
        $stats['new_inquiries']            = $getCount("SELECT COUNT(*) FROM inquiries WHERE status = 'new'");
        $stats['pending_booking_requests'] = $getCount("SELECT COUNT(*) FROM booking_requests WHERE status = 'pending'");
        $stats['active_bookings']          = $getCount("SELECT COUNT(*) FROM bookings WHERE status = 'active'");

        return $stats;
    }

    /**
     * Get latest booking requests to show in a table
     */
    public function getRecentBookingRequests(int $limit = 6): array
    {
        $limit = max(1, min(50, $limit));
        $sql = "SELECT 
                    br.id,
                    br.farmhouse_id,
                    br.user_id,
                    br.check_in,
                    br.check_out,
                    br.start_date,
                    br.end_date,
                    br.guests,
                    br.rooms,
                    br.price,
                    br.status,
                    br.created_at,
                    COALESCE(u.name, 'Guest') AS guest_name,
                    COALESCE(u.phone, 'N/A') AS guest_phone,
                    COALESCE(u.email, '') AS guest_email,
                    f.title AS farmhouse_title,
                    f.location AS farmhouse_location
                FROM booking_requests br
                LEFT JOIN users u ON br.user_id = u.id
                LEFT JOIN farmhouses f ON br.farmhouse_id = f.id
                ORDER BY br.created_at DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Get latest leads/inquiries (Calls/Whatsapp)
     */
    public function getRecentInquiries($limit = 6) {
        $limit = (int) $limit;
        
        $sql = "SELECT i.*, f.title as farmhouse_title 
                FROM inquiries i
                LEFT JOIN farmhouses f ON i.farmhouse_id = f.id
                ORDER BY i.created_at DESC 
                LIMIT {$limit}";
                
        $result = $this->db->query($sql);
        
        if (!$result) {
            error_log("Database Error in getRecentInquiries: " . $this->db->error);
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get recently listed farmhouses with thumbnails and owner details
     */
    public function getRecentProperties($limit = 4): array
    {
        $limit = (int) $limit;
        $sql = "SELECT f.*, 
                       o.name AS owner_name,
                       (SELECT image_url FROM images i WHERE i.farmhouse_id = f.id LIMIT 1) AS thumb_url
                FROM farmhouses f
                LEFT JOIN owners o ON f.owner_id = o.id
                ORDER BY f.created_at DESC
                LIMIT {$limit}";

        $result = $this->db->query($sql);

        if (!$result) {
            error_log("Database Error in getRecentProperties: " . $this->db->error);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAllInquiries(): array
    {
        $sql = "SELECT id, full_name, phone, email, Message, created_at 
                FROM contact_inquiries 
                ORDER BY created_at DESC 
                LIMIT 6";
    
        $result = $this->db->query($sql);
    
        if (!$result) {
            error_log("ContactInquiriesModel::getAllInquiries() query failed: " . $this->db->error);
            return [];
        }
    
        $inquiries = [];
    
        while ($row = $result->fetch_assoc()) {
            $row['encrypted_id'] = CryptoHelper::encrypt((string)$row['id']);
            $inquiries[] = $row;
        }
    
        $result->free();
    
        return $inquiries;
    }
}
?>