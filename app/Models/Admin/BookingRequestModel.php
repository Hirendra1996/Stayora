<?php
namespace App\Models\Admin;

use mysqli;

class BookingRequestModel {
    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Fetch filtered, searched, and sorted booking requests with user and farmhouse details
     */
    public function getAllRequests(array $filters = [], string $sortBy = 'newest', int $limit = 10, int $offset = 0): array {
        $sql = "SELECT 
                    br.id AS req_id,
                    br.user_id,
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
                    br.status AS req_status,
                    br.message,
                    br.created_at AS req_date,
                    COALESCE(u.name, br.message, 'Guest User') AS cust_name,
                    u.email AS cust_email,
                    u.phone AS cust_phone,
                    u.gender AS cust_gender,
                    u.profile_image AS cust_avatar,
                    COALESCE(f.title, CONCAT('Farmhouse #', br.farmhouse_id)) AS farm_title,
                    COALESCE(f.location, '—') AS farm_location,
                    COALESCE(f.category, 'Farmhouse') AS farm_category,
                    COALESCE(f.price, br.price) AS farm_price_per_night,
                    (SELECT image_url FROM images i WHERE i.farmhouse_id = f.id ORDER BY i.id ASC LIMIT 1) AS farm_thumb,
                    o.name AS host_name,
                    o.phone AS host_phone,
                    o.email AS host_email
                FROM booking_requests br
                LEFT JOIN farmhouses f ON br.farmhouse_id = f.id
                LEFT JOIN users u ON br.user_id = u.id
                LEFT JOIN owners o ON f.owner_id = o.id";

        $where = [];

        // Status Filter
        if (!empty($filters['status']) && strtolower($filters['status']) !== 'all' && $filters['status'] !== 'All Statuses') {
            $status = $this->db->real_escape_string(strtolower($filters['status']));
            $where[] = "br.status = '$status'";
        }

        // Farmhouse Filter
        if (!empty($filters['farmhouse_id'])) {
            $fId = (int)$filters['farmhouse_id'];
            $where[] = "br.farmhouse_id = $fId";
        }

        // Search Query
        if (!empty($filters['q'])) {
            $q = $this->db->real_escape_string(strtolower($filters['q']));
            $where[] = "(LOWER(COALESCE(u.name, '')) LIKE '%$q%' OR LOWER(COALESCE(u.email, '')) LIKE '%$q%' OR COALESCE(u.phone, '') LIKE '%$q%' OR LOWER(COALESCE(f.title, '')) LIKE '%$q%' OR LOWER(COALESCE(f.location, '')) LIKE '%$q%' OR br.id = '$q')";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // Sorting
        $sql .= match($sortBy) {
            'oldest'      => " ORDER BY br.created_at ASC",
            'stay_date'   => " ORDER BY COALESCE(br.check_in, br.start_date) ASC",
            'price_high'  => " ORDER BY COALESCE(br.price, f.price) DESC",
            'price_low'   => " ORDER BY COALESCE(br.price, f.price) ASC",
            'guests_high' => " ORDER BY br.guests DESC",
            default       => " ORDER BY br.created_at DESC",
        };

        $sql .= " LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];

        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get count of filtered requests
     */
    public function getTotalRequestCount(array $filters = []): int {
        $sql = "SELECT COUNT(*) as total 
                FROM booking_requests br 
                LEFT JOIN farmhouses f ON br.farmhouse_id = f.id
                LEFT JOIN users u ON br.user_id = u.id";

        $where = [];

        if (!empty($filters['status']) && strtolower($filters['status']) !== 'all' && $filters['status'] !== 'All Statuses') {
            $status = $this->db->real_escape_string(strtolower($filters['status']));
            $where[] = "br.status = '$status'";
        }

        if (!empty($filters['farmhouse_id'])) {
            $fId = (int)$filters['farmhouse_id'];
            $where[] = "br.farmhouse_id = $fId";
        }

        if (!empty($filters['q'])) {
            $q = $this->db->real_escape_string(strtolower($filters['q']));
            $where[] = "(LOWER(COALESCE(u.name, '')) LIKE '%$q%' OR LOWER(COALESCE(u.email, '')) LIKE '%$q%' OR COALESCE(u.phone, '') LIKE '%$q%' OR LOWER(COALESCE(f.title, '')) LIKE '%$q%' OR LOWER(COALESCE(f.location, '')) LIKE '%$q%' OR br.id = '$q')";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $result = $this->db->query($sql);
        return $result ? (int)$result->fetch_assoc()['total'] : 0;
    }

    /**
     * Get Single Comprehensive Booking Dossier by ID
     */
    public function getBookingDetails(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT 
                br.id AS req_id,
                br.user_id,
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
                br.status AS req_status,
                br.message,
                br.created_at AS req_date,
                COALESCE(u.name, 'Guest User') AS cust_name,
                u.email AS cust_email,
                u.phone AS cust_phone,
                u.gender AS cust_gender,
                u.profile_image AS cust_avatar,
                (SELECT COUNT(*) FROM booking_requests sub WHERE sub.user_id = u.id) AS cust_total_bookings,
                COALESCE(f.title, CONCAT('Farmhouse #', br.farmhouse_id)) AS farm_title,
                COALESCE(f.location, '—') AS farm_location,
                COALESCE(f.address, '—') AS farm_address,
                COALESCE(f.category, 'Farmhouse') AS farm_category,
                f.price AS farm_price_per_night,
                f.bedrooms AS farm_bedrooms,
                f.day_capacity,
                f.night_capacity,
                (SELECT image_url FROM images i WHERE i.farmhouse_id = f.id ORDER BY i.id ASC LIMIT 1) AS farm_thumb,
                o.name AS host_name,
                o.phone AS host_phone,
                o.email AS host_email
            FROM booking_requests br
            LEFT JOIN farmhouses f ON br.farmhouse_id = f.id
            LEFT JOIN users u ON br.user_id = u.id
            LEFT JOIN owners o ON f.owner_id = o.id
            WHERE br.id = ?
            LIMIT 1"
        );
        if (!$stmt) return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Update single request status
     */
    public function updateStatus(int $id, string $status): bool {
        $valid = ['pending', 'approved', 'rejected', 'completed', 'cancelled'];
        if (!in_array($status, $valid, true)) return false;

        $stmt = $this->db->prepare("UPDATE booking_requests SET status = ? WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $ids, string $status): int {
        $valid = ['pending', 'approved', 'rejected', 'completed', 'cancelled'];
        if (empty($ids) || !in_array($status, $valid, true)) return 0;

        $intIds = array_map('intval', $ids);
        $inList = implode(',', $intIds);
        $safeStatus = $this->db->real_escape_string($status);
        $this->db->query("UPDATE booking_requests SET status = '$safeStatus' WHERE id IN ($inList)");
        return $this->db->affected_rows;
    }

    /**
     * Bulk delete requests
     */
    public function bulkDelete(array $ids): int {
        if (empty($ids)) return 0;
        $intIds = array_map('intval', $ids);
        $inList = implode(',', $intIds);
        $this->db->query("DELETE FROM booking_requests WHERE id IN ($inList)");
        return $this->db->affected_rows;
    }

    /**
     * Delete a single request
     */
    public function deleteRequest(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM booking_requests WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Overall Statistics & KPI calculations
     */
    public function getBookingStats(): array {
        $stats = [
            'total'            => 0,
            'pending'          => 0,
            'approved'         => 0,
            'completed'        => 0,
            'cancelled'        => 0,
            'rejected'         => 0,
            'pipeline_revenue' => 0,
            'approved_revenue' => 0
        ];

        $res = $this->db->query("SELECT status, COUNT(*) as count, SUM(price) as rev FROM booking_requests GROUP BY status");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $s = strtolower($row['status']);
                if (array_key_exists($s, $stats)) {
                    $stats[$s] = (int)$row['count'];
                }
                $stats['total'] += (int)$row['count'];
                if ($s === 'approved' || $s === 'completed') {
                    $stats['approved_revenue'] += (float)($row['rev'] ?? 0);
                } elseif ($s === 'pending') {
                    $stats['pipeline_revenue'] += (float)($row['rev'] ?? 0);
                }
            }
        }
        return $stats;
    }

    /**
     * Distinct farmhouses list for filter dropdown
     */
    public function getFarmhousesList(): array {
        $res = $this->db->query("SELECT id, title, location FROM farmhouses ORDER BY title ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Get all blocked date ranges for Admin (including both owner-locked and admin-locked)
     */
    public function getAllBlockedDates(array $filters = []): array {
        $sql = "SELECT 
                    bd.id,
                    bd.farmhouse_id,
                    bd.start_date,
                    bd.end_date,
                    bd.reason,
                    bd.locked_by,
                    f.title AS farm_title,
                    f.location AS farm_location,
                    o.name AS owner_name,
                    o.phone AS owner_phone
                FROM blocked_dates bd
                JOIN farmhouses f ON bd.farmhouse_id = f.id
                LEFT JOIN owners o ON f.owner_id = o.id";

        $where = [];

        if (!empty($filters['farmhouse_id'])) {
            $fId = (int)$filters['farmhouse_id'];
            $where[] = "bd.farmhouse_id = $fId";
        }

        if (!empty($filters['locked_by']) && in_array(strtolower($filters['locked_by']), ['owner', 'admin'], true)) {
            $lockedBy = $this->db->real_escape_string(strtolower($filters['locked_by']));
            $where[] = "bd.locked_by = '$lockedBy'";
        }

        if (!empty($filters['q'])) {
            $q = $this->db->real_escape_string(strtolower($filters['q']));
            $where[] = "(LOWER(f.title) LIKE '%$q%' OR LOWER(o.name) LIKE '%$q%' OR LOWER(bd.reason) LIKE '%$q%')";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY bd.start_date ASC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Admin lock dates for any farmhouse
     */
    public function addBlockedDate(int $farmhouseId, string $startDate, string $endDate, string $reason = '', string $lockedBy = 'admin'): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO blocked_dates (farmhouse_id, start_date, end_date, reason, locked_by)
             VALUES (?, ?, ?, ?, ?)"
        );
        if (!$stmt) return false;
        $stmt->bind_param("issss", $farmhouseId, $startDate, $endDate, $reason, $lockedBy);
        return $stmt->execute();
    }

    /**
     * Admin delete/unlock any blocked date record
     */
    public function deleteBlockedDate(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM blocked_dates WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}