<?php
namespace App\Models\Admin;

use mysqli;

class InquiriesModel {
    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Get inquiries, optional filter by status
     */
    public function getAllInquiries($statusFilter = null) {
        $sql = "SELECT i.*, f.title as farmhouse_title 
                FROM inquiries i
                LEFT JOIN farmhouses f ON i.farmhouse_id = f.id ";
                
        // Safely apply status filtering
        if ($statusFilter && in_array($statusFilter, ['new', 'contacted', 'converted', 'closed'])) {
            $sql .= " WHERE i.status = ? ";
            $sql .= " ORDER BY i.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) return []; // Fallback if table doesn't exist
            
            $stmt->bind_param("s", $statusFilter);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $sql .= " ORDER BY i.created_at DESC";
            $result = $this->db->query($sql);
            if (!$result) return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get exact counts of each inquiry status for quick tab switching on UI
     */
    public function getInquiryStats() {
        $stats = ['all' => 0, 'new' => 0, 'contacted' => 0, 'converted' => 0, 'closed' => 0];
        
        $result = $this->db->query("SELECT status, COUNT(*) as count FROM inquiries GROUP BY status");
        
        if ($result) {
            while($row = $result->fetch_assoc()) {
                $statusString = strtolower($row['status']);
                if (isset($stats[$statusString])) {
                    $stats[$statusString] = (int)$row['count'];
                }
                $stats['all'] += (int)$row['count'];
            }
        }
        return $stats;
    }

    /**
     * Change status in pipeline ('new', 'contacted', 'converted', 'closed') and update notes
     */
    public function updateStatus($id, $new_status, $notes = null, $follow_up_date = null) {
        if (!in_array($new_status, ['new', 'contacted', 'converted', 'closed'])) return false;
        
        if ($notes !== null || $follow_up_date !== null) {
            $stmt = $this->db->prepare("UPDATE inquiries SET status = ?, notes = COALESCE(?, notes), follow_up_date = COALESCE(?, follow_up_date) WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("sssi", $new_status, $notes, $follow_up_date, $id);
                return $stmt->execute();
            }
        } else {
            $stmt = $this->db->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
            if($stmt) {
                $stmt->bind_param("si", $new_status, $id);
                return $stmt->execute();
            }
        }
        return false;
    }

    /**
     * Delete an inquiry forever
     */
    public function deleteInquiry($id) {
        $stmt = $this->db->prepare("DELETE FROM inquiries WHERE id = ?");
        if($stmt) {
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
        return false;
    }
}
?>