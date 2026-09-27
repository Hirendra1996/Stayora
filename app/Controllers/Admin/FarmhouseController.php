<?php
namespace App\Controllers\Admin;

use App\Helpers\CryptoHelper;
use App\Config\Database;
use App\Models\Admin\FarmhouseModel;
use Exception;

/**
 * FarmhouseController
 * Handles: Add Farmhouse | Edit Farmhouse | Manage Farmhouses
 * Route dispatch is done via the router — see bottom of this file for route cases.
 */
class FarmhouseController {

    private $db;
    private FarmhouseModel $model;

    public function __construct() {
        try {
            $this->db    = Database::connect();
            $this->model = new FarmhouseModel($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  1. MANAGE FARMHOUSES  →  /admin/managefarmhouses
    // ================================================================

    public function manage_farmhouses(): void {
        try {
            // ── POST: actions ──────────────────────────────────────────
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
                $action = $_POST['action'];

                // Single Toggle Status
                if ($action === 'toggle_status' && !empty($_POST['status'])) {
                    $farmhouse_id = $this->resolveFarmhouseId($_POST['farmhouse_id'] ?? '');
                    $this->model->updateStatus($farmhouse_id, $_POST['status']);
                    $this->flash('success_msg', "Property visibility updated successfully.");
                    redirect('admin/managefarmhouses');
                }

                // Single Delete
                if ($action === 'delete') {
                    $farmhouse_id = $this->resolveFarmhouseId($_POST['farmhouse_id'] ?? '');
                    $this->model->deleteFarmhouse($farmhouse_id);
                    $this->flash('success_msg', "Farmhouse permanently deleted from the platform.");
                    redirect('admin/managefarmhouses');
                }

                // Bulk Status Update
                if ($action === 'bulk_status') {
                    $rawIds = $_POST['farmhouse_ids'] ?? [];
                    $targetStatus = in_array($_POST['target_status'] ?? '', ['active', 'pending', 'rejected']) ? $_POST['target_status'] : 'active';
                    
                    if (!empty($rawIds) && is_array($rawIds)) {
                        $resolvedIds = [];
                        foreach ($rawIds as $raw) {
                            try {
                                $resolvedIds[] = $this->resolveFarmhouseId($raw);
                            } catch (Exception $e) {}
                        }
                        $count = $this->model->bulkUpdateStatus($resolvedIds, $targetStatus);
                        $this->flash('success_msg', "Updated status for {$count} property/properties to {$targetStatus}.");
                    } else {
                        $this->flash('error_msg', "No properties selected for bulk action.");
                    }
                    redirect('admin/managefarmhouses');
                }

                // Bulk Delete
                if ($action === 'bulk_delete') {
                    $rawIds = $_POST['farmhouse_ids'] ?? [];
                    if (!empty($rawIds) && is_array($rawIds)) {
                        $resolvedIds = [];
                        foreach ($rawIds as $raw) {
                            try {
                                $resolvedIds[] = $this->resolveFarmhouseId($raw);
                            } catch (Exception $e) {}
                        }
                        $count = $this->model->bulkDelete($resolvedIds);
                        $this->flash('success_msg', "Permanently deleted {$count} farmhouse listing(s).");
                    } else {
                        $this->flash('error_msg', "No properties selected for deletion.");
                    }
                    redirect('admin/managefarmhouses');
                }
            }

            // ── GET: fetch data for view ───────────────────────────────
            $allFarmhouses = $this->model->getAllFarmhouses();
            $stats         = $this->model->getFarmhouseStats();
            $owners        = $this->model->getActiveOwners();

            // Encrypt each farmhouse's ID
            $allFarmhouses = array_map(function (array $f): array {
                $f['encrypted_id'] = CryptoHelper::encrypt((string) $f['id']);
                return $f;
            }, $allFarmhouses);

            // Filtering parameters
            $searchQuery    = trim($_GET['q'] ?? '');
            $statusFilter   = strtolower(trim($_GET['status'] ?? 'all'));
            $categoryFilter = trim($_GET['category'] ?? '');
            $ownerFilter    = trim($_GET['owner_id'] ?? '');
            $sortBy         = strtolower(trim($_GET['sort'] ?? 'newest'));

            $filteredFarmhouses = array_filter($allFarmhouses, function($f) use ($searchQuery, $statusFilter, $categoryFilter, $ownerFilter) {
                // Search match
                if ($searchQuery !== '') {
                    $haystack = strtolower($f['title'] . ' ' . ($f['location'] ?? '') . ' ' . ($f['address'] ?? '') . ' ' . ($f['owner_name'] ?? '') . ' ' . ($f['category'] ?? ''));
                    if (strpos($haystack, strtolower($searchQuery)) === false) {
                        return false;
                    }
                }

                // Category match
                if ($categoryFilter !== '' && strtolower($categoryFilter) !== 'all') {
                    if (($f['category'] ?? 'Farmhouse') !== $categoryFilter) return false;
                }

                // Status match
                if ($statusFilter === 'active' && strtolower($f['status'] ?? '') !== 'active') return false;
                if ($statusFilter === 'pending' && strtolower($f['status'] ?? '') !== 'pending') return false;
                if ($statusFilter === 'rejected' && strtolower($f['status'] ?? '') !== 'rejected') return false;

                // Owner match
                if ($ownerFilter !== '' && (string)($f['owner_id'] ?? '') !== $ownerFilter) return false;

                return true;
            });

            // Sorting
            usort($filteredFarmhouses, function($a, $b) use ($sortBy) {
                return match($sortBy) {
                    'oldest'        => strtotime($a['created_at']) <=> strtotime($b['created_at']),
                    'price_asc'     => (float)($a['price'] ?? 0) <=> (float)($b['price'] ?? 0),
                    'price_desc'    => (float)($b['price'] ?? 0) <=> (float)($a['price'] ?? 0),
                    'most_bookings' => (int)($b['booking_count'] ?? 0) <=> (int)($a['booking_count'] ?? 0),
                    'title_asc'     => strcasecmp($a['title'], $b['title']),
                    'title_desc'    => strcasecmp($b['title'], $a['title']),
                    default         => strtotime($b['created_at']) <=> strtotime($a['created_at']),
                };
            });

            // Pagination calculations
            $perPage     = max(6, min(100, (int) ($_GET['per_page'] ?? 12)));
            $totalCount  = count($filteredFarmhouses);
            $totalPages  = max(1, (int) ceil($totalCount / $perPage));
            $currentPage = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));
            $offset      = ($currentPage - 1) * $perPage;

            // Paginated slice
            $farmhouses = array_slice($filteredFarmhouses, $offset, $perPage);

            $success_message = $this->popFlash('success_msg');
            $error_message   = $this->popFlash('error_msg');

            extract([
                'farmhouses'            => $farmhouses,
                'totalCount'            => $totalCount,
                'totalPages'            => $totalPages,
                'currentPage'           => $currentPage,
                'perPage'               => $perPage,
                'stats'                 => $stats,
                'owners'                => $owners,
                'currentStatusFilter'   => $statusFilter,
                'currentCategoryFilter' => $categoryFilter,
                'currentOwnerFilter'    => $ownerFilter,
                'searchQuery'           => $searchQuery,
                'sortBy'                => $sortBy,
                'success_message'       => $success_message,
                'error_message'         => $error_message,
            ]);

            include __DIR__ . '/../../Views/admin/manage_farmhouses.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error managing properties: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  AJAX — Property Details Preview Dossier
    //  GET /admin/managefarmhouses/details?id=...
    // ================================================================

    public function propertyDetails(): void {
        header('Content-Type: application/json');
        try {
            $rawId = $_GET['id'] ?? '';
            $farmhouse_id = $this->resolveFarmhouseId($rawId);

            $dossier = $this->model->getPropertyDetails($farmhouse_id);

            if (!$dossier) {
                echo json_encode(['success' => false, 'message' => 'Property not found.']);
                $this->db->close();
                return;
            }

            if (!empty($dossier['images']) && is_array($dossier['images'])) {
                foreach ($dossier['images'] as &$imgItem) {
                    $imgItem['resolved_url'] = farmhouse_img_url($imgItem['image_url'] ?? '');
                }
                unset($imgItem);
            }

            $dossier['farmhouse']['encrypted_id'] = CryptoHelper::encrypt((string) $dossier['farmhouse']['id']);
            $dossier['farmhouse']['resolved_image_url'] = farmhouse_img_url($dossier['farmhouse']['image_url'] ?? '');
            $dossier['farmhouse']['formatted_created'] = !empty($dossier['farmhouse']['created_at']) 
                ? date('d M Y', strtotime($dossier['farmhouse']['created_at'])) 
                : 'N/A';
            $dossier['farmhouse']['formatted_price'] = number_format((float)($dossier['farmhouse']['price'] ?? 0));

            echo json_encode([
                'success' => true,
                'data'    => $dossier,
            ]);
            $this->db->close();

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ================================================================
    //  EXPORT PROPERTIES TO CSV
    //  GET /admin/managefarmhouses/export
    // ================================================================

    public function exportProperties(): void {
        try {
            $farmhouses = $this->model->getAllFarmhouses();

            $filename = "farmlelo_properties_export_" . date('Y-m-d_His') . ".csv";

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $output = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($output, [
                'ID',
                'Estate Title',
                'Category',
                'Location',
                'Full Address',
                'Nightly Price (INR)',
                'Negotiable',
                'Bedrooms',
                'Day Capacity',
                'Night Capacity',
                'Host / Owner Name',
                'Host Phone',
                'Host Email',
                'Listing Status',
                'Admin Approval Status',
                'Total Bookings',
                'Wishlist Count',
                'Created Date'
            ]);

            foreach ($farmhouses as $f) {
                fputcsv($output, [
                    $f['id'],
                    $f['title'],
                    $f['category'] ?? 'Farmhouse',
                    $f['location'] ?? 'N/A',
                    $f['address'] ?? 'N/A',
                    $f['price'] ?? 0,
                    !empty($f['is_negotiable']) ? 'Yes' : 'No',
                    $f['bedrooms'] ?? 1,
                    $f['day_capacity'] ?? 'N/A',
                    $f['night_capacity'] ?? 'N/A',
                    $f['owner_name'] ?? 'Unassigned',
                    $f['owner_phone'] ?? 'N/A',
                    $f['owner_email'] ?? 'N/A',
                    ucfirst($f['status'] ?? 'pending'),
                    ucfirst($f['admin_approval_status'] ?? 'pending'),
                    $f['booking_count'] ?? 0,
                    $f['wishlist_count'] ?? 0,
                    $f['created_at'] ?? 'N/A'
                ]);
            }

            fclose($output);
            $this->db->close();
            exit;

        } catch (Exception $e) {
            die("Export error: " . $e->getMessage());
        }
    }

    private function resolveFarmhouseId(string $raw): int {
        if (empty($raw)) {
            throw new Exception("Missing farmhouse_id.");
        }
        if (ctype_digit($raw)) {
            return (int) $raw;
        }
        $decrypted = CryptoHelper::decrypt($raw);
        if (!$decrypted || !ctype_digit($decrypted)) {
            throw new Exception("Invalid or tampered farmhouse_id.");
        }
        return (int) $decrypted;
    }

    // ================================================================
    //  2. ADD FARMHOUSE  →  /admin/addfarm
    // ================================================================

    public function add_farm(): void {
        $upload_dir = farmhouse_upload_path();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title    = trim($_POST['title']    ?? '');
            $location = trim($_POST['location'] ?? '');
            $price    = floatval($_POST['price'] ?? 0);

            if (empty($title) || empty($location) || $price <= 0) {
                $this->flash('error_msg', "Title, Location, and a valid Nightly Price (> 0) are required.");
            } else {
                try {
                    $allowedCategories = ['Guest House', 'Resort', 'Farmhouse', 'Villa'];
                    $rawCategory = trim($_POST['category'] ?? '');
                    $category = in_array($rawCategory, $allowedCategories) ? $rawCategory : 'Farmhouse';

                    $isRoomBooking = isset($_POST['allow_room_booking']) ? 1 : 0;
                    $farmData = [
                        'owner_id'              => !empty($_POST['owner_id'])   ? (int)$_POST['owner_id']   : null,
                        'title'                 => $title,
                        'description'           => trim($_POST['description']   ?? ''),
                        'location'              => $location,
                        'address'               => trim($_POST['address']       ?? ''),
                        'google_map_link'       => trim($_POST['google_map_link'] ?? ''),
                        'category'              => $category,
                        'price'                 => $price,
                        'room_price'            => ($isRoomBooking && !empty($_POST['room_price'])) ? floatval($_POST['room_price']) : null,
                        'allow_room_booking'    => $isRoomBooking,
                        'bedrooms'              => max(1, (int)($_POST['bedrooms']     ?? 1)),
                        'bedroom_capacity'      => max(1, (int)($_POST['bedroom_capacity'] ?? 2)),
                        'day_capacity'          => !empty($_POST['day_capacity'])   ? (int)$_POST['day_capacity']   : null,
                        'night_capacity'        => !empty($_POST['night_capacity']) ? (int)$_POST['night_capacity'] : null,
                        'is_negotiable'         => isset($_POST['is_negotiable'])   ? 1 : 0,
                        'contact_phone'         => trim($_POST['contact_phone'] ?? ''),
                        'whatsapp_number'       => trim($_POST['whatsapp_number'] ?? ''),
                        'owner_notes'           => trim($_POST['owner_notes']   ?? ''),
                        'status'                => $_POST['status']             ?? 'pending',
                        'admin_approval_status' => $_POST['admin_approval_status'] ?? 'pending',
                        'booking_mode'          => in_array($_POST['booking_mode'] ?? '', ['instant', 'request'], true) ? $_POST['booking_mode'] : 'request',
                        'created_by'            => 'admin',
                    ];

                    $selectedAmenities = $_POST['amenities'] ?? [];
                    $rules             = $_POST['rules']     ?? [];
                    $roomTypes         = $isRoomBooking ? ($_POST['room_types'] ?? []) : [];
                    $uploadedImages    = $this->handleImageUploads($_FILES['images'] ?? null, $upload_dir, 'farmhouse_');

                    if ($this->model->createFarmhouse($farmData, $uploadedImages, $selectedAmenities, $rules, $roomTypes)) {
                        $this->flash('success_msg', "New property '{$title}' ({$category}) has been successfully created and published!");
                        redirect('admin/managefarmhouses');
                    }
                } catch (Exception $e) {
                    $this->flash('error_msg', "Failed to create farmhouse: " . $e->getMessage());
                }
            }
        }

        $availableAmenities = $this->model->getAllAmenities();
        $allRulePresets     = $this->model->getRulePresets();
        $owners             = $this->model->getActiveOwners();
        $success_message    = $this->popFlash('success_msg');
        $error_message      = $this->popFlash('error_msg');

        include __DIR__ . '/../../Views/admin/add_farm.php';
        $this->db->close();
    }

    // ================================================================
    //  3. EDIT FARMHOUSE  →  /admin/editfarm?id=<encrypted>
    // ================================================================

    public function edit_farmhouse(): void {
        try {
            $rawId = $_GET['id'] ?? '';
            $id = $this->resolveFarmhouseId($rawId);

            $upload_dir = farmhouse_upload_path();

            // ── POST: save changes ─────────────────────────────────────
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $title    = trim($_POST['title']    ?? '');
                $location = trim($_POST['location'] ?? '');
                $price    = floatval($_POST['price'] ?? 0);

                if (empty($title) || empty($location) || $price <= 0) {
                    $this->flash('error_msg', "Title, Location, and a valid Nightly Price (> 0) are required.");
                } else {
                    $allowedCategories = ['Guest House', 'Resort', 'Farmhouse', 'Villa'];
                    $rawCategory = trim($_POST['category'] ?? '');
                    $category = in_array($rawCategory, $allowedCategories) ? $rawCategory : 'Farmhouse';

                    $isRoomBooking = isset($_POST['allow_room_booking']) ? 1 : 0;
                    $updateData = [
                        'owner_id'              => !empty($_POST['owner_id'])   ? (int)$_POST['owner_id']   : null,
                        'title'                 => $title,
                        'description'           => trim($_POST['description']   ?? ''),
                        'location'              => $location,
                        'address'               => trim($_POST['address']       ?? ''),
                        'google_map_link'       => trim($_POST['google_map_link'] ?? ''),
                        'category'              => $category,
                        'price'                 => $price,
                        'room_price'            => ($isRoomBooking && !empty($_POST['room_price'])) ? floatval($_POST['room_price']) : null,
                        'allow_room_booking'    => $isRoomBooking,
                        'bedrooms'              => max(1, (int)($_POST['bedrooms']     ?? 1)),
                        'bedroom_capacity'      => max(1, (int)($_POST['bedroom_capacity'] ?? 2)),
                        'day_capacity'          => !empty($_POST['day_capacity'])   ? (int)$_POST['day_capacity']   : null,
                        'night_capacity'        => !empty($_POST['night_capacity']) ? (int)$_POST['night_capacity'] : null,
                        'is_negotiable'         => isset($_POST['is_negotiable'])   ? 1 : 0,
                        'contact_phone'         => trim($_POST['contact_phone'] ?? ''),
                        'whatsapp_number'       => trim($_POST['whatsapp_number'] ?? ''),
                        'owner_notes'           => trim($_POST['owner_notes']   ?? ''),
                        'status'                => $_POST['status']            ?? 'pending',
                        'admin_approval_status' => $_POST['admin_approval_status'] ?? 'pending',
                        'booking_mode'          => in_array($_POST['booking_mode'] ?? '', ['instant', 'request'], true) ? $_POST['booking_mode'] : 'request',
                    ];

                    // Update core farmhouse row and room types
                    $roomTypes = $isRoomBooking ? ($_POST['room_types'] ?? []) : [];
                    $saved = $this->model->updateFarmhouse($id, $updateData, $roomTypes);
                    if (!$saved) {
                        $this->flash('error_msg', "Failed to update property details. Please verify your entries.");
                        redirect("admin/editfarm?id=" . urlencode(CryptoHelper::encrypt((string)$id)));
                        return;
                    }

                    // Update amenities (delete-all + re-insert)
                    $amenities = $_POST['amenities'] ?? [];
                    $this->model->updateAmenities($id, $amenities);

                    // Update rules (delete-all + re-insert)
                    $rules = $_POST['rules'] ?? [];
                    $this->model->updateRules($id, $rules);

                    // Remove images the admin checked for deletion
                    if (!empty($_POST['remove_images']) && is_array($_POST['remove_images'])) {
                        $this->model->removeSelectedImages($_POST['remove_images'], $upload_dir);
                    }

                    // Handle newly uploaded images
                    if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
                        $newFilenames = $this->handleImageUploads($_FILES['new_images'], $upload_dir, 'farmhouse_');
                        if (!empty($newFilenames)) {
                            $this->model->addNewImages($id, $newFilenames);
                        }
                    }

                    $this->flash('success_msg', "Farmhouse configuration updates saved successfully!");
                    redirect("admin/editfarm?id=" . urlencode(CryptoHelper::encrypt((string)$id)));
                }
            }

            // ── GET: load edit page ────────────────────────────────────
            $farmhouse = $this->model->getFarmhouseById($id);
            if (!$farmhouse) {
                $this->flash('error_msg', "Property not found.");
                redirect('admin/managefarmhouses');
            }

            $farmhouse['encrypted_id'] = CryptoHelper::encrypt((string)$farmhouse['id']);
            $images           = $this->model->getImages($id);
            $roomTypes        = $this->model->getRoomTypes($id);
            $allAmenities     = $this->model->getAllAmenities();
            $currentAmenities = $this->model->getFarmhouseAmenities($id);
            $currentRules     = $this->model->getFarmhouseRules($id);
            $allRulePresets   = $this->model->getRulePresets();
            $owners           = $this->model->getActiveOwners();
            $success_message  = $this->popFlash('success_msg');
            $error_message    = $this->popFlash('error_msg');

            extract([
                'farmhouse'        => $farmhouse,
                'images'           => $images,
                'roomTypes'        => $roomTypes,
                'allAmenities'     => $allAmenities,
                'currentAmenities' => $currentAmenities,
                'currentRules'     => $currentRules,
                'allRulePresets'   => $allRulePresets,
                'owners'           => $owners,
                'success_message'  => $success_message,
                'error_message'    => $error_message,
            ]);

            include __DIR__ . '/../../Views/admin/edit_farmhouse.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading property editor: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  PRIVATE HELPERS
    // ================================================================

    /**
     * Upload multiple images and return an array of saved filenames.
     *
     * @param array|null $fileArray   $_FILES array
     * @param string     $uploadDir   Absolute path to upload folder
     * @param string     $prefix      Filename prefix (default 'farm_')
     * @return array                  Array of saved filenames (basename only)
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
