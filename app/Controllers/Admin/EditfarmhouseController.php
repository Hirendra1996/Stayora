<?php
namespace App\Controllers\Admin;
use App\Helpers\CryptoHelper;
use App\Config\Database;
use App\Models\Admin\EditFarmhouseModel;
use Exception;

class EditfarmhouseController {
    
    public function edit_farmhouse() {
        try {
            $db = Database::connect();
            $model = new EditFarmhouseModel($db);
            
            
            $id = isset($_GET['id']) ? CryptoHelper::decrypt($_GET['id']) : 0;
            
            // ==========================================
            // IF POST REQUEST: Save & Assign Actions
            // ==========================================
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $updateData = [
                    'owner_id'              => $_POST['owner_id'] ?? null,
                    'title'                 => $_POST['title'] ?? '',
                    'description'           => $_POST['description'] ?? '',
                    'location'              => $_POST['location'] ?? '',
                    'address'               => $_POST['address'] ?? '',
                    'price'                 => (float)($_POST['price'] ?? 0),
                    'is_negotiable'         => isset($_POST['is_negotiable']) ? 1 : 0,
                    'status'                => $_POST['status'] ?? 'inactive',
                    'admin_approval_status' => $_POST['admin_approval_status'] ?? 'pending',
                ];

                // Update Form Body Main Details
                $model->updateFarmhouse($id, $updateData);

                // Update Form Attached Checkboxes/Features arrays
                $amenities = $_POST['amenities'] ?? [];
                $model->updateAmenities($id, $amenities);

                // ==========================================
                // IMAGES DIRECTORY CONFIGURATION
                // ==========================================
                $upload_dir = farmhouse_upload_path(); 
                
                // 1. Remove checked images
                if (!empty($_POST['remove_images']) && is_array($_POST['remove_images'])) {
                    $model->removeSelectedImages($_POST['remove_images'], $upload_dir);
                }

                // 2. Handle New Uploads
                if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
                    $uploaded_filenames = [];
                    
                    // Create directory if it doesn't exist
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    // Loop through uploaded files
                    foreach ($_FILES['new_images']['name'] as $key => $filename) {
                        $tmp_name = $_FILES['new_images']['tmp_name'][$key];
                        $error = $_FILES['new_images']['error'][$key];

                        if ($error === UPLOAD_ERR_OK && is_uploaded_file($tmp_name)) {
                            // Generate unique file name to prevent accidental overwriting
                            $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                            $new_name = uniqid('farmhouse_') . time() . '.' . $file_ext;
                            
                            $destination_path = $upload_dir . $new_name;

                            // Move to final directory
                            if (move_uploaded_file($tmp_name, $destination_path)) {
                                // Add to array for Database entry
                                $uploaded_filenames[] = $new_name;
                            }
                        }
                    }

                    // Save new image filenames into the Database
                    if (!empty($uploaded_filenames)) {
                        $model->addNewImages($id, $uploaded_filenames);
                    }
                }

                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['success_message'] = "Listing Configuration Updates Saved & Published Successfully!";
                redirect("admin/editfarm?id=" . CryptoHelper::encrypt($id));
            }

            // ==========================================
            // IF GET REQUEST: Fetch to Setup Page Loader
            // ==========================================
            $farmhouse = $model->getFarmhouseById($id);

            if (!$farmhouse) {
                die("Farmhouse not found.");
            }

            $images = $model->getImages($id);
            $allAmenities = $model->getAllAmenities();
            $currentAmenities = $model->getFarmhouseAmenities($id);
            $owners = $model->getActiveOwners();

            $data = [
                'farmhouse'        => $farmhouse,
                'images'           => $images,
                'allAmenities'     => $allAmenities,
                'currentAmenities' => $currentAmenities,
                'owners'           => $owners
            ];

            extract($data);

            if(session_status() === PHP_SESSION_NONE) session_start();
            $success_message = $_SESSION['success_message'] ?? null;
            unset($_SESSION['success_message']);

            include __DIR__ . '/../../Views/admin/edit_farmhouse.php';

            $db->close();

        } catch (Exception $e) {
            die("Error: " . htmlspecialchars($e->getMessage()));
        }
    }
}
?>