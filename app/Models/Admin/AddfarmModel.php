<?php
namespace App\Models\Admin;

use mysqli;
use Exception;

class AddfarmModel {
    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    public function getAllAmenities() {
        $result = $this->db->query("SELECT id, name FROM amenities ORDER BY name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function createFarmhouse($farmData, $images, $amenityIds) {
        try {
            $this->db->begin_transaction();

            // 1. Insert into farmhouses table
            $category = !empty($farmData['category']) ? trim($farmData['category']) : 'Farmhouse';
            $stmt = $this->db->prepare("INSERT INTO farmhouses (title, description, location, address, category, price, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, 'admin')");
            $stmt->bind_param("sssssds", 
                $farmData['title'], $farmData['description'], $farmData['location'], 
                $farmData['address'], $category, $farmData['price'], $farmData['status']
            );
            $stmt->execute();
            $farmId = $this->db->insert_id;
            $stmt->close();

            // 2. Insert Images
            if (!empty($images)) {
                $imgStmt = $this->db->prepare("INSERT INTO images (farmhouse_id, image_url) VALUES (?, ?)");
                foreach ($images as $path) {
                    $imgStmt->bind_param("is", $farmId, $path);
                    $imgStmt->execute();
                }
                $imgStmt->close();
            }

            // 3. Insert Amenities mapping
            if (!empty($amenityIds)) {
                $amStmt = $this->db->prepare("INSERT INTO farmhouse_amenities (farmhouse_id, amenity_id) VALUES (?, ?)");
                foreach ($amenityIds as $aid) {
                    $idInt = (int)$aid;
                    $amStmt->bind_param("ii", $farmId, $idInt);
                    $amStmt->execute();
                }
                $amStmt->close();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}