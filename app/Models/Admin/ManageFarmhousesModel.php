<?php
namespace App\Models\Admin;

use mysqli;

class ManageFarmhousesModel {
    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Get all farmhouses, optionally filtered by status
     */
    public function getAllFarmhouses($statusFilter = null) {
        // Query to grab farmhouse data + Owner Name + 1 thumbnail image attached to it
        $sql = "SELECT 
                    f.*, 
                    o.name as owner_name,
                    (SELECT image_url FROM images i WHERE i.farmhouse_id = f.id LIMIT 1) as thumb_url
                FROM farmhouses f
                LEFT JOIN owners o ON f.owner_id = o.id";
                
        // Safely apply status filtering
        if ($statusFilter && in_array($statusFilter, ['active', 'inactive'])) {
            $sql .= " WHERE f.status = ?";
            $sql .= " ORDER BY f.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) return []; 
            
            $stmt->bind_param("s", $statusFilter);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $sql .= " ORDER BY f.created_at DESC";
            $result = $this->db->query($sql);
            if (!$result) return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get overarching counts for the property dashboard
     */
    public function getFarmhouseStats() {
        $stats = ['total' => 0, 'active' => 0, 'inactive' => 0];
        
        $result = $this->db->query("SELECT status, COUNT(*) as count FROM farmhouses GROUP BY status");
        
        if ($result) {
            while($row = $result->fetch_assoc()) {
                $statusString = strtolower($row['status']);
                if (isset($stats[$statusString])) {
                    $stats[$statusString] = (int)$row['count'];
                }
                $stats['total'] += (int)$row['count'];
            }
        }
        return $stats;
    }

    /**
     * Toggle visibility (Active <-> Inactive)
     */
    public function updateStatus($id, $new_status) {
        if (!in_array($new_status, ['active', 'inactive'])) return false;
        
        $stmt = $this->db->prepare("UPDATE farmhouses SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $new_status, $id);
            return $stmt->execute();
        }
        return false;
    }

    /**
     * Delete farmhouse permanently
     * (Due to ON DELETE CASCADE, images/amenities attached to this farm will cleanly erase themselves)
     */
    public function deleteFarmhouse($id) {
        $stmt = $this->db->prepare("DELETE FROM farmhouses WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
        return false;
    }
}
?>