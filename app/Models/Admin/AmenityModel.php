<?php
namespace App\Models\Admin;

use mysqli;
use Exception;

/**
 * AmenityModel
 * Covers DB operations and analytics for amenities table.
 */
class AmenityModel {

    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Get all amenities with real-time farmhouse attachment usage counts
     */
    public function getAllAmenities(string $search = '', string $category = ''): array {
        $sql = "SELECT 
                    a.*, 
                    (SELECT COUNT(DISTINCT fa.farmhouse_id) 
                     FROM farmhouse_amenities fa 
                     WHERE fa.amenity_id = a.id) AS usage_count
                FROM amenities a";

        $where = [];

        if (!empty($category) && strtolower($category) !== 'all') {
            $cat = $this->db->real_escape_string(strtolower($category));
            $where[] = "LOWER(a.category) = '$cat'";
        }

        if (!empty($search)) {
            $q = $this->db->real_escape_string(strtolower($search));
            $where[] = "(LOWER(a.name) LIKE '%$q%' OR LOWER(a.icon_class) LIKE '%$q%')";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY a.category ASC, a.name ASC";

        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAmenityById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT a.*, 
                    (SELECT COUNT(DISTINCT fa.farmhouse_id) 
                     FROM farmhouse_amenities fa 
                     WHERE fa.amenity_id = a.id) AS usage_count 
             FROM amenities a 
             WHERE a.id = ?"
        );
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    public function isNameUnique(string $name, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM amenities WHERE LOWER(TRIM(name)) = LOWER(TRIM(?))";
        if ($excludeId) {
            $sql .= " AND id != ?";
        }
        $stmt = $this->db->prepare($sql);
        
        if ($excludeId) {
            $stmt->bind_param("si", $name, $excludeId);
        } else {
            $stmt->bind_param("s", $name);
        }
        
        $stmt->execute();
        return $stmt->get_result()->num_rows === 0;
    }

    public function createAmenity(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO amenities (name, category, icon_class) VALUES (?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("sss", $data['name'], $data['category'], $data['icon_class']);
        return $stmt->execute();
    }

    public function updateAmenity(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE amenities SET name = ?, category = ?, icon_class = ? WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("sssi", $data['name'], $data['category'], $data['icon_class'], $id);
        return $stmt->execute();
    }

    public function deleteAmenity(int $id): bool {
        $this->db->query("DELETE FROM farmhouse_amenities WHERE amenity_id = $id");
        $stmt = $this->db->prepare("DELETE FROM amenities WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function bulkDelete(array $ids): int {
        if (empty($ids)) return 0;
        $intIds = array_map('intval', $ids);
        $inList = implode(',', $intIds);
        $this->db->query("DELETE FROM farmhouse_amenities WHERE amenity_id IN ($inList)");
        $this->db->query("DELETE FROM amenities WHERE id IN ($inList)");
        return $this->db->affected_rows;
    }

    /**
     * KPI Analytics for Amenities
     */
    public function getAmenityStats(): array {
        $stats = [
            'total'         => 0,
            'total_equipped'=> 0,
            'top_amenity'   => 'None',
            'top_count'     => 0,
            'by_category'   => []
        ];

        $resCount = $this->db->query("SELECT category, COUNT(*) as c FROM amenities GROUP BY category");
        if ($resCount) {
            while ($row = $resCount->fetch_assoc()) {
                $cat = strtolower($row['category']);
                $stats['by_category'][$cat] = (int)$row['c'];
                $stats['total'] += (int)$row['c'];
            }
        }

        $resEquip = $this->db->query("SELECT COUNT(*) as c FROM farmhouse_amenities");
        if ($resEquip) {
            $stats['total_equipped'] = (int)($resEquip->fetch_assoc()['c'] ?? 0);
        }

        $resTop = $this->db->query(
            "SELECT a.name, COUNT(fa.id) as c 
             FROM farmhouse_amenities fa 
             JOIN amenities a ON fa.amenity_id = a.id 
             GROUP BY a.id, a.name 
             ORDER BY c DESC LIMIT 1"
        );
        if ($resTop && $resTop->num_rows > 0) {
            $row = $resTop->fetch_assoc();
            $stats['top_amenity'] = $row['name'];
            $stats['top_count']   = (int)$row['c'];
        }

        return $stats;
    }
}