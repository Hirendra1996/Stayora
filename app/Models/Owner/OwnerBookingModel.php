<?php
namespace App\Models\Owner;

use mysqli;

/**
 * OwnerBookingModel
 * Handles booking reservation retrieval and management for Farmhouse Owners.
 *
 * CRITICAL PRIVACY RULE:
 * Owners must NOT see the guest's phone number, email address, or direct contact information.
 * All guest contact is strictly mediated by the FarmLelo Admin Concierge.
 * Queries in this model strictly exclude user contact fields.
 */
class OwnerBookingModel {

    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Fetch paginated bookings for an owner's farmhouses with optional filtering and search.
     *
     * @param int    $ownerId
     * @param array  $filters ['status' => ..., 'farmhouse_id' => ..., 'q' => ...]
     * @param string $sortBy
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public function getBookingsByOwner(
        int $ownerId,
        array $filters = [],
        string $sortBy = 'newest',
        int $limit = 10,
        int $offset = 0
    ): array {
        // Privacy guaranteed: No u.phone, no u.email
        $sql = "SELECT 
                    br.id AS booking_id,
                    br.farmhouse_id,
                    br.booking_type,
                    br.room_type_id,
                    br.room_type_name,
                    br.price_per_room,
                    br.check_in,
                    br.check_in_time,
                    br.check_out,
                    br.check_out_time,
                    br.start_date,
                    br.end_date,
                    br.guests,
                    br.rooms,
                    br.price,
                    br.status,
                    br.message,
                    br.created_at,
                    f.title AS farmhouse_title,
                    f.location AS farmhouse_location,
                    f.category AS farmhouse_category,
                    (SELECT image_url FROM images i WHERE i.farmhouse_id = f.id ORDER BY i.id ASC LIMIT 1) AS farmhouse_thumb,
                    COALESCE(u.name, 'Guest') AS guest_name
                FROM booking_requests br
                JOIN farmhouses f ON br.farmhouse_id = f.id
                LEFT JOIN users u ON br.user_id = u.id
                WHERE f.owner_id = ?";

        $types = "i";
        $params = [$ownerId];

        // Status Filter
        if (!empty($filters['status']) && strtolower($filters['status']) !== 'all') {
            $status = strtolower(trim($filters['status']));
            // Support 'confirmed' alias for 'approved'
            if ($status === 'confirmed') {
                $status = 'approved';
            }
            $sql .= " AND br.status = ?";
            $types .= "s";
            $params[] = $status;
        }

        // Farmhouse Filter
        if (!empty($filters['farmhouse_id'])) {
            $fId = (int)$filters['farmhouse_id'];
            if ($fId > 0) {
                $sql .= " AND br.farmhouse_id = ?";
                $types .= "i";
                $params[] = $fId;
            }
        }

        // Search Query (Search by farm title, guest name, or booking ID)
        if (!empty($filters['q'])) {
            $q = trim($filters['q']);
            $sql .= " AND (LOWER(f.title) LIKE ? OR LOWER(f.location) LIKE ? OR LOWER(u.name) LIKE ? OR br.id = ?)";
            $types .= "ssss";
            $searchPattern = '%' . strtolower($q) . '%';
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $params[] = $q;
        }

        // Sorting
        $sql .= match($sortBy) {
            'oldest'     => " ORDER BY br.created_at ASC",
            'stay_date'  => " ORDER BY COALESCE(br.check_in, br.start_date) ASC",
            'price_high' => " ORDER BY br.price DESC",
            'price_low'  => " ORDER BY br.price ASC",
            default      => " ORDER BY br.created_at DESC",
        };

        $sql .= " LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("OwnerBookingModel::getBookingsByOwner prepare failed: " . $this->db->error);
            return [];
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Get count of filtered bookings for an owner
     */
    public function getTotalBookingCount(int $ownerId, array $filters = []): int {
        $sql = "SELECT COUNT(*) AS total
                FROM booking_requests br
                JOIN farmhouses f ON br.farmhouse_id = f.id
                LEFT JOIN users u ON br.user_id = u.id
                WHERE f.owner_id = ?";

        $types = "i";
        $params = [$ownerId];

        if (!empty($filters['status']) && strtolower($filters['status']) !== 'all') {
            $status = strtolower(trim($filters['status']));
            if ($status === 'confirmed') $status = 'approved';
            $sql .= " AND br.status = ?";
            $types .= "s";
            $params[] = $status;
        }

        if (!empty($filters['farmhouse_id'])) {
            $fId = (int)$filters['farmhouse_id'];
            if ($fId > 0) {
                $sql .= " AND br.farmhouse_id = ?";
                $types .= "i";
                $params[] = $fId;
            }
        }

        if (!empty($filters['q'])) {
            $q = trim($filters['q']);
            $sql .= " AND (LOWER(f.title) LIKE ? OR LOWER(f.location) LIKE ? OR LOWER(u.name) LIKE ? OR br.id = ?)";
            $types .= "ssss";
            $searchPattern = '%' . strtolower($q) . '%';
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $params[] = $q;
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return 0;

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Get aggregate booking stats for the owner
     */
    public function getOwnerBookingStats(int $ownerId): array {
        $stats = [
            'total'     => 0,
            'approved'  => 0, // Confirmed
            'pending'   => 0,
            'completed' => 0,
            'cancelled' => 0,
            'rejected'  => 0,
            'revenue'   => 0.0,
        ];

        $stmt = $this->db->prepare(
            "SELECT 
                br.status, 
                COUNT(br.id) AS cnt, 
                SUM(br.price) AS total_amount
             FROM booking_requests br
             JOIN farmhouses f ON br.farmhouse_id = f.id
             WHERE f.owner_id = ?
             GROUP BY br.status"
        );

        if ($stmt) {
            $stmt->bind_param("i", $ownerId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $st = strtolower($row['status'] ?? '');
                $cnt = (int)$row['cnt'];
                $stats['total'] += $cnt;
                if (array_key_exists($st, $stats)) {
                    $stats[$st] = $cnt;
                }
                if ($st === 'approved' || $st === 'completed') {
                    $stats['revenue'] += (float)($row['total_amount'] ?? 0);
                }
            }
        }

        return $stats;
    }

    /**
     * Get single booking detail dossier for an owner with ownership validation.
     * STRICT PRIVACY: Redacts phone and email from the result.
     *
     * @param int $bookingId
     * @param int $ownerId
     * @return array|null
     */
    public function getBookingDetailForOwner(int $bookingId, int $ownerId): ?array {
        $stmt = $this->db->prepare(
            "SELECT 
                br.id AS booking_id,
                br.farmhouse_id,
                br.booking_type,
                br.room_type_id,
                br.room_type_name,
                br.price_per_room,
                br.check_in,
                br.check_in_time,
                br.check_out,
                br.check_out_time,
                br.start_date,
                br.end_date,
                br.guests,
                br.rooms,
                br.price,
                br.status,
                br.message,
                br.created_at,
                f.title AS farmhouse_title,
                f.location AS farmhouse_location,
                f.address AS farmhouse_address,
                f.category AS farmhouse_category,
                f.price AS farmhouse_base_price,
                (SELECT image_url FROM images i WHERE i.farmhouse_id = f.id ORDER BY i.id ASC LIMIT 1) AS farmhouse_thumb,
                COALESCE(u.name, 'Guest') AS guest_name
             FROM booking_requests br
             JOIN farmhouses f ON br.farmhouse_id = f.id
             LEFT JOIN users u ON br.user_id = u.id
             WHERE br.id = ? AND f.owner_id = ?
             LIMIT 1"
        );

        if (!$stmt) return null;

        $stmt->bind_param("ii", $bookingId, $ownerId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Update booking status by Owner (with strict ownership check).
     * Owners can mark bookings as completed or cancelled.
     *
     * @param int    $bookingId
     * @param int    $ownerId
     * @param string $status ('approved', 'completed', 'cancelled')
     * @return bool
     */
    public function updateBookingStatusByOwner(int $bookingId, int $ownerId, string $status): bool {
        $allowedStatuses = ['approved', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        // Verify the booking belongs to this owner's farmhouse
        $checkStmt = $this->db->prepare(
            "SELECT br.id FROM booking_requests br 
             JOIN farmhouses f ON br.farmhouse_id = f.id 
             WHERE br.id = ? AND f.owner_id = ? LIMIT 1"
        );
        if (!$checkStmt) return false;

        $checkStmt->bind_param("ii", $bookingId, $ownerId);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();
        if ($checkRes->num_rows === 0) {
            return false; // Not authorized or booking does not exist
        }

        $stmt = $this->db->prepare("UPDATE booking_requests SET status = ? WHERE id = ?");
        if (!$stmt) return false;

        $stmt->bind_param("si", $status, $bookingId);
        return $stmt->execute();
    }

    /**
     * Get owner's farmhouses for filter dropdown
     */
    public function getOwnerFarmhousesList(int $ownerId): array {
        $stmt = $this->db->prepare("SELECT id, title FROM farmhouses WHERE owner_id = ? ORDER BY title ASC");
        if (!$stmt) return [];
        $stmt->bind_param("i", $ownerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
