<?php
namespace App\Models\Admin;

use mysqli;
use Exception;

class EditFarmhouseModel {
    private $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    // ============================================
    // 1. FETCH METHODS
    // ============================================

    public function getFarmhouseById($id) {
        $stmt = $this->db->prepare("SELECT * FROM farmhouses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getImages($farmhouse_id) {
        $stmt = $this->db->prepare("SELECT id, image_url FROM images WHERE farmhouse_id = ?");
        $stmt->bind_param("i", $farmhouse_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllAmenities() {
        $result = $this->db->query("SELECT * FROM amenities");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getFarmhouseAmenities($farmhouse_id) {
        $stmt = $this->db->prepare("SELECT amenity_id FROM farmhouse_amenities WHERE farmhouse_id = ?");
        $stmt->bind_param("i", $farmhouse_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        return array_column($result, 'amenity_id');
    }

    public function getActiveOwners() {
        $result = $this->db->query("SELECT id, name, email FROM owners WHERE status = 'active'");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // ============================================
    // 2. UPDATE METHODS
    // ============================================

    public function updateFarmhouse($id, $data) {
        $sql = "UPDATE farmhouses SET 
                    owner_id = ?, title = ?, description = ?, location = ?, 
                    address = ?, category = ?, price = ?, is_negotiable = ?, status = ?,
                    admin_approval_status = ?
                WHERE id = ?";
    
        $stmt = $this->db->prepare($sql);
        $owner_id = !empty($data['owner_id']) ? (int)$data['owner_id'] : null;
        $category = !empty($data['category']) ? trim($data['category']) : 'Farmhouse';
    
        $stmt->bind_param(
            "isssssdissi", 
            $owner_id, $data['title'], $data['description'], $data['location'], 
            $data['address'], $category, $data['price'], $data['is_negotiable'], 
            $data['status'], $data['admin_approval_status'], $id
        );
    
        return $stmt->execute();
    }

    public function updateAmenities($farmhouse_id, $amenity_ids = []) {
        $stmt = $this->db->prepare("DELETE FROM farmhouse_amenities WHERE farmhouse_id = ?");
        $stmt->bind_param("i", $farmhouse_id);
        $stmt->execute();

        if (!empty($amenity_ids)) {
            $insert_stmt = $this->db->prepare("INSERT INTO farmhouse_amenities (farmhouse_id, amenity_id) VALUES (?, ?)");
            foreach ($amenity_ids as $amenity_id) {
                $a_id = (int)$amenity_id;
                $insert_stmt->bind_param("ii", $farmhouse_id, $a_id);
                $insert_stmt->execute();
            }
        }
    }

    // ============================================
    // 3. IMAGE MANAGEMENT (Add & Remove via POST)
    // ============================================

    /**
     * Remove images that the user checked in the form
     * @param array $image_ids  Array from $_POST['remove_images']
     * @param string $upload_dir Absolute path to image folder
     */
    public function removeSelectedImages($image_ids, $upload_dir) {
        if (empty($image_ids) || !is_array($image_ids)) return;

        // Prepared statements for selection and deletion
        $stmt_select = $this->db->prepare("SELECT image_url FROM images WHERE id = ?");
        $stmt_delete = $this->db->prepare("DELETE FROM images WHERE id = ?");

        foreach ($image_ids as $id) {
            $image_id = (int)$id;

            // 1. Get file name to delete from physical folder
            $stmt_select->bind_param("i", $image_id);
            $stmt_select->execute();
            $result = $stmt_select->get_result();
            
            if ($row = $result->fetch_assoc()) {
                // Delete actual physical file
                $filename = basename($row['image_url']);
                $filepathPrimary = farmhouse_upload_path() . $filename;
                $filepathLegacy = dirname(__DIR__, 3) . '/assets/images/uploads/' . $filename;
                $filepathCustom = rtrim($upload_dir, '/') . '/' . $row['image_url'];

                if (file_exists($filepathPrimary) && is_file($filepathPrimary)) {
                    unlink($filepathPrimary);
                }
                if (file_exists($filepathLegacy) && is_file($filepathLegacy)) {
                    unlink($filepathLegacy);
                }
                if (file_exists($filepathCustom) && is_file($filepathCustom)) {
                    unlink($filepathCustom);
                }

                // 2. Delete from database
                $stmt_delete->bind_param("i", $image_id);
                $stmt_delete->execute();
            }
        }
    }

    /**
     * Add new image names to the database after they have been uploaded
     * @param int $farmhouse_id
     * @param array $new_image_filenames Array of freshly uploaded file names
     */
    public function addNewImages($farmhouse_id, $new_image_filenames) {
        if (empty($new_image_filenames) || !is_array($new_image_filenames)) return;

        $stmt = $this->db->prepare("INSERT INTO images (farmhouse_id, image_url) VALUES (?, ?)");
        foreach ($new_image_filenames as $filename) {
            $stmt->bind_param("is", $farmhouse_id, $filename);
            $stmt->execute();
        }
    }
}
?>