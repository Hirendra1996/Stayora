<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Models\Admin\AddfarmModel;
use Exception;

class AddfarmControllers {
    
    private $db;

    public function __construct() {
        try {
            $this->db = Database::connect();
        } catch (Exception $e) {
            die("Database Initialization Failed: " . $e->getMessage());
        }
    }

    public function add_farm() {
        $model = new AddfarmModel($this->db);
        $message = null; 
        $messageType = null; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            
            if (empty($title) || empty($location) || $price <= 0) {
                $message = "Title, Location, and a valid Price are required.";
                $messageType = "error";
            } else {
                try {
                    $farmData = [
                        'title'       => $title,
                        'description' => trim($_POST['description'] ?? ''),
                        'location'    => $location,
                        'address'     => trim($_POST['address'] ?? ''),
                        'price'       => $price,
                        'status'      => $_POST['status'] ?? 'active'
                    ];

                    $selectedAmenities = $_POST['amenities'] ?? [];
                    $uploadedImages = $this->handleMultipleFileUploads($_FILES['images'] ?? null);

                    // Saving to DB via MySQLi Model
                    if ($model->createFarmhouse($farmData, $uploadedImages, $selectedAmenities)) {
                        $message = "Farmhouse listing has been added successfully!";
                        $messageType = "success";
                        $_POST = []; // Clear form on success
                    }

                } catch (Exception $e) {
                    $message = "Error: " . $e->getMessage();
                    $messageType = "error";
                }
            }
        }

        $availableAmenities = $model->getAllAmenities();
        include __DIR__ . '/../../Views/admin/add_farm.php';
    }

    private function handleMultipleFileUploads($fileArray) {
        $uploadedPaths = [];
        if (empty($fileArray) || empty($fileArray['name'][0])) return $uploadedPaths;

        $uploadDir = farmhouse_upload_path(); 
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true); 

        for ($i = 0; $i < count($fileArray['name']); $i++) {
            if ($fileArray['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = 'farm_' . uniqid() . time() . '.' . strtolower(pathinfo($fileArray['name'][$i], PATHINFO_EXTENSION));
                if (move_uploaded_file($fileArray['tmp_name'][$i], rtrim($uploadDir, '/') . '/' . $fileName)) {
                    $uploadedPaths[] = $fileName; 
                }
            }
        }
        return $uploadedPaths;
    }
}