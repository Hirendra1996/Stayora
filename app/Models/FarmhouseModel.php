<?php
namespace App\Models;

use mysqli;

class FarmhouseModel {

    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Get filtered farmhouses.
     * Includes day_capacity + night_capacity so the controller can
     * filter by guest count after fetching.
     */
    public function getFilteredFarmhouses(array $filters = []): array {
        $sql = "SELECT
                    f.id, f.title, f.description, f.location, f.address, f.category,
                    f.price, f.is_negotiable, f.created_at, f.status,
                    f.bedrooms, f.day_capacity, f.night_capacity,
                    f.contact_phone, f.whatsapp_number,
                    f.allow_room_booking, f.room_price, f.bedroom_capacity,
                    u.name  AS owner_name,
                    u.email AS owner_email,
                    (SELECT image_url FROM images i
                     WHERE i.farmhouse_id = f.id
                     ORDER BY i.id ASC LIMIT 1) AS cover_image,
                    GROUP_CONCAT(DISTINCT a.name ORDER BY a.name SEPARATOR ', ') AS amenities
                FROM farmhouses f
                LEFT JOIN users u          ON f.contact_phone = u.phone
                LEFT JOIN farmhouse_amenities fa ON f.id = fa.farmhouse_id
                LEFT JOIN amenities a      ON fa.amenity_id = a.id
                WHERE f.status = 'active'
                  AND f.admin_approval_status = 'approved'";

        $types  = '';
        $params = [];

        // 1. Keyword search — title, location, address
        if (!empty($filters['search'])) {
            $sql  .= " AND (f.title LIKE ? OR f.location LIKE ? OR f.address LIKE ?)";
            $kw    = '%' . $filters['search'] . '%';
            $types .= 'sss';
            array_push($params, $kw, $kw, $kw);
        }

        // 2. Category filter
        if (!empty($filters['category'])) {
            $sql  .= " AND f.category = ?";
            $types .= 's';
            $params[] = $filters['category'];
        }

        // 3. Exact location
        if (!empty($filters['location'])) {
            $sql  .= " AND f.location = ?";
            $types .= 's';
            $params[] = $filters['location'];
        }

        // 3. Min price
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $sql  .= " AND f.price >= ?";
            $types .= 'd';
            $params[] = (float)$filters['min_price'];
        }

        // 4. Max price
        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $sql  .= " AND f.price <= ?";
            $types .= 'd';
            $params[] = (float)$filters['max_price'];
        }

        // 5. Bedrooms
        if (isset($filters['bedrooms']) && is_numeric($filters['bedrooms']) && (int)$filters['bedrooms'] > 0) {
            $bed = (int)$filters['bedrooms'];
            if ($bed >= 4) {
                $sql .= " AND f.bedrooms >= 4";
            } else {
                $sql .= " AND f.bedrooms = ?";
                $types .= 'i';
                $params[] = $bed;
            }
        }

        // 6. Negotiable / Open to offer
        if (!empty($filters['is_negotiable'])) {
            $sql .= " AND f.is_negotiable = 1";
        }

        // 7. Room booking allowed
        if (!empty($filters['allow_room_booking'])) {
            $sql .= " AND f.allow_room_booking = 1";
        }

        // 8. Amenities (must have ALL selected amenities)
        if (!empty($filters['amenities']) && is_array($filters['amenities'])) {
            $ids   = array_map('intval', $filters['amenities']);
            $ph    = implode(',', array_fill(0, count($ids), '?'));
            $cnt   = count($ids);
            // Subquery: farmhouse must have at least $cnt of the selected amenities
            $sql  .= " AND f.id IN (
                           SELECT farmhouse_id
                           FROM farmhouse_amenities
                           WHERE amenity_id IN ($ph)
                           GROUP BY farmhouse_id
                           HAVING COUNT(DISTINCT amenity_id) >= $cnt
                       )";
            $types .= str_repeat('i', $cnt);
            $params = array_merge($params, $ids);
        }

        // Default order — controller re-sorts for price if needed
        $sql .= " GROUP BY f.id ORDER BY f.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("getFilteredFarmhouses prepare failed: " . $this->db->error);
            return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $rows   = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Return farmhouse IDs that are fully unavailable (booked / blocked)
     * for the entire requested date range (checkin → checkout).
     *
     * "Unavailable" = there exists at least one approved booking or blocked_date
     * range that overlaps with [checkin, checkout).
     *
     * Overlap condition: existing.start < requested.checkout
     *                AND existing.end   > requested.checkin
     */
    public function getUnavailableFarmhouseIds(string $checkin, string $checkout): array {
        // We query three sources and UNION them, then return distinct farmhouse_ids
        $sql = "
            SELECT DISTINCT farmhouse_id FROM (

                -- Approved bookings
                SELECT farmhouse_id
                FROM bookings
                WHERE status = 'approved'
                  AND start_date < ?
                  AND end_date   > ?

                UNION

                -- Approved booking requests
                SELECT farmhouse_id
                FROM booking_requests
                WHERE status IN ('approved', 'completed')
                  AND check_in  < ?
                  AND check_out > ?

                UNION

                -- Manually blocked dates
                SELECT farmhouse_id
                FROM blocked_dates
                WHERE start_date < ?
                  AND end_date   > ?

            ) AS unavailable
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("getUnavailableFarmhouseIds prepare failed: " . $this->db->error);
            return [];
        }

        // Six params: checkout, checkin × 3 pairs
        $stmt->bind_param(
            'ssssss',
            $checkout, $checkin,
            $checkout, $checkin,
            $checkout, $checkin
        );
        $stmt->execute();
        $result = $stmt->get_result();

        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = (int)$row['farmhouse_id'];
        }
        $stmt->close();
        return $ids;
    }

    /**
     * Distinct active locations for the dropdown.
     */
    public function getDistinctLocations(): array {
        $result = $this->db->query(
            "SELECT DISTINCT TRIM(location) AS location
             FROM farmhouses
             WHERE status = 'active'
               AND admin_approval_status = 'approved'
               AND location IS NOT NULL
               AND location != ''
             ORDER BY location ASC"
        );
        if (!$result) return [];

        $locations = [];
        while ($row = $result->fetch_assoc()) {
            if (!empty($row['location'])) {
                $locations[] = $row['location'];
            }
        }
        return $locations;
    }

    /**
     * All amenities for the sidebar checkboxes.
     */
    public function getAmenities(): array {
        $result = $this->db->query(
            "SELECT id, name, category, icon_class
             FROM amenities
             ORDER BY category, name ASC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Farmhouse IDs the logged-in user has wishlisted.
     */
    public function getUserWishlistIds(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT farmhouse_id FROM wishlist WHERE user_id = ?"
        );
        if (!$stmt) return [];

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = (int)$row['farmhouse_id'];
        }
        $stmt->close();
        return $ids;
    }
}