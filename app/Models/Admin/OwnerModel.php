<?php
namespace App\Models\Admin;

use mysqli;

class OwnerModel {
    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Get all owners with their farmhouse counts and presence
     */
    public function getAllOwners(): array {
        $sql = "SELECT o.id, o.name, o.email, o.phone, o.status, o.is_logged_in, 
                       o.profile_image, o.created_at, o.last_login, o.token_expiry,
                       CASE
                           WHEN o.is_logged_in = 1 
                           AND (
                               (o.remember_token IS NOT NULL AND o.token_expiry IS NOT NULL AND o.token_expiry > NOW())
                                OR (o.last_login IS NOT NULL AND o.last_login >= NOW() - INTERVAL 30 MINUTE)
                           )
                           THEN 1
                           ELSE 0
                       END AS is_online,
                       (SELECT COUNT(*) FROM farmhouses f WHERE f.owner_id = o.id) as property_count,
                       (SELECT COUNT(*) FROM booking_requests br INNER JOIN farmhouses f ON br.farmhouse_id = f.id WHERE f.owner_id = o.id) as total_bookings_received
                FROM owners o 
                ORDER BY o.created_at DESC";
        
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Get a single owner's detailed information by ID
     */
    public function getOwnerById(int $id): ?array {
        $sql = "SELECT o.id, o.name, o.email, o.phone, o.status, o.is_logged_in, 
                       o.profile_image, o.created_at, o.last_login,
                       CASE
                           WHEN o.is_logged_in = 1 
                           AND (
                               (o.remember_token IS NOT NULL AND o.token_expiry IS NOT NULL AND o.token_expiry > NOW())
                                OR (o.last_login IS NOT NULL AND o.last_login >= NOW() - INTERVAL 30 MINUTE)
                           )
                           THEN 1
                           ELSE 0
                       END AS is_online,
                       (SELECT COUNT(*) FROM farmhouses f WHERE f.owner_id = o.id) as property_count 
                FROM owners o 
                WHERE o.id = ?";
                
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    /**
     * Get Comprehensive Owner Dossier (Profile + List of Properties + Performance)
     */
    public function getOwnerDetails(int $id): ?array {
        $owner = $this->getOwnerById($id);
        if (!$owner) {
            return null;
        }

        // Clean sensitive data
        unset($owner['password'], $owner['remember_token'], $owner['reset_token']);

        // Fetch all properties listed under this owner
        $propStmt = $this->db->prepare(
            "SELECT 
                f.id,
                f.title,
                f.location,
                f.price,
                f.status,
                f.bedrooms,
                f.day_capacity,
                f.night_capacity,
                f.created_at,
                (SELECT img.image_url FROM images img WHERE img.farmhouse_id = f.id ORDER BY img.id ASC LIMIT 1) AS thumb_url,
                (SELECT COUNT(*) FROM booking_requests br WHERE br.farmhouse_id = f.id) AS booking_count
             FROM farmhouses f
             WHERE f.owner_id = ?
             ORDER BY f.created_at DESC"
        );
        $propStmt->bind_param("i", $id);
        $propStmt->execute();
        $properties = $propStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calculate total bookings across all their properties
        $totalBookings = 0;
        foreach ($properties as $p) {
            $totalBookings += (int) ($p['booking_count'] ?? 0);
        }

        return [
            'owner'          => $owner,
            'properties'     => $properties,
            'total_bookings' => $totalBookings,
        ];
    }

    /**
     * Add a new owner
     */
    public function createOwner(array $data): bool {
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $status         = in_array($data['status'] ?? '', ['active', 'disabled']) ? $data['status'] : 'active';

        $stmt = $this->db->prepare("INSERT INTO owners (name, email, phone, password, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $data['name'], $data['email'], $data['phone'], $hashedPassword, $status);
        return $stmt->execute();
    }

    /**
     * Update an existing owner's info
     */
    public function updateOwner(int $id, array $data): bool {
        $status = in_array($data['status'] ?? '', ['active', 'disabled']) ? $data['status'] : 'active';

        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("UPDATE owners SET name=?, email=?, phone=?, status=?, password=? WHERE id=?");
            $stmt->bind_param("sssssi", $data['name'], $data['email'], $data['phone'], $status, $hashedPassword, $id);
        } else {
            $stmt = $this->db->prepare("UPDATE owners SET name=?, email=?, phone=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $data['name'], $data['email'], $data['phone'], $status, $id);
        }
        return $stmt->execute();
    }

    /**
     * Toggle owner status (active/disabled)
     */
    public function updateStatus(int $id, string $status): bool {
        $validStatus = ($status === 'active') ? 'active' : 'disabled';
        $stmt = $this->db->prepare("UPDATE owners SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $validStatus, $id);
        $res = $stmt->execute();
        
        if ($validStatus === 'disabled') {
            $this->forceLogout($id);
        }
        return $res;
    }

    /**
     * Bulk update owner statuses
     */
    public function bulkUpdateStatus(array $ids, string $status): int {
        if (empty($ids)) return 0;
        $validStatus = ($status === 'active') ? 'active' : 'disabled';
        $intIds = array_map('intval', $ids);
        $inList = implode(',', $intIds);
        
        $sql = "UPDATE owners SET status = '$validStatus' WHERE id IN ($inList)";
        $this->db->query($sql);
        
        if ($validStatus === 'disabled') {
            $this->db->query("UPDATE owners SET remember_token = NULL, token_expiry = NULL, is_logged_in = 0 WHERE id IN ($inList)");
        }
        return $this->db->affected_rows;
    }

    /**
     * Bulk delete owners
     */
    public function bulkDelete(array $ids): int {
        if (empty($ids)) return 0;
        $intIds = array_map('intval', $ids);
        $inList = implode(',', $intIds);
        
        // Unassign farmhouses from deleted owners
        $this->db->query("UPDATE farmhouses SET owner_id = NULL WHERE owner_id IN ($inList)");
        
        $this->db->query("DELETE FROM owners WHERE id IN ($inList)");
        return $this->db->affected_rows;
    }

    /**
     * Force Logout owner
     */
    public function forceLogout(int $id): bool {
        $stmt = $this->db->prepare(
            "UPDATE owners
             SET remember_token = NULL,
                 token_expiry = NULL,
                 is_logged_in = 0
             WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Delete an owner (and gracefully unassign their properties)
     */
    public function deleteOwner(int $id): bool {
        $this->db->query("UPDATE farmhouses SET owner_id = NULL WHERE owner_id = " . (int)$id);
        $stmt = $this->db->prepare("DELETE FROM owners WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Get overarching owner statistics
     */
    public function getOwnerStats(): array {
        $stats = [
            'total'            => 0, 
            'active'           => 0, 
            'disabled'         => 0,
            'total_properties' => 0
        ];
        
        $result = $this->db->query("SELECT status, COUNT(*) as count FROM owners GROUP BY status");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $s = strtolower($row['status']);
                if (isset($stats[$s])) $stats[$s] = (int)$row['count'];
                $stats['total'] += (int)$row['count'];
            }
        }

        // Total properties owned by all owners
        $propRes = $this->db->query("SELECT COUNT(*) as count FROM farmhouses WHERE owner_id IS NOT NULL");
        if ($propRes) {
            $stats['total_properties'] = (int) ($propRes->fetch_assoc()['count'] ?? 0);
        }

        return $stats;
    }
}