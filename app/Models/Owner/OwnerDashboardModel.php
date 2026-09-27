<?php
namespace App\Models\Owner;

use mysqli;

/**
 * OwnerDashboardModel
 * Aggregated statistics and recent data for the owner dashboard.
 * Tables read: farmhouses, booking_requests, images
 *
 * PRIVACY RULE: booking_requests user data is NEVER exposed.
 *               Only counts and date ranges are surfaced.
 */
class OwnerDashboardModel {

    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    // ================================================================
    //  DASHBOARD STATS
    // ================================================================

    /**
     * High-level counts for the owner's dashboard cards.
     *
     * Returns:
     *  [
     *    'total_farmhouses'    => N,
     *    'active_farmhouses'   => N,
     *    'pending_farmhouses'  => N,
     *    'rejected_farmhouses' => N,
     *    'total_bookings'      => N,   // booking_request count only — no user details
     *    'pending_bookings'    => N,
     *    'approved_bookings'   => N,
     *  ]
     */
    public function getDashboardStats(int $ownerId): array {
        $stats = [
            'total_farmhouses'    => 0,
            'active_farmhouses'   => 0,
            'pending_farmhouses'  => 0,
            'rejected_farmhouses' => 0,
            'total_bookings'      => 0,
            'pending_bookings'    => 0,
            'approved_bookings'   => 0,
        ];

        // ── Farmhouse counts ──────────────────────────────────────────
        $stmt = $this->db->prepare(
            "SELECT status, COUNT(*) AS cnt FROM farmhouses WHERE owner_id = ? GROUP BY status"
        );
        if ($stmt) {
            $stmt->bind_param("i", $ownerId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $stats['total_farmhouses'] += (int) $row['cnt'];
                $key = strtolower($row['status']) . '_farmhouses';
                if (array_key_exists($key, $stats)) {
                    $stats[$key] = (int) $row['cnt'];
                }
            }
        }

        // ── Booking request counts (aggregate only — no user data) ─────
        $stmt2 = $this->db->prepare(
            "SELECT br.status, COUNT(br.id) AS cnt
             FROM booking_requests br
             JOIN farmhouses f ON br.farmhouse_id = f.id
             WHERE f.owner_id = ?
             GROUP BY br.status"
        );
        if ($stmt2) {
            $stmt2->bind_param("i", $ownerId);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            while ($row = $result2->fetch_assoc()) {
                $stats['total_bookings'] += (int) $row['cnt'];
                $key = strtolower($row['status']) . '_bookings';
                if (array_key_exists($key, $stats)) {
                    $stats[$key] = (int) $row['cnt'];
                }
            }
        }

        return $stats;
    }

    // ================================================================
    //  RECENT FARMHOUSES  (for dashboard preview table)
    // ================================================================

    /**
     * Most recently added farmhouses for the owner, with thumbnail
     * and total booking request count per farmhouse.
     *
     * @param int $ownerId
     * @param int $limit   Number of rows to return (default 5)
     * @return array
     */
    public function getRecentFarmhouses(int $ownerId, int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT
                 f.id,
                 f.title,
                 f.location,
                 f.category,
                 f.price,
                 f.status,
                 f.admin_approval_status,
                 f.created_at,
                 (SELECT image_url FROM images i WHERE i.farmhouse_id = f.id LIMIT 1) AS thumb_url,
                 COUNT(br.id) AS booking_request_count
             FROM farmhouses f
             LEFT JOIN booking_requests br ON br.farmhouse_id = f.id
             WHERE f.owner_id = ?
             GROUP BY f.id
             ORDER BY f.created_at DESC
             LIMIT ?"
        );
        if (!$stmt) return [];

        $stmt->bind_param("ii", $ownerId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ================================================================
    //  RECENT BOOKING REQUESTS  (aggregate view — no user identity)
    // ================================================================

    /**
     * Recent booking requests across all the owner's farmhouses.
     * Returns ONLY: farmhouse title, check-in/out dates, guest count,
     * price, status, and created_at. NO user_id, name, or phone.
     *
     * @param int $ownerId
     * @param int $limit
     * @return array
     */
    public function getRecentBookingRequests(int $ownerId, int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT
                 br.id,
                 f.title        AS farmhouse_title,
                 br.check_in,
                 br.check_out,
                 br.guests,
                 br.price,
                 br.status,
                 br.created_at
             FROM booking_requests br
             JOIN farmhouses f ON br.farmhouse_id = f.id
             WHERE f.owner_id = ?
             ORDER BY br.created_at DESC
             LIMIT ?"
        );
        if (!$stmt) return [];

        $stmt->bind_param("ii", $ownerId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
