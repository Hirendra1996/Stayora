<?php
namespace App\Controllers\Owner;

use App\Helpers\CryptoHelper;
use App\Config\Database;
use App\Models\Owner\OwnerFarmhouseModel;
use Exception;

/**
 * OwnerFarmhouseController
 * Handles: List | Show | Add | Edit farmhouses (owner-scoped)
 *
 * Routes:
 *   GET  /owner/farmhouses          → index()   – paginated list with booking counts
 *   GET  /owner/farmhouses/show     → show()    – single farmhouse detail + booking count
 *   GET  /owner/farmhouses/add      → addView() – blank add form
 *   POST /owner/farmhouses/add      → add()     – persist new farmhouse
 *   GET  /owner/farmhouses/edit     → edit()    – pre-filled edit form  (?id=<encrypted>)
 *   POST /owner/farmhouses/edit     → edit()    – persist updates
 */
class OwnerFarmhouseController {

    private $db;
    private OwnerFarmhouseModel $model;

    public function __construct() {
        try {
            $this->db    = Database::connect();
            $this->model = new OwnerFarmhouseModel($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  1. LIST  →  /owner/farmhouses
    // ================================================================

    public function index(): void {
        try {
            $ownerId             = $this->resolveOwnerId();
            $currentStatusFilter = $_GET['status'] ?? null;

            $farmhouses      = $this->model->getFarmhousesByOwner($ownerId, $currentStatusFilter);
            $stats           = $this->model->getOwnerFarmhouseStats($ownerId);
            $success_message = $this->popFlash('success_message');
            $error_message   = $this->popFlash('error_message');

            extract([
                'farmhouses'          => $farmhouses,
                'stats'               => $stats,
                'currentStatusFilter' => $currentStatusFilter,
                'success_message'     => $success_message,
                'error_message'       => $error_message,
            ]);

            include __DIR__ . '/../../Views/owner/farmhouses/index.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading farmhouses: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  2. SHOW  →  /owner/farmhouses/show?id=<encrypted>
    // ================================================================

    public function show(): void {
        try {
            $ownerId = $this->resolveOwnerId();
            $id      = isset($_GET['id']) ? (int) CryptoHelper::decrypt($_GET['id']) : 0;

            $farmhouse = $this->model->getFarmhouseByIdAndOwner($id, $ownerId);

            if (!$farmhouse) {
                // Farmhouse not found or does not belong to this owner
                redirect('owner/farmhouses');
            }

            $images        = $this->model->getImages($id);
            $roomTypes     = $this->model->getRoomTypes($id);
            $amenities     = $this->model->getFarmhouseAmenitiesDetailed($id);
            $rules         = $this->model->getFarmhouseRules($id);
            $bookingCount  = $this->model->getBookingRequestCount($id);   // count only — no user details

            extract([
                'farmhouse'    => $farmhouse,
                'images'       => $images,
                'roomTypes'    => $roomTypes,
                'amenities'    => $amenities,
                'rules'        => $rules,
                'bookingCount' => $bookingCount,
            ]);

            include __DIR__ . '/../../Views/owner/farmhouses/show.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading farmhouse: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  3. ADD (GET)  →  /owner/farmhouses/add
    // ================================================================

    public function addView(): void {
        try {
            $availableAmenities = $this->model->getAllAmenities();
            $allRulePresets     = $this->model->getRulePresets();
            $message            = $this->popFlash('success_message');
            $error              = $this->popFlash('error_message');

            extract([
                'availableAmenities' => $availableAmenities,
                'allRulePresets'     => $allRulePresets,
                'message'            => $message,
                'error'              => $error,
            ]);

            include __DIR__ . '/../../Views/owner/farmhouses/add.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading add form: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  4. ADD (POST)  →  /owner/farmhouses/add
    // ================================================================

    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->addView();
            return;
        }

        try {
            $ownerId  = $this->resolveOwnerId();
            $title    = trim($_POST['title']    ?? '');
            $location = trim($_POST['location'] ?? '');
            $price    = floatval($_POST['price'] ?? 0);

            if (empty($title) || empty($location) || $price <= 0) {
                $this->flash('error_message', "Title, Location, and a valid Price are required.");
                redirect('owner/farmhouses/add');
            }

            $allowedCategories = ['Guest House', 'Resort', 'Farmhouse', 'Villa'];
            $rawCategory = trim($_POST['category'] ?? '');
            $category = in_array($rawCategory, $allowedCategories) ? $rawCategory : 'Farmhouse';

            $farmData = [
                'owner_id'         => $ownerId,
                'title'            => $title,
                'description'      => trim($_POST['description']   ?? ''),
                'location'         => $location,
                'address'          => trim($_POST['address']       ?? ''),
                'google_map_link'  => trim($_POST['google_map_link'] ?? ''),
                'category'         => $category,
                'price'                 => $price,
                'room_price'            => !empty($_POST['room_price']) ? floatval($_POST['room_price']) : null,
                'allow_room_booking'    => isset($_POST['allow_room_booking']) ? 1 : 0,
                'bedrooms'              => (int)($_POST['bedrooms']     ?? 1),
                'day_capacity'     => !empty($_POST['day_capacity'])   ? (int)$_POST['day_capacity']   : null,
                'night_capacity'   => !empty($_POST['night_capacity']) ? (int)$_POST['night_capacity'] : null,
                'is_negotiable'    => isset($_POST['is_negotiable'])   ? 1 : 0,
                'contact_phone'    => trim($_POST['contact_phone']    ?? ''),
                'whatsapp_number'  => trim($_POST['whatsapp_number']  ?? ''),
                'owner_notes'      => trim($_POST['owner_notes']      ?? ''),
                // Owner-submitted listings always start as pending for admin approval
                'status'                => 'pending',
                'admin_approval_status' => 'pending',
                'created_by'            => 'owner',
            ];

            $selectedAmenities = $_POST['amenities'] ?? [];
            $rules             = $_POST['rules']     ?? [];
            $roomTypes         = $_POST['room_types'] ?? [];
            $uploadDir         = farmhouse_upload_path();
            $uploadedFiles     = (!empty($_FILES['new_images']['name'][0])) ? $_FILES['new_images'] : ($_FILES['images'] ?? null);
            $uploadedImages    = $this->handleImageUploads($uploadedFiles, $uploadDir, 'farmhouse_');

            if ($this->model->createFarmhouse($farmData, $uploadedImages, $selectedAmenities, $rules, $roomTypes)) {
                $this->flash('success_message', "Your property listing ({$category}) has been submitted and is awaiting admin approval.");
                redirect('owner/farmhouses');
            }

            $this->flash('error_message', "Could not save your listing. Please try again.");
            redirect('owner/farmhouses/add');

        } catch (Exception $e) {
            die("Error saving farmhouse: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  5. EDIT  →  /owner/farmhouses/edit?id=<encrypted>
    // ================================================================

    public function edit(): void {
        try {
            $ownerId = $this->resolveOwnerId();
            $id      = isset($_GET['id']) ? (int) CryptoHelper::decrypt($_GET['id']) : 0;

            // Ownership guard — fetch only if this farmhouse belongs to the owner
            $farmhouse = $this->model->getFarmhouseByIdAndOwner($id, $ownerId);
            if (!$farmhouse) {
                redirect('owner/farmhouses');
            }

            // ── POST: save changes ─────────────────────────────────────
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $allowedCategories = ['Guest House', 'Resort', 'Farmhouse', 'Villa'];
                $rawCategory = trim($_POST['category'] ?? '');
                $category = in_array($rawCategory, $allowedCategories) ? $rawCategory : 'Farmhouse';

                $updateData = [
                    'title'          => trim($_POST['title']         ?? ''),
                    'description'    => trim($_POST['description']   ?? ''),
                    'location'       => trim($_POST['location']      ?? ''),
                    'address'        => trim($_POST['address']       ?? ''),
                    'google_map_link'=> trim($_POST['google_map_link'] ?? ''),
                    'category'       => $category,
                    'price'              => (float)($_POST['price']      ?? 0),
                    'room_price'         => !empty($_POST['room_price']) ? floatval($_POST['room_price']) : null,
                    'allow_room_booking' => isset($_POST['allow_room_booking']) ? 1 : 0,
                    'bedrooms'           => (int)($_POST['bedrooms']     ?? 1),
                    'day_capacity'   => !empty($_POST['day_capacity'])   ? (int)$_POST['day_capacity']   : null,
                    'night_capacity' => !empty($_POST['night_capacity']) ? (int)$_POST['night_capacity'] : null,
                    'is_negotiable'  => isset($_POST['is_negotiable'])   ? 1 : 0,
                    'contact_phone'  => trim($_POST['contact_phone']   ?? ''),
                    'whatsapp_number'=> trim($_POST['whatsapp_number'] ?? ''),
                    'owner_notes'    => trim($_POST['owner_notes']     ?? ''),
                    // Owners cannot change approval status; status reverts to pending on edit
                    'status'                => 'pending',
                    'admin_approval_status' => 'pending',
                ];

                $roomTypes = $_POST['room_types'] ?? [];
                $this->model->updateFarmhouse($id, $updateData, $roomTypes);

                $amenities = $_POST['amenities'] ?? [];
                $this->model->updateAmenities($id, $amenities);

                $rules = $_POST['rules'] ?? [];
                $this->model->updateRules($id, $rules);

                $uploadDir = farmhouse_upload_path();

                // 1. Remove images the owner checked for deletion
                if (!empty($_POST['remove_images']) && is_array($_POST['remove_images'])) {
                    $this->model->removeSelectedImages($_POST['remove_images'], $uploadDir);
                }

                // 2. Handle newly uploaded images (supports new_images or images input name)
                $uploadedFiles = (!empty($_FILES['new_images']['name'][0])) ? $_FILES['new_images'] : ($_FILES['images'] ?? null);
                if ($uploadedFiles && !empty($uploadedFiles['name'][0])) {
                    $newFilenames = $this->handleImageUploads($uploadedFiles, $uploadDir, 'farmhouse_');
                    if (!empty($newFilenames)) {
                        $this->model->addNewImages($id, $newFilenames);
                    }
                }

                $this->flash('success_message', "Listing updated successfully! Changes saved.");
                redirect("owner/farmhouses/edit?id=" . CryptoHelper::encrypt($id));
            }

            // ── GET: render edit form ──────────────────────────────────
            $images           = $this->model->getImages($id);
            $roomTypes        = $this->model->getRoomTypes($id);
            $allAmenities     = $this->model->getAllAmenities();
            $currentAmenities = $this->model->getFarmhouseAmenities($id);
            $currentRules     = $this->model->getFarmhouseRules($id);
            $allRulePresets   = $this->model->getRulePresets();
            $success_message  = $this->popFlash('success_message');
            $error_message    = $this->popFlash('error_message');

            extract([
                'farmhouse'        => $farmhouse,
                'images'           => $images,
                'roomTypes'        => $roomTypes,
                'allAmenities'     => $allAmenities,
                'currentAmenities' => $currentAmenities,
                'currentRules'     => $currentRules,
                'allRulePresets'   => $allRulePresets,
                'success_message'  => $success_message,
                'error_message'    => $error_message,
            ]);

            include __DIR__ . '/../../Views/owner/farmhouses/edit.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  6. TOGGLE STATUS  →  POST /owner/farmhouses (action=toggle_status)
    // ================================================================

    public function toggleStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('owner/farmhouses');
        }

        try {
            $ownerId     = $this->resolveOwnerId();
            $farmhouseId = (int) CryptoHelper::decrypt($_POST['farmhouse_id'] ?? '');
            $newStatus   = $_POST['status'] ?? '';

            // Ownership guard before mutating
            $farmhouse = $this->model->getFarmhouseByIdAndOwner($farmhouseId, $ownerId);
            if (!$farmhouse) {
                $this->flash('error_message', "Farmhouse not found or access denied.");
                redirect('owner/farmhouses');
            }

            if ($this->model->updateStatus($farmhouseId, $newStatus)) {
                $this->flash('success_message', "Farmhouse status updated successfully.");
            } else {
                $this->flash('error_message', "Could not update status. Please try again.");
            }

            redirect('owner/farmhouses');

        } catch (Exception $e) {
            die("Error updating status: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  PRIVATE HELPERS
    // ================================================================

    /**
     * Resolve the logged-in owner's ID from session.
     * Redirects to login if not set.
     */
    private function resolveOwnerId(): int {
        $id = (int) ($_SESSION['user_id'] ?? 0);
        if (!$id) {
            redirect('owner/login');
        }
        return $id;
    }

    /**
     * Upload multiple images and return an array of saved filenames.
     */
    private function handleImageUploads(?array $fileArray, string $uploadDir = '', string $prefix = 'farm_'): array {
        $saved = [];

        if (empty($fileArray) || empty($fileArray['name'][0])) return $saved;

        if (empty($uploadDir)) {
            $uploadDir = farmhouse_upload_path();
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $count = count($fileArray['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($fileArray['error'][$i] !== UPLOAD_ERR_OK) continue;
            if (!is_uploaded_file($fileArray['tmp_name'][$i])) continue;

            $ext      = strtolower(pathinfo($fileArray['name'][$i], PATHINFO_EXTENSION));
            $newName  = $prefix . uniqid() . time() . '.' . $ext;
            $destPath = rtrim($uploadDir, '/') . '/' . $newName;

            if (move_uploaded_file($fileArray['tmp_name'][$i], $destPath)) {
                $saved[] = $newName;
            }
        }

        return $saved;
    }

    /** Write a one-shot message to the session. */
    private function flash(string $key, string $message): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[$key] = $message;
    }

    /** Read and immediately destroy a flash message. */
    private function popFlash(string $key): ?string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $msg = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $msg;
    }
}
