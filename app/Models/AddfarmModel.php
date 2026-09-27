<?php
namespace App\Models;

use mysqli;
use Exception;

class AddfarmModel {
    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Get list of all available amenities for the form
     */
    public function getAllAmenities() {
        $sql = "SELECT id, name FROM amenities ORDER BY id ASC";
        $result = $this->db->query($sql);
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) :[];
    }

    /**
     * Safely insert Farmhouse, Images, and Amenities using a Transaction
     */
    public function createFarmhouse($farmData, $imageUrls, $amenityIds) {
        try {
            // Start Transaction to ensure all related tables save safely
            $this->db->begin_transaction();

            // 1. Insert Farmhouse Details
            $sqlFarm = "INSERT INTO farmhouses (title, description, location, address, category, price, contact_phone, whatsapp_number, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sqlFarm);

            if (!$stmt) {
                die("Prepare failed: " . $this->db->error);
            }
            
            $category = !empty($farmData['category']) ? trim($farmData['category']) : 'Farmhouse';
            
            // "sssssdsss" = string, string, string, string, string, double, string, string, string
            $stmt->bind_param("sssssdsss", 
                $farmData['title'], 
                $farmData['description'], 
                $farmData['location'], 
                $farmData['address'], 
                $category,
                $farmData['price'], 
                $farmData['contact_phone'], 
                $farmData['whatsapp_number'],
                $farmData['status']
            );
            $stmt->execute();
            
            $farmhouseId = $stmt->insert_id; // Grab the newly generated Farmhouse ID

            // 2. Insert Associated Images
            if (!empty($imageUrls)) {
                $sqlImg = "INSERT INTO images (farmhouse_id, image_url) VALUES (?, ?)";
                $stmtImg = $this->db->prepare($sqlImg);
                
                foreach ($imageUrls as $url) {
                    $stmtImg->bind_param("is", $farmhouseId, $url);
                    $stmtImg->execute();
                }
            }

            // 3. Insert Selected Amenities
            if (!empty($amenityIds) && is_array($amenityIds)) {
                $sqlAmenity = "INSERT INTO farmhouse_amenities (farmhouse_id, amenity_id) VALUES (?, ?)";
                $stmtAmenity = $this->db->prepare($sqlAmenity);
                
                foreach ($amenityIds as $amenity_id) {
                    // Force parsing as integer for safety
                    $idInt = (int)$amenity_id;
                    $stmtAmenity->bind_param("ii", $farmhouseId, $idInt);
                    $stmtAmenity->execute();
                }
            }

            // Everything succeeded. Commit to DB permanently.
            $this->db->commit();
            return $farmhouseId;

        } catch (Exception $e) {
            // Something went wrong, revert everything so no bad data is left
            $this->db->rollback();
            throw new Exception("Error saving farmhouse: " . $e->getMessage());
        }
    }
}
?>