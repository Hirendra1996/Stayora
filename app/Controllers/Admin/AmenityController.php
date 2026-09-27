<?php
namespace App\Controllers\Admin;

use App\Helpers\CryptoHelper;
use App\Config\Database;
use App\Models\Admin\AmenityModel;
use Exception;

class AmenityController {

    private $db;
    private AmenityModel $model;

    public function __construct() {
        try {
            $this->db = Database::connect();
            $this->model = new AmenityModel($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    // MANAGE / LIST / BULK ACTIONS
    // GET /admin/manageamenities
    // ================================================================
    public function manage(): void {
        try {
            // Handle POST actions
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';

                // 1. Single Delete
                if ($action === 'delete') {
                    $rawId     = $_POST['amenity_id'] ?? '';
                    $amenityId = $this->resolveId($rawId);
                    
                    if ($amenityId > 0 && $this->model->deleteAmenity($amenityId)) {
                        $this->flash('success_msg', "Amenity successfully deleted.");
                    } else {
                        $this->flash('error_msg', "Failed to delete amenity.");
                    }
                    redirect('admin/manageamenities');
                }

                // 2. Bulk Delete
                if ($action === 'bulk_delete') {
                    $rawIds = $_POST['amenity_ids'] ?? [];
                    if (!empty($rawIds) && is_array($rawIds)) {
                        $resolvedIds = [];
                        foreach ($rawIds as $raw) {
                            try {
                                $resolvedIds[] = $this->resolveId($raw);
                            } catch (Exception $e) {}
                        }
                        $count = $this->model->bulkDelete($resolvedIds);
                        $this->flash('success_msg', "Successfully deleted {$count} amenity item(s).");
                    } else {
                        $this->flash('error_msg', "No amenities selected for deletion.");
                    }
                    redirect('admin/manageamenities');
                }

                // 3. Quick Inline Add from Modal
                if ($action === 'create_amenity') {
                    $name      = trim($_POST['name'] ?? '');
                    $category  = trim($_POST['category'] ?? 'general');
                    $iconClass = trim($_POST['icon_class'] ?? 'star');

                    if (empty($name)) {
                        $this->flash('error_msg', "Amenity name is required.");
                    } elseif (!$this->model->isNameUnique($name)) {
                        $this->flash('error_msg', "An amenity named '{$name}' already exists.");
                    } else {
                        if ($this->model->createAmenity(['name' => $name, 'category' => $category, 'icon_class' => $iconClass])) {
                            $this->flash('success_msg', "New amenity '{$name}' added successfully.");
                        } else {
                            $this->flash('error_msg', "Failed to create amenity.");
                        }
                    }
                    redirect('admin/manageamenities');
                }

                // 4. Quick Inline Update from Modal
                if ($action === 'update_amenity') {
                    $rawId     = $_POST['amenity_id'] ?? '';
                    $amenityId = $this->resolveId($rawId);
                    $name      = trim($_POST['name'] ?? '');
                    $category  = trim($_POST['category'] ?? 'general');
                    $iconClass = trim($_POST['icon_class'] ?? 'star');

                    if (empty($name)) {
                        $this->flash('error_msg', "Amenity name is required.");
                    } elseif (!$this->model->isNameUnique($name, $amenityId)) {
                        $this->flash('error_msg', "Another amenity named '{$name}' already exists.");
                    } else {
                        if ($this->model->updateAmenity($amenityId, ['name' => $name, 'category' => $category, 'icon_class' => $iconClass])) {
                            $this->flash('success_msg', "Amenity '{$name}' updated successfully.");
                        } else {
                            $this->flash('error_msg', "Failed to update amenity.");
                        }
                    }
                    redirect('admin/manageamenities');
                }
            }

            // Filtering & Search
            $search   = trim($_GET['q'] ?? '');
            $category = trim($_GET['category'] ?? 'all');
            
            $amenities = $this->model->getAllAmenities($search, $category);
            $stats     = $this->model->getAmenityStats();

            // Encrypt IDs
            $amenities = array_map(function(array $a): array {
                $a['encrypted_id'] = CryptoHelper::encrypt((string)$a['id']);
                return $a;
            }, $amenities);

            $success_message = $this->popFlash('success_msg');
            $error_message   = $this->popFlash('error_msg');

            include __DIR__ . '/../../Views/admin/manage_amenities.php';
            $this->db->close();
            
        } catch (Exception $e) {
            die("Error loading amenities: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    // ADD AMENITY (Dedicated Page)
    // GET /admin/addamenity
    // ================================================================
    public function add(): void {
        $message = null; $messageType = null;
        $validCategories = ['general', 'kitchen', 'bedroom', 'bathroom', 'outdoor', 'other'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name      = trim($_POST['name'] ?? '');
            $category  = trim($_POST['category'] ?? 'general');
            $icon_class = trim($_POST['icon_class'] ?? 'star');

            if (empty($name)) {
                $message = "Amenity name is required."; $messageType = "error";
            } elseif (!in_array($category, $validCategories, true)) {
                $message = "Invalid category selected."; $messageType = "error";
            } elseif (!$this->model->isNameUnique($name)) {
                $message = "This amenity name already exists."; $messageType = "error";
            } else {
                if ($this->model->createAmenity(compact('name', 'category', 'icon_class'))) {
                    $this->flash('success_msg', "Amenity '{$name}' created successfully.");
                    redirect('admin/manageamenities');
                } else {
                    $message = "Failed to add amenity."; $messageType = "error";
                }
            }
        }
        include __DIR__ . '/../../Views/admin/add_amenity.php';
        $this->db->close();
    }

    // ================================================================
    // EDIT AMENITY (Dedicated Page)
    // GET /admin/editamenity?id=...
    // ================================================================
    public function edit(): void {
        try {
            $rawId = $_GET['id'] ?? '';
            $id = $this->resolveId($rawId);

            $message = null; $messageType = null;
            $validCategories = ['general', 'kitchen', 'bedroom', 'bathroom', 'outdoor', 'other'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name      = trim($_POST['name'] ?? '');
                $category  = trim($_POST['category'] ?? 'general');
                $icon_class = trim($_POST['icon_class'] ?? 'star');

                if (empty($name)) {
                    $message = "Amenity name is required."; $messageType = "error";
                } elseif (!in_array($category, $validCategories, true)) {
                    $message = "Invalid category selected."; $messageType = "error";
                } elseif (!$this->model->isNameUnique($name, $id)) {
                    $message = "This amenity name already exists."; $messageType = "error";
                } else {
                    if ($this->model->updateAmenity($id, compact('name', 'category', 'icon_class'))) {
                        $this->flash('success_msg', "Amenity updated successfully.");
                        redirect('admin/manageamenities');
                    } else {
                        $message = "Failed to update amenity."; $messageType = "error";
                    }
                }
            }

            $amenity = $this->model->getAmenityById($id);
            if (!$amenity) {
                $this->flash('error_msg', "Amenity not found.");
                redirect('admin/manageamenities');
            }

            $amenity['encrypted_id'] = CryptoHelper::encrypt((string)$amenity['id']);

            include __DIR__ . '/../../Views/admin/edit_amenity.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading amenity editor: " . htmlspecialchars($e->getMessage()));
        }
    }

    // Helper: ID resolver
    private function resolveId(string $raw): int {
        if (empty($raw)) throw new Exception("Missing ID.");
        if (ctype_digit($raw)) return (int) $raw;
        $decrypted = CryptoHelper::decrypt($raw);
        if (!$decrypted || !ctype_digit($decrypted)) throw new Exception("Invalid ID.");
        return (int) $decrypted;
    }

    // Flash Messaging Helpers
    private function flash(string $key, string $message): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[$key] = $message;
    }
    private function popFlash(string $key): ?string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $msg = $_SESSION[$key] ?? null; unset($_SESSION[$key]);
        return $msg;
    }
}