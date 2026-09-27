<?php

namespace App\Models;

use mysqli;
use Exception;

class ManageFarmhouseModel {
    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Add a New Farmhouse (With Transaction for Images & Amenities)
     */
    public function addFarmhouse(int $ownerId, array $data): int {
        // Begin Transaction!
        $this->db->begin_transaction();

        try {
            // 1. Insert Base Farmhouse data
            $stmt = $this->db->prepare("INSERT INTO farmhouses (owner_id, title, description, location, address, category, price, is_negotiable, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $isNeg = $data['is_negotiable'] ? 1 : 0;
            $category = !empty($data['category']) ? trim($data['category']) : 'Farmhouse';
            
            $stmt->bind_param("isssssdi", 
                $ownerId, 
                $data['title'], 
                $data['description'], 
                $data['location'], 
                $data['address'], 
                $category,
                $data['price'], 
                $isNeg
            );
            $stmt->execute();
            $farmhouseId = $stmt->insert_id;
            $stmt->close();

            // 2. Insert Associated Amenities (If provided)
            if (!empty($data['amenities'])) {
                $amStmt = $this->db->prepare("INSERT INTO farmhouse_amenities (farmhouse_id, amenity_id) VALUES (?, ?)");
                foreach ($data['amenities'] as $amId) {
                    $amenity_id = intval($amId);
                    $amStmt->bind_param("ii", $farmhouseId, $amenity_id);
                    $amStmt->execute();
                }
                $amStmt->close();
            }

            // 3. Insert Images (URLs uploaded)
            if (!empty($data['images'])) {
                $imgStmt = $this->db->prepare("INSERT INTO images (farmhouse_id, image_url) VALUES (?, ?)");
                foreach ($data['images'] as $url) {
                    $imgStmt->bind_param("is", $farmhouseId, $url);
                    $imgStmt->execute();
                }
                $imgStmt->close();
            }

            // 4. Everything worked! Commit changes to database.
            $this->db->commit();
            return $farmhouseId;

        } catch (Exception $e) {
            // Something failed! Rollback EVERYTHING. No junk left behind.
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Edit / Update existing farmhouse details
     */
    public function updateFarmhouse(int $id, int $ownerId, array $data): bool {
        $this->db->begin_transaction();

        try {
            // 1. Ensure property belongs to user before update (IDOR prevention)
            $stmt = $this->db->prepare("UPDATE farmhouses SET title=?, description=?, location=?, address=?, category=?, price=?, is_negotiable=?, status='pending' WHERE id=? AND owner_id=?");
            
            $isNeg = $data['is_negotiable'] ? 1 : 0;
            $category = !empty($data['category']) ? trim($data['category']) : 'Farmhouse';
            $stmt->bind_param("sssssdiii", 
                $data['title'], 
                $data['description'], 
                $data['location'], 
                $data['address'], 
                $category,
                $data['price'], 
                $isNeg, 
                $id, 
                $ownerId
            );
            $stmt->execute();
            if ($stmt->affected_rows === 0 && $stmt->errno === 0) {
                 // Nothing was changed or property doesn't exist for user
                 $stmt->close();
                 $this->db->commit(); // Just finish
            } else {
                 $stmt->close();
            }

            // 2. Refresh Amenities: Drop existing mappings and create new ones
            if (isset($data['amenities'])) {
                $delAm = $this->db->prepare("DELETE FROM farmhouse_amenities WHERE farmhouse_id = ?");
                $delAm->bind_param("i", $id);
                $delAm->execute();
                $delAm->close();

                if (!empty($data['amenities'])) {
                    $amStmt = $this->db->prepare("INSERT INTO farmhouse_amenities (farmhouse_id, amenity_id) VALUES (?, ?)");
                    foreach ($data['amenities'] as $amId) {
                        $amenity_id = intval($amId);
                        $amStmt->bind_param("ii", $id, $amenity_id);
                        $amStmt->execute();
                    }
                    $amStmt->close();
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Delete Farmhouse
     */
    public function deleteFarmhouse(int $id, int $ownerId): bool {
        // Because of "ON DELETE CASCADE" set in DB definitions, deleting it from
        // 'farmhouses' table will magically drop its rows from images & amenities tables.
        $stmt = $this->db->prepare("DELETE FROM farmhouses WHERE id = ? AND owner_id = ?");
        $stmt->bind_param("ii", $id, $ownerId);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Get specific farmhouse to auto-fill an "Edit Form" correctly 
     */
    public function getFarmhouseByIdAndOwner(int $id, int $ownerId): array {
        $stmt = $this->db->prepare("SELECT * FROM farmhouses WHERE id = ? AND owner_id = ?");
        $stmt->bind_param("ii", $id, $ownerId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$result) return[];

        // Fetch Amenities tied to property (so form checkboxes can be 'checked')
        $amStmt = $this->db->prepare("SELECT amenity_id FROM farmhouse_amenities WHERE farmhouse_id = ?");
        $amStmt->bind_param("i", $id);
        $amStmt->execute();
        $amRes = $amStmt->get_result();
        
        $result['amenity_ids'] =[];
        while($row = $amRes->fetch_assoc()) {
            $result['amenity_ids'][] = $row['amenity_id'];
        }
        $amStmt->close();

        // Optional: fetch image urls associated too here if needed
        return $result;
    }

    /**
     * System utilities
     */
    public function getAvailableAmenities(): array {
        $res = $this->db->query("SELECT * FROM amenities");
        $items =[];
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }
}
?>