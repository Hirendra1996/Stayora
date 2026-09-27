<?php
namespace App\Models\Owner;

use mysqli;

/**
 * OwnerBlockedDatesModel
 *
 * Manages owner-locked date ranges for their farmhouses.
 * These blocked dates prevent online bookings for those periods.
 *
 * SECURITY: All queries are strictly scoped to the owner's farmhouses
 * by joining through the farmhouses table on owner_id.
 */
class OwnerBlockedDatesModel {

    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Get all owner's farmhouses (for the property selector).
     */
    public function getOwnerFarmhouses(int $ownerId): array {
        $stmt = $this->db->prepare(
            "SELECT id, title, location FROM farmhouses
             WHERE owner_id = ?
             ORDER BY title ASC"
        );
        if (!$stmt) return [];
        $stmt->bind_param("i", $ownerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all blocked date ranges for a specific farmhouse, scoped to owner.
     *
     * @param int $ownerId
     * @param int $farmhouseId
     * @return array
     */
    public function getBlockedDates(int $ownerId, int $farmhouseId): array {
        $stmt = $this->db->prepare(
            "SELECT bd.id, bd.start_date, bd.end_date, bd.reason, bd.locked_by
             FROM blocked_dates bd
             JOIN farmhouses f ON bd.farmhouse_id = f.id
             WHERE bd.farmhouse_id = ? AND f.owner_id = ?
             ORDER BY bd.start_date ASC"
        );
        if (!$stmt) return [];
        $stmt->bind_param("ii", $farmhouseId, $ownerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all blocked dates across all of an owner's farmhouses
     * (used for the overview/stats section).
     */
    public function getAllBlockedDatesByOwner(int $ownerId): array {
        $stmt = $this->db->prepare(
            "SELECT bd.id, bd.farmhouse_id, bd.start_date, bd.end_date,
                    bd.reason, bd.locked_by,
                    f.title AS farmhouse_title
             FROM blocked_dates bd
             JOIN farmhouses f ON bd.farmhouse_id = f.id
             WHERE f.owner_id = ?
             ORDER BY bd.start_date ASC"
        );
        if (!$stmt) return [];
        $stmt->bind_param("i", $ownerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add a new blocked date range for a farmhouse.
     * Validates ownership before inserting.
     *
     * @param int    $ownerId
     * @param int    $farmhouseId
     * @param string $startDate   'Y-m-d'
     * @param string $endDate     'Y-m-d'
     * @param string $reason
     * @return bool
     */
    public function addBlockedDates(
        int    $ownerId,
        int    $farmhouseId,
        string $startDate,
        string $endDate,
        string $reason = ''
    ): bool {
        // Ownership guard
        if (!$this->ownsThisFarmhouse($ownerId, $farmhouseId)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO blocked_dates (farmhouse_id, start_date, end_date, reason, locked_by, owner_id)
             VALUES (?, ?, ?, ?, 'owner', ?)"
        );
        if (!$stmt) {
            error_log("OwnerBlockedDatesModel::addBlockedDates prepare failed: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("isssi", $farmhouseId, $startDate, $endDate, $reason, $ownerId);
        return $stmt->execute();
    }

    /**
     * Remove a blocked date entry.
     * Only allows removing entries the owner themselves created (locked_by = 'owner').
     *
     * @param int $id       The blocked_dates.id to remove
     * @param int $ownerId
     * @return bool
     */
    public function removeBlockedDate(int $id, int $ownerId): bool {
        // Join to ensure owner owns the farmhouse AND the lock was created by owner
        $stmt = $this->db->prepare(
            "DELETE bd FROM blocked_dates bd
             JOIN farmhouses f ON bd.farmhouse_id = f.id
             WHERE bd.id = ? AND f.owner_id = ? AND bd.locked_by = 'owner'"
        );
        if (!$stmt) {
            error_log("OwnerBlockedDatesModel::removeBlockedDate prepare failed: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("ii", $id, $ownerId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    /**
     * Check whether there is any overlap with existing active bookings
     * in the requested lock window (to warn the owner).
     *
     * @return int  Number of overlapping approved bookings
     */
    public function countOverlappingApprovedBookings(int $farmhouseId, string $startDate, string $endDate): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS cnt FROM booking_requests
             WHERE farmhouse_id = ?
               AND status IN ('approved', 'completed')
               AND check_in  < ?
               AND check_out > ?"
        );
        if (!$stmt) return 0;
        $stmt->bind_param("iss", $farmhouseId, $endDate, $startDate);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['cnt'] ?? 0);
    }

    /**
     * Check for duplicate/overlapping owner-blocked date ranges to avoid confusion.
     */
    public function hasOverlappingBlock(int $farmhouseId, string $startDate, string $endDate, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) AS cnt FROM blocked_dates
                WHERE farmhouse_id = ?
                  AND start_date < ?
                  AND end_date   > ?";

        if ($excludeId > 0) {
            $sql .= " AND id != ?";
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        if ($excludeId > 0) {
            $stmt->bind_param("issi", $farmhouseId, $endDate, $startDate, $excludeId);
        } else {
            $stmt->bind_param("iss", $farmhouseId, $endDate, $startDate);
        }

        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['cnt'] ?? 0) > 0;
    }

    /**
     * Verify the farmhouse belongs to this owner.
     */
    private function ownsThisFarmhouse(int $ownerId, int $farmhouseId): bool {
        $stmt = $this->db->prepare(
            "SELECT id FROM farmhouses WHERE id = ? AND owner_id = ? LIMIT 1"
        );
        if (!$stmt) return false;
        $stmt->bind_param("ii", $farmhouseId, $ownerId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
