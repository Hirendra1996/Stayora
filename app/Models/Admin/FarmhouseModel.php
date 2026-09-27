<?php
namespace App\Models\Admin;

use mysqli;
use Exception;

/**
 * FarmhouseModel
 * Covers all DB operations for: Add | Edit | Manage Farmhouses
 * Tables touched: farmhouses, images, amenities, farmhouse_amenities,
 *                 farmhouse_rules, rule_presets, owners
 */
class FarmhouseModel {

    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    // ================================================================
    //  SECTION A — MANAGE FARMHOUSES (list / status / delete)
    // ================================================================

    /**
     * Return all farmhouses with owner name + thumbnail + booking count,
     * optionally filtered by status (active | pending | rejected).
     */
    public function getAllFarmhouses(?string $statusFilter = null): array {
        $validStatuses = ['active', 'pending', 'rejected'];

        $sql = "SELECT 
                    f.*,
                    o.name  AS owner_name,
                    o.phone AS owner_phone,
                    o.email AS owner_email,
                    (SELECT image_url FROM images i WHERE i.farmhouse_id = f.id ORDER BY i.id ASC LIMIT 1) AS thumb_url,
                    (SELECT COUNT(*) FROM booking_requests br WHERE br.farmhouse_id = f.id) AS booking_count,
                    (SELECT COUNT(*) FROM wishlist w WHERE w.farmhouse_id = f.id) AS wishlist_count
                FROM farmhouses f
                LEFT JOIN owners o ON f.owner_id = o.id";

        if ($statusFilter && in_array($statusFilter, $validStatuses, true)) {
            $sql  .= " WHERE f.status = ?";
            $sql  .= " ORDER BY f.created_at DESC";
            $stmt  = $this->db->prepare($sql);
            if (!$stmt) return [];
            $stmt->bind_param("s", $statusFilter);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        $sql .= " ORDER BY f.created_at DESC";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Get Comprehensive Farmhouse Details Dossier
     */
    public function getPropertyDetails(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT 
                f.*,
                o.name  AS owner_name,
                o.phone AS owner_phone,
                o.email AS owner_email,
                (SELECT COUNT(*) FROM booking_requests br WHERE br.farmhouse_id = f.id) AS booking_count,
                (SELECT COUNT(*) FROM wishlist w WHERE w.farmhouse_id = f.id) AS wishlist_count,
                (SELECT COUNT(*) FROM inquiries inq WHERE inq.farmhouse_id = f.id) AS inquiry_count
             FROM farmhouses f
             LEFT JOIN owners o ON f.owner_id = o.id
             WHERE f.id = ?
             LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $farmhouse = $stmt->get_result()->fetch_assoc();

        if (!$farmhouse) {
            return null;
        }

        // Fetch gallery images
        $images = $this->getImages($id);

        // Fetch amenities with names & icons
        $amStmt = $this->db->prepare(
            "SELECT a.id, a.name, a.icon_class AS icon, a.category 
             FROM farmhouse_amenities fa 
             INNER JOIN amenities a ON fa.amenity_id = a.id 
             WHERE fa.farmhouse_id = ?
             ORDER BY a.category, a.name ASC"
        );
        $amStmt->bind_param("i", $id);
        $amStmt->execute();
        $amenities = $amStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Fetch rules
        $rules = $this->getFarmhouseRules($id);

        // Fetch room types
        $roomTypes = $this->getRoomTypes($id);

        return [
            'farmhouse'  => $farmhouse,
            'images'     => $images,
            'amenities'  => $amenities,
            'rules'      => $rules,
            'room_types' => $roomTypes,
        ];
    }

    /**
     * Bulk update status (active | pending | rejected)
     */
    public function bulkUpdateStatus(array $ids, string $newStatus): int {
        $valid = ['active', 'pending', 'rejected'];
        if (empty($ids) || !in_array($newStatus, $valid, true)) return 0;
        
        $intIds = array_map('intval', $ids);
        $inList = implode(',', $intIds);
        $this->db->query("UPDATE farmhouses SET status = '$newStatus' WHERE id IN ($inList)");
        return $this->db->affected_rows;
    }

    /**
     * Bulk delete farmhouses
     */
    public function bulkDelete(array $ids): int {
        if (empty($ids)) return 0;
        $intIds = array_map('intval', $ids);
        $inList = implode(',', $intIds);
        $this->db->query("DELETE FROM farmhouses WHERE id IN ($inList)");
        return $this->db->affected_rows;
    }

    /**
     * Count farmhouses grouped by status for dashboard cards.
     * Returns: ['total'=>N, 'active'=>N, 'pending'=>N, 'rejected'=>N]
     */
    public function getFarmhouseStats(): array {
        $stats  = ['total' => 0, 'active' => 0, 'pending' => 0, 'rejected' => 0];
        $result = $this->db->query("SELECT status, COUNT(*) AS count FROM farmhouses GROUP BY status");

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $key = strtolower($row['status']);
                if (array_key_exists($key, $stats)) {
                    $stats[$key] = (int)$row['count'];
                }
                $stats['total'] += (int)$row['count'];
            }
        }
        return $stats;
    }

    /**
     * Update only the status column (active | pending | rejected).
     */
    public function updateStatus(int $id, string $newStatus): bool {
        $valid = ['active', 'pending', 'rejected'];
        if (!in_array($newStatus, $valid, true)) return false;

        $stmt = $this->db->prepare("UPDATE farmhouses SET status = ? WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("si", $newStatus, $id);
        return $stmt->execute();
    }

    /**
     * Hard-delete a farmhouse.
     * CASCADE FK rules handle images, amenities, rules automatically.
     */
    public function deleteFarmhouse(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM farmhouses WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // ================================================================
    //  SECTION B — ADD FARMHOUSE
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

            // 1. Insert into farmhouses
            $sql = "INSERT INTO farmhouses 
                        (owner_id, title, description, location, address, google_map_link, category, price, room_price, allow_room_booking,
                         bedrooms, bedroom_capacity, day_capacity, night_capacity, is_negotiable,
                         contact_phone, whatsapp_number, owner_notes,
                         status, admin_approval_status, booking_mode, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->db->error);

            $roomPrice   = !empty($data['room_price']) ? floatval($data['room_price']) : null;
            $allowRoom   = !empty($data['allow_room_booking']) ? 1 : 0;
            $bedCap      = !empty($data['bedroom_capacity']) ? (int)$data['bedroom_capacity'] : 2;
            $category    = !empty($data['category']) ? trim($data['category']) : 'Farmhouse';
            $mapLink     = !empty($data['google_map_link']) ? trim($data['google_map_link']) : null;
            $bookingMode = in_array($data['booking_mode'] ?? '', ['instant', 'request'], true) ? $data['booking_mode'] : 'request';

            $stmt->bind_param(
                "issssssddiiiiiisssssss",
                $data['owner_id'],
                $data['title'],
                $data['description'],
                $data['location'],
                $data['address'],
                $mapLink,
                $category,
                $data['price'],           // d (Full Farm Price)
                $roomPrice,               // d (Per Room Price)
                $allowRoom,               // i (Allow Room Booking)
                $data['bedrooms'],        // i (Total Bedrooms)
                $bedCap,                  // i (Capacity per Bedroom)
                $data['day_capacity'],    // i
                $data['night_capacity'],  // i
                $data['is_negotiable'],   // i
                $data['contact_phone'],
                $data['whatsapp_number'],
                $data['owner_notes'],
                $data['status'],
                $data['admin_approval_status'],
                $bookingMode,
                $data['created_by']
            );

            $stmt->execute();
            $farmId = (int)$this->db->insert_id;
            $stmt->close();

            // 2. Images
            if (!empty($images)) {
                $imgStmt = $this->db->prepare("INSERT INTO images (farmhouse_id, image_url) VALUES (?, ?)");
                foreach ($images as $filename) {
                    $imgStmt->bind_param("is", $farmId, $filename);
                    $imgStmt->execute();
                }
                $imgStmt->close();
            }

            // 3. Amenities
            if (!empty($amenityIds)) {
                $amStmt = $this->db->prepare("INSERT INTO farmhouse_amenities (farmhouse_id, amenity_id) VALUES (?, ?)");
                foreach ($amenityIds as $aid) {
                    $aid = (int)$aid;
                    $amStmt->bind_param("ii", $farmId, $aid);
                    $amStmt->execute();
                }
                $amStmt->close();
            }

            // 4. Rules (farmhouse_rules table)
            if (!empty($rules)) {
                $ruleStmt = $this->db->prepare(
                    "INSERT INTO farmhouse_rules (farmhouse_id, rule_name, is_allowed) VALUES (?, ?, ?)"
                );
                foreach ($rules as $rule) {
                    $ruleName  = $rule['rule_name'] ?? '';
                    $isAllowed = isset($rule['is_allowed']) ? (int)$rule['is_allowed'] : 1;
                    if (empty($ruleName)) continue;
                    $ruleStmt->bind_param("isi", $farmId, $ruleName, $isAllowed);
                    $ruleStmt->execute();
                }
                $ruleStmt->close();
            }

            // 5. Room Types (farmhouse_room_types table)
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
    //  SECTION C — EDIT FARMHOUSE
    // ================================================================

    /**
     * Fetch a single farmhouse row by primary key.
     */
    public function getFarmhouseById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM farmhouses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Update all editable columns of a farmhouse row.
     */
    public function updateFarmhouse(int $id, array $data, ?array $roomTypes = null): bool {
        $sql = "UPDATE farmhouses SET
                    owner_id               = ?,
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
                    bedroom_capacity       = ?,
                    day_capacity           = ?,
                    night_capacity         = ?,
                    is_negotiable          = ?,
                    contact_phone          = ?,
                    whatsapp_number        = ?,
                    owner_notes            = ?,
                    status                 = ?,
                    admin_approval_status  = ?,
                    booking_mode           = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("FarmhouseModel::updateFarmhouse prepare failed: " . $this->db->error);
            return false;
        }

        $owner_id    = !empty($data['owner_id']) ? (int)$data['owner_id'] : null;
        $allowRoom   = !empty($data['allow_room_booking']) ? 1 : 0;
        $roomPrice   = ($allowRoom && !empty($data['room_price'])) ? floatval($data['room_price']) : null;
        $bedCap      = !empty($data['bedroom_capacity']) ? (int)$data['bedroom_capacity'] : 2;
        $bedrooms    = max(1, (int)($data['bedrooms'] ?? 1));
        $dayCap      = !empty($data['day_capacity']) ? (int)$data['day_capacity'] : null;
        $nightCap    = !empty($data['night_capacity']) ? (int)$data['night_capacity'] : null;
        $category    = !empty($data['category']) ? trim($data['category']) : 'Farmhouse';
        $mapLink     = !empty($data['google_map_link']) ? trim($data['google_map_link']) : null;
        $bookingMode = in_array($data['booking_mode'] ?? '', ['instant', 'request'], true) ? $data['booking_mode'] : 'request';

        $stmt->bind_param(
            "issssssddiiiiiissssssi",
            $owner_id,
            $data['title'],
            $data['description'],
            $data['location'],
            $data['address'],
            $mapLink,
            $category,
            $data['price'],            // d (Full Farmhouse Price)
            $roomPrice,                // d (Per Room Price)
            $allowRoom,                // i (Allow Room Booking)
            $bedrooms,                 // i (Bedrooms count)
            $bedCap,                   // i (Capacity per bedroom)
            $dayCap,                   // i
            $nightCap,                 // i
            $data['is_negotiable'],    // i
            $data['contact_phone'],
            $data['whatsapp_number'],
            $data['owner_notes'],
            $data['status'],
            $data['admin_approval_status'],
            $bookingMode,
            $id                        // i (WHERE)
        );

        $executed = $stmt->execute();
        if (!$executed) {
            error_log("FarmhouseModel::updateFarmhouse execute failed: " . $stmt->error);
            return false;
        }

        if ($roomTypes !== null) {
            $this->syncRoomTypes($id, $roomTypes, $allowRoom === 1);
        }

        return true;
    }

    // ================================================================
    //  SECTION D — AMENITY HELPERS
    // ================================================================

    /** All amenities from the amenities table (for checkbox lists). */
    public function getAllAmenities(): array {
        $result = $this->db->query("SELECT * FROM amenities ORDER BY category, name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Amenity IDs currently linked to a farmhouse. */
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
     * Replace all amenity links for a farmhouse.
     * Deletes existing rows, then inserts the new set.
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
                $aid = (int)$aid;
                $ins->bind_param("ii", $farmhouseId, $aid);
                $ins->execute();
            }
        }
    }

    // ================================================================
    //  SECTION E — RULES HELPERS
    // ================================================================

    /** All rule presets from rule_presets table (for the UI checkbox list). */
    public function getRulePresets(): array {
        $result = $this->db->query("SELECT * FROM rule_presets ORDER BY rule_name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Fetch the current rules saved for a farmhouse.
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
     * Replace all rules for a farmhouse.
     * $rules = [['rule_name' => 'Pets', 'is_allowed' => 1], ...]
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
                $isAllowed = isset($rule['is_allowed']) ? (int)$rule['is_allowed'] : 1;
                if (empty($ruleName)) continue;
                $ins->bind_param("isi", $farmhouseId, $ruleName, $isAllowed);
                $ins->execute();
            }
        }
    }

    // ================================================================
    //  SECTION F — IMAGE HELPERS
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
     * Delete images whose DB IDs are in $imageIds.
     * Also removes the physical file from $uploadDir.
     *
     * @param array  $imageIds   Array of image record IDs from $_POST
     * @param string $uploadDir  Absolute path to the upload folder
     */
    public function removeSelectedImages(array $imageIds, string $uploadDir): void {
        if (empty($imageIds)) return;

        $sel = $this->db->prepare("SELECT image_url FROM images WHERE id = ?");
        $del = $this->db->prepare("DELETE FROM images WHERE id = ?");

        foreach ($imageIds as $rawId) {
            $imageId = (int)$rawId;

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
                // Verify ownership of the room type before updating
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

        // Keep core farmhouses record in sync for legacy queries/cards/booking engine
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

    // ================================================================
    //  SECTION I — OWNER HELPER
    // ================================================================

    /** Active owners for the owner-assignment dropdown. */
    public function getActiveOwners(): array {
        $result = $this->db->query(
            "SELECT id, name, email, phone FROM owners WHERE status = 'active' ORDER BY name ASC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}


