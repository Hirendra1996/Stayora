<?php
namespace App\Models\Owner;

use mysqli;
use Exception;

/**
 * OwnerFarmhouseModel
 * All DB operations scoped to a specific owner_id.
 * Tables touched: farmhouses, images, amenities, farmhouse_amenities,
 *                 farmhouse_rules, rule_presets, booking_requests
 *
 * PRIVACY RULE: booking_requests user data is NEVER exposed.
 *               Only aggregate counts are returned.
 */
class OwnerFarmhouseModel {

    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    // ================================================================
    //  SECTION A — LIST & STATS  (scoped to owner)
    // ================================================================

    /**
     * Return all farmhouses belonging to the given owner,
     * with a thumbnail and the total number of booking requests.
     * No user details from booking_requests are included.
     *
     * @param int         $ownerId
     * @param string|null $statusFilter  'active' | 'pending' | 'rejected' | null
     * @return array
     */
    public function getFarmhousesByOwner(int $ownerId, ?string $statusFilter = null): array {
        $validStatuses = ['active', 'pending', 'rejected'];

        $sql = "SELECT
                    f.*,
                    (SELECT image_url FROM images i WHERE i.farmhouse_id = f.id LIMIT 1) AS thumb_url,
                    COUNT(br.id) AS booking_request_count
                FROM farmhouses f
                LEFT JOIN booking_requests br ON br.farmhouse_id = f.id
                WHERE f.owner_id = ?";

        if ($statusFilter && in_array($statusFilter, $validStatuses, true)) {
            $sql .= " AND f.status = ?";
        }

        $sql .= " GROUP BY f.id ORDER BY f.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];

        if ($statusFilter && in_array($statusFilter, $validStatuses, true)) {
            $stmt->bind_param("is", $ownerId, $statusFilter);
        } else {
            $stmt->bind_param("i", $ownerId);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Count the owner's farmhouses grouped by status.
     * Returns: ['total'=>N, 'active'=>N, 'pending'=>N, 'rejected'=>N]
     */
    public function getOwnerFarmhouseStats(int $ownerId): array {
        $stats = ['total' => 0, 'active' => 0, 'pending' => 0, 'rejected' => 0];

        $stmt = $this->db->prepare(
            "SELECT status, COUNT(*) AS count FROM farmhouses WHERE owner_id = ? GROUP BY status"
        );
        if (!$stmt) return $stats;

        $stmt->bind_param("i", $ownerId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $key = strtolower($row['status']);
            if (array_key_exists($key, $stats)) {
                $stats[$key] = (int) $row['count'];
            }
            $stats['total'] += (int) $row['count'];
        }

        return $stats;
    }

    // ================================================================
    //  SECTION B — SINGLE FARMHOUSE
    // ================================================================

    /**
     * Fetch one farmhouse row only if it belongs to the given owner.
     * Returns null if not found or if owner_id doesn't match (access guard).
     */
    public function getFarmhouseByIdAndOwner(int $id, int $ownerId): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM farmhouses WHERE id = ? AND owner_id = ?"
        );
        if (!$stmt) return null;

        $stmt->bind_param("ii", $id, $ownerId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Return ONLY the count of booking requests for a farmhouse.
     * No user_id, name, or contact details are returned — privacy requirement.
     */
    public function getBookingRequestCount(int $farmhouseId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total FROM booking_requests WHERE farmhouse_id = ?"
        );
        if (!$stmt) return 0;

        $stmt->bind_param("i", $farmhouseId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Count booking requests broken down by status for one farmhouse.
     * Returns: ['pending'=>N, 'approved'=>N, 'rejected'=>N]
     * Again — no user information is included.
     */
    public function getBookingRequestCountByStatus(int $farmhouseId): array {
        $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];

        $stmt = $this->db->prepare(
            "SELECT status, COUNT(*) AS cnt
             FROM booking_requests
             WHERE farmhouse_id = ?
             GROUP BY status"
        );
        if (!$stmt) return $counts;

        $stmt->bind_param("i", $farmhouseId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $key = strtolower($row['status']);
            if (array_key_exists($key, $counts)) {
                $counts[$key] = (int) $row['cnt'];
            }
        }

        return $counts;
    }

    // ================================================================
    //  SECTION C — CREATE FARMHOUSE
    // ================================================================

    /**
     * Insert a new farmhouse with images, amenities, and rules.
     * Runs inside a transaction — rolls back fully on any failure.
     *
     * @param array  $data         All scalar farmhouse fields
     * @param array  $images       Array of image filenames (basenames)
     * @param array  $amenityIds   Array of amenity IDs
     * @param array  $rules        Array of ['rule_name'=>..,'is_allowed'=>..] maps
     * @return bool
     * @throws Exception
     */
    public function createFarmhouse(array $data, array $images = [], array $amenityIds = [], array $rules = [], array $roomTypes = []): bool {
        try {
            $this->db->begin_transaction();

            $sql = "INSERT INTO farmhouses
                        (owner_id, title, description, location, address, google_map_link, category, price, room_price, allow_room_booking,
                         bedrooms, day_capacity, night_capacity, is_negotiable,
                         contact_phone, whatsapp_number, owner_notes,
                         status, admin_approval_status, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->db->error);

            $category  = !empty($data['category']) ? trim($data['category']) : 'Farmhouse';
            $mapLink   = !empty($data['google_map_link']) ? trim($data['google_map_link']) : null;
            $roomPrice = !empty($data['room_price']) ? floatval($data['room_price']) : null;
            $allowRoom = !empty($data['allow_room_booking']) ? 1 : 0;

            $stmt->bind_param(
                "issssssddiiiiissssss",
                $data['owner_id'],
                $data['title'],
                $data['description'],
                $data['location'],
                $data['address'],
                $mapLink,
                $category,
                $data['price'],
                $roomPrice,
                $allowRoom,
                $data['bedrooms'],
                $data['day_capacity'],
                $data['night_capacity'],
                $data['is_negotiable'],
                $data['contact_phone'],
                $data['whatsapp_number'],
                $data['owner_notes'],
                $data['status'],
                $data['admin_approval_status'],
                $data['created_by']
            );

            $stmt->execute();
            $farmId = (int) $this->db->insert_id;
            $stmt->close();

            // Images
            if (!empty($images)) {
                $imgStmt = $this->db->prepare("INSERT INTO images (farmhouse_id, image_url) VALUES (?, ?)");
                foreach ($images as $filename) {
                    $imgStmt->bind_param("is", $farmId, $filename);
                    $imgStmt->execute();
                }
                $imgStmt->close();
            }

            // Amenities
            if (!empty($amenityIds)) {
                $amStmt = $this->db->prepare(
                    "INSERT INTO farmhouse_amenities (farmhouse_id, amenity_id) VALUES (?, ?)"
                );
                foreach ($amenityIds as $aid) {
                    $aid = (int) $aid;
                    $amStmt->bind_param("ii", $farmId, $aid);
                    $amStmt->execute();
                }
                $amStmt->close();
            }

            // Rules
            if (!empty($rules)) {
                $ruleStmt = $this->db->prepare(
                    "INSERT INTO farmhouse_rules (farmhouse_id, rule_name, is_allowed) VALUES (?, ?, ?)"
                );
                foreach ($rules as $rule) {
                    $ruleName  = $rule['rule_name'] ?? '';
                    $isAllowed = isset($rule['is_allowed']) ? (int) $rule['is_allowed'] : 1;
                    if (empty($ruleName)) continue;
                    $ruleStmt->bind_param("isi", $farmId, $ruleName, $isAllowed);
                    $ruleStmt->execute();
                }
                $ruleStmt->close();
            }

            // Room Types
            if (!empty($roomTypes) && $allowRoom === 1) {
                $this->syncRoomTypes($farmId, $roomTypes, true);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // ================================================================
    //  SECTION D — UPDATE FARMHOUSE
    // ================================================================

    /**
     * Update editable columns on a farmhouse row.
     * owner_id is intentionally excluded — owners cannot reassign listings.
     */
    public function updateFarmhouse(int $id, array $data, ?array $roomTypes = null): bool {
        $sql = "UPDATE farmhouses SET
                    title                  = ?,
                    description            = ?,
                    location               = ?,
                    address                = ?,
                    google_map_link        = ?,
                    category               = ?,
                    price                  = ?,
                    room_price             = ?,
                    allow_room_booking     = ?,
                    bedrooms               = ?,
                    day_capacity           = ?,
                    night_capacity         = ?,
                    is_negotiable          = ?,
                    contact_phone          = ?,
                    whatsapp_number        = ?,
                    owner_notes            = ?,
                    status                 = ?,
                    admin_approval_status  = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $category  = !empty($data['category']) ? trim($data['category']) : 'Farmhouse';
        $mapLink   = !empty($data['google_map_link']) ? trim($data['google_map_link']) : null;
        $roomPrice = !empty($data['room_price']) ? floatval($data['room_price']) : null;
        $allowRoom = !empty($data['allow_room_booking']) ? 1 : 0;

        $stmt->bind_param(
            "ssssssddiiiiisssssi",
            $data['title'],
            $data['description'],
            $data['location'],
            $data['address'],
            $mapLink,
            $category,
            $data['price'],
            $roomPrice,
            $allowRoom,
            $data['bedrooms'],
            $data['day_capacity'],
            $data['night_capacity'],
            $data['is_negotiable'],
            $data['contact_phone'],
            $data['whatsapp_number'],
            $data['owner_notes'],
            $data['status'],
            $data['admin_approval_status'],
            $id
        );

        $executed = $stmt->execute();

        if ($roomTypes !== null) {
            $this->syncRoomTypes($id, $roomTypes, $allowRoom === 1);
        }

        return $executed;
    }

    /**
     * Toggle only the status column (active | pending | rejected).
     */
    public function updateStatus(int $id, string $newStatus): bool {
        $valid = ['active', 'pending', 'rejected'];
        if (!in_array($newStatus, $valid, true)) return false;

        $stmt = $this->db->prepare("UPDATE farmhouses SET status = ? WHERE id = ?");
        if (!$stmt) return false;

        $stmt->bind_param("si", $newStatus, $id);
        return $stmt->execute();
    }

    // ================================================================
    //  SECTION E — AMENITY HELPERS
    // ================================================================

    /** All amenities (for checkbox lists in add/edit forms). */
    public function getAllAmenities(): array {
        $result = $this->db->query("SELECT * FROM amenities ORDER BY category, name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Amenity IDs currently linked to a farmhouse (for pre-checking checkboxes). */
    public function getFarmhouseAmenities(int $farmhouseId): array {
        $stmt = $this->db->prepare(
            "SELECT amenity_id FROM farmhouse_amenities WHERE farmhouse_id = ?"
        );
        $stmt->bind_param("i", $farmhouseId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_column($rows, 'amenity_id');
    }

    /**
     * Full amenity details for a farmhouse (name, icon, category).
     * Used in the show/detail view.
     */
    public function getFarmhouseAmenitiesDetailed(int $farmhouseId): array {
        $stmt = $this->db->prepare(
            "SELECT a.id, a.name, a.icon_class, a.category
             FROM farmhouse_amenities fa
             JOIN amenities a ON fa.amenity_id = a.id
             WHERE fa.farmhouse_id = ?
             ORDER BY a.category, a.name ASC"
        );
        if (!$stmt) return [];
        $stmt->bind_param("i", $farmhouseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Replace all amenity links for a farmhouse (delete-all + re-insert).
     */
    public function updateAmenities(int $farmhouseId, array $amenityIds = []): void {
        $del = $this->db->prepare("DELETE FROM farmhouse_amenities WHERE farmhouse_id = ?");
        $del->bind_param("i", $farmhouseId);
        $del->execute();

        if (!empty($amenityIds)) {
            $ins = $this->db->prepare(
                "INSERT INTO farmhouse_amenities (farmhouse_id, amenity_id) VALUES (?, ?)"
            );
            foreach ($amenityIds as $aid) {
                $aid = (int) $aid;
                $ins->bind_param("ii", $farmhouseId, $aid);
                $ins->execute();
            }
        }
    }

    // ================================================================
    //  SECTION F — RULE HELPERS
    // ================================================================

    /** All rule presets for the UI checkbox list. */
    public function getRulePresets(): array {
        $result = $this->db->query("SELECT * FROM rule_presets ORDER BY rule_name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Current rules saved for a farmhouse.
     * Returns: [['rule_name'=>..,'is_allowed'=>..], ...]
     */
    public function getFarmhouseRules(int $farmhouseId): array {
        $stmt = $this->db->prepare(
            "SELECT rule_name, is_allowed FROM farmhouse_rules WHERE farmhouse_id = ?"
        );
        $stmt->bind_param("i", $farmhouseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Replace all rules for a farmhouse (delete-all + re-insert).
     */
    public function updateRules(int $farmhouseId, array $rules = []): void {
        $del = $this->db->prepare("DELETE FROM farmhouse_rules WHERE farmhouse_id = ?");
        $del->bind_param("i", $farmhouseId);
        $del->execute();

        if (!empty($rules)) {
            $ins = $this->db->prepare(
                "INSERT INTO farmhouse_rules (farmhouse_id, rule_name, is_allowed) VALUES (?, ?, ?)"
            );
            foreach ($rules as $rule) {
                $ruleName  = $rule['rule_name'] ?? '';
                $isAllowed = isset($rule['is_allowed']) ? (int) $rule['is_allowed'] : 1;
                if (empty($ruleName)) continue;
                $ins->bind_param("isi", $farmhouseId, $ruleName, $isAllowed);
                $ins->execute();
            }
        }
    }

    // ================================================================
    //  SECTION G — IMAGE HELPERS
    // ================================================================

    /** Fetch all images for a farmhouse. */
    public function getImages(int $farmhouseId): array {
        $stmt = $this->db->prepare(
            "SELECT id, image_url FROM images WHERE farmhouse_id = ?"
        );
        $stmt->bind_param("i", $farmhouseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Delete images by DB ID and remove physical files.
     *
     * @param array  $imageIds   Array of image record IDs from $_POST
     * @param string $uploadDir  Absolute path to the upload folder
     */
    public function removeSelectedImages(array $imageIds, string $uploadDir): void {
        if (empty($imageIds)) return;

        $sel = $this->db->prepare("SELECT image_url FROM images WHERE id = ?");
        $del = $this->db->prepare("DELETE FROM images WHERE id = ?");

        foreach ($imageIds as $rawId) {
            $imageId = (int) $rawId;

            $sel->bind_param("i", $imageId);
            $sel->execute();
            $row = $sel->get_result()->fetch_assoc();

            if ($row) {
                $filename = basename($row['image_url']);
                $filepathPrimary = farmhouse_upload_path() . $filename;
                $filepathLegacy = dirname(__DIR__, 3) . '/assets/images/uploads/' . $filename;
                $filepathCustom = rtrim($uploadDir, '/') . '/' . $row['image_url'];

                if (file_exists($filepathPrimary) && is_file($filepathPrimary)) {
                    unlink($filepathPrimary);
                }
                if (file_exists($filepathLegacy) && is_file($filepathLegacy)) {
                    unlink($filepathLegacy);
                }
                if (file_exists($filepathCustom) && is_file($filepathCustom)) {
                    unlink($filepathCustom);
                }
                $del->bind_param("i", $imageId);
                $del->execute();
            }
        }
    }

    /**
     * Insert new image records after upload.
     *
     * @param int   $farmhouseId
     * @param array $filenames   Array of basename strings
     */
    public function addNewImages(int $farmhouseId, array $filenames): void {
        if (empty($filenames)) return;

        $stmt = $this->db->prepare(
            "INSERT INTO images (farmhouse_id, image_url) VALUES (?, ?)"
        );
        foreach ($filenames as $filename) {
            $stmt->bind_param("is", $farmhouseId, $filename);
            $stmt->execute();
        }
    }

    // ================================================================
    //  SECTION H — ROOM TYPES HELPERS
    // ================================================================

    /**
     * Fetch all room types configured for a farmhouse.
     * @param int $farmhouseId
     * @return array
     */
    public function getRoomTypes(int $farmhouseId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM farmhouse_room_types WHERE farmhouse_id = ? ORDER BY id ASC"
        );
        if (!$stmt) return [];
        $stmt->bind_param("i", $farmhouseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Synchronize room types for a farmhouse.
     * Handles add, update, and removal of room types.
     *
     * @param int $farmhouseId
     * @param array $roomTypes
     */
    public function syncRoomTypes(int $farmhouseId, array $roomTypes = [], bool $isRoomBookingEnabled = true): void {
        $keptIds = [];
        $totalRooms = 0;
        $totalOvernightCapacity = 0;
        $lowestRoomPrice = null;
        $activeRoomTypesCount = 0;

        foreach ($roomTypes as $rt) {
            $name = trim($rt['room_type_name'] ?? '');
            if ($name === '') continue;

            $rooms    = max(1, (int)($rt['total_rooms'] ?? 1));
            $price    = max(0.0, (float)($rt['price_per_room'] ?? 0));
            $capacity = max(1, (int)($rt['capacity_per_room'] ?? 2));
            $desc     = trim($rt['description'] ?? '');
            $status   = (isset($rt['status']) && in_array($rt['status'], ['active', 'inactive'], true)) 
                        ? $rt['status'] 
                        : ($isRoomBookingEnabled ? 'active' : 'inactive');
            $rtId     = isset($rt['id']) && (int)$rt['id'] > 0 ? (int)$rt['id'] : 0;

            if ($status === 'active' && $isRoomBookingEnabled) {
                $totalRooms += $rooms;
                $totalOvernightCapacity += ($rooms * $capacity);
                $activeRoomTypesCount++;
                if ($lowestRoomPrice === null || ($price > 0 && $price < $lowestRoomPrice)) {
                    $lowestRoomPrice = $price;
                }
            }

            if ($rtId > 0) {
                // Verify that the room type belongs to this farmhouse before updating
                $upStmt = $this->db->prepare(
                    "UPDATE farmhouse_room_types 
                     SET room_type_name = ?, total_rooms = ?, price_per_room = ?, capacity_per_room = ?, description = ?, status = ? 
                     WHERE id = ? AND farmhouse_id = ?"
                );
                if ($upStmt) {
                    $upStmt->bind_param("sidissii", $name, $rooms, $price, $capacity, $desc, $status, $rtId, $farmhouseId);
                    $upStmt->execute();
                    $keptIds[] = $rtId;
                }
            } else if ($isRoomBookingEnabled) {
                // Insert new room type only if room booking is enabled
                $inStmt = $this->db->prepare(
                    "INSERT INTO farmhouse_room_types 
                        (farmhouse_id, room_type_name, total_rooms, price_per_room, capacity_per_room, description, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                if ($inStmt) {
                    $inStmt->bind_param("isidiss", $farmhouseId, $name, $rooms, $price, $capacity, $desc, $status);
                    if ($inStmt->execute()) {
                        $insertedId = $inStmt->insert_id ?: $this->db->insert_id;
                        if ($insertedId > 0) {
                            $keptIds[] = (int)$insertedId;
                        }
                    } else {
                        error_log("Failed to insert room type: " . $inStmt->error);
                    }
                }
            }
        }

        // Delete room types that were removed in the form
        if (!empty($keptIds)) {
            $inList = implode(',', array_map('intval', $keptIds));
            $this->db->query("DELETE FROM farmhouse_room_types WHERE farmhouse_id = {$farmhouseId} AND id NOT IN ({$inList})");
        } else if (empty($roomTypes) && $isRoomBookingEnabled) {
            $this->db->query("DELETE FROM farmhouse_room_types WHERE farmhouse_id = {$farmhouseId}");
        }

        // Keep core farmhouses record in sync for search / filters / cards (rates and bedroom specs)
        if ($isRoomBookingEnabled && $activeRoomTypesCount > 0) {
            $syncPrice = $lowestRoomPrice ?? 0.0;
            $avgCap = (int)round($totalOvernightCapacity / max(1, $totalRooms));
            if ($avgCap < 1) $avgCap = 2;

            $syncStmt = $this->db->prepare(
                "UPDATE farmhouses 
                 SET room_price = ?, 
                     bedrooms = ?, 
                     bedroom_capacity = ?, 
                     night_capacity = GREATEST(COALESCE(night_capacity, 0), ?) 
                 WHERE id = ?"
            );
            if ($syncStmt) {
                $syncStmt->bind_param("diiii", $syncPrice, $totalRooms, $avgCap, $totalOvernightCapacity, $farmhouseId);
                $syncStmt->execute();
            }
        } else if (!$isRoomBookingEnabled) {
            // When room booking is disabled, ensure room_price is cleared and dormant room types are deactivated
            $this->db->query("UPDATE farmhouses SET room_price = NULL WHERE id = {$farmhouseId}");
            $this->db->query("UPDATE farmhouse_room_types SET status = 'inactive' WHERE farmhouse_id = {$farmhouseId}");
        }
    }
}
