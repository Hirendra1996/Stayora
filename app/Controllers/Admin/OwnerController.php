<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Helpers\CryptoHelper;
use App\Models\Admin\OwnerModel;
use Exception;

class OwnerController
{
    public function owners(): void
    {
        try {
            $db    = Database::connect();
            $model = new OwnerModel($db);

            // Handle Post Actions
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';

                // ── ADD OWNER ───────────────────────────────────
                if ($action === 'add_owner') {
                    $this->validateOwnerInput($_POST, true);

                    $model->createOwner([
                        'name'     => trim($_POST['name']),
                        'email'    => trim($_POST['email']),
                        'phone'    => trim($_POST['phone']),
                        'password' => $_POST['password'],
                        'status'   => in_array($_POST['status'] ?? '', ['active', 'disabled']) ? $_POST['status'] : 'active'
                    ]);
                    $this->redirectSuccess("Owner account created successfully.");
                }

                // ── EDIT OWNER ──────────────────────────────────
                if ($action === 'edit_owner') {
                    $ownerId = $this->resolveOwnerId($_POST['owner_id'] ?? '');
                    $this->validateOwnerInput($_POST, false);

                    $model->updateOwner($ownerId, [
                        'name'     => trim($_POST['name']),
                        'email'    => trim($_POST['email']),
                        'phone'    => trim($_POST['phone']),
                        'status'   => in_array($_POST['status'] ?? '', ['active', 'disabled']) ? $_POST['status'] : 'active',
                        'password' => $_POST['password'] ?? null 
                    ]);
                    $this->redirectSuccess("Owner account updated successfully.");
                }

                // ── TOGGLE STATUS ───────────────────────────────
                if ($action === 'toggle_status') {
                    $ownerId = $this->resolveOwnerId($_POST['owner_id'] ?? '');
                    $status = ($_POST['status'] === 'active') ? 'active' : 'disabled';
                    
                    $model->updateStatus($ownerId, $status);
                    if ($status === 'disabled') {
                        $this->redirectSuccess("Owner account suspended and active sessions terminated.");
                    } else {
                        $this->redirectSuccess("Owner account activated successfully.");
                    }
                }

                // ── BULK STATUS ─────────────────────────────────
                if ($action === 'bulk_status') {
                    $rawIds = $_POST['owner_ids'] ?? [];
                    $targetStatus = in_array($_POST['target_status'] ?? '', ['active', 'disabled']) ? $_POST['target_status'] : 'active';
                    
                    if (!empty($rawIds) && is_array($rawIds)) {
                        $resolvedIds = [];
                        foreach ($rawIds as $raw) {
                            try {
                                $resolvedIds[] = $this->resolveOwnerId($raw);
                            } catch (Exception $e) {}
                        }
                        $count = $model->bulkUpdateStatus($resolvedIds, $targetStatus);
                        $this->redirectSuccess("Updated status for {$count} owner(s) to {$targetStatus}.");
                    }
                    $this->redirectError("No owners selected for bulk action.");
                }

                // ── BULK DELETE ─────────────────────────────────
                if ($action === 'bulk_delete') {
                    $rawIds = $_POST['owner_ids'] ?? [];
                    if (!empty($rawIds) && is_array($rawIds)) {
                        $resolvedIds = [];
                        foreach ($rawIds as $raw) {
                            try {
                                $resolvedIds[] = $this->resolveOwnerId($raw);
                            } catch (Exception $e) {}
                        }
                        $count = $model->bulkDelete($resolvedIds);
                        $this->redirectSuccess("Permanently deleted {$count} owner(s) and unassigned their properties.");
                    }
                    $this->redirectError("No owners selected for deletion.");
                }

                // ── FORCE LOGOUT ────────────────────────────────
                if ($action === 'force_logout') {
                    $ownerId = $this->resolveOwnerId($_POST['owner_id'] ?? '');
                    $model->forceLogout($ownerId);
                    $this->redirectSuccess("Owner session formally terminated.");
                }

                // ── DELETE OWNER ────────────────────────────────
                if ($action === 'delete') {
                    $ownerId = $this->resolveOwnerId($_POST['owner_id'] ?? '');
                    $model->deleteOwner($ownerId);
                    $this->redirectSuccess("Owner deleted and their listed properties were unassigned.");
                }
            }

            // Fetch all owners & statistics
            $allOwners = $model->getAllOwners();
            $stats     = $model->getOwnerStats();

            // Encrypt each owner's ID
            $allOwners = array_map(function (array $o): array {
                $o['encrypted_id'] = CryptoHelper::encrypt((string) $o['id']);
                return $o;
            }, $allOwners);

            // Filtering parameters
            $searchQuery  = trim($_GET['q'] ?? '');
            $statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
            $sortBy       = strtolower(trim($_GET['sort'] ?? 'newest'));

            $filteredOwners = array_filter($allOwners, function($o) use ($searchQuery, $statusFilter) {
                // Search match
                if ($searchQuery !== '') {
                    $haystack = strtolower($o['name'] . ' ' . $o['email'] . ' ' . $o['phone']);
                    if (strpos($haystack, strtolower($searchQuery)) === false) {
                        return false;
                    }
                }

                // Status match
                if ($statusFilter === 'active' && strtolower($o['status'] ?? '') !== 'active') return false;
                if ($statusFilter === 'disabled' && strtolower($o['status'] ?? '') !== 'disabled') return false;
                if ($statusFilter === 'has_properties' && (int)($o['property_count'] ?? 0) === 0) return false;
                if ($statusFilter === 'online' && empty($o['is_online'])) return false;

                return true;
            });

            // Sorting
            usort($filteredOwners, function($a, $b) use ($sortBy) {
                return match($sortBy) {
                    'oldest'          => strtotime($a['created_at']) <=> strtotime($b['created_at']),
                    'name_asc'        => strcasecmp($a['name'], $b['name']),
                    'name_desc'       => strcasecmp($b['name'], $a['name']),
                    'most_properties' => ($b['property_count'] ?? 0) <=> ($a['property_count'] ?? 0),
                    'most_bookings'   => ($b['total_bookings_received'] ?? 0) <=> ($a['total_bookings_received'] ?? 0),
                    default           => strtotime($b['created_at']) <=> strtotime($a['created_at']),
                };
            });

            // Pagination calculations
            $perPage     = max(5, min(100, (int) ($_GET['per_page'] ?? 10)));
            $totalCount  = count($filteredOwners);
            $totalPages  = max(1, (int) ceil($totalCount / $perPage));
            $currentPage = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));
            $offset      = ($currentPage - 1) * $perPage;

            // Paginated slice
            $owners = array_slice($filteredOwners, $offset, $perPage);

            // Handle Session messages
            if (session_status() === PHP_SESSION_NONE) session_start();
            $success_message = $_SESSION['owner_success'] ?? null;
            $error_message   = $_SESSION['owner_error'] ?? null;
            unset($_SESSION['owner_success'], $_SESSION['owner_error']);

            include __DIR__ . '/../../Views/admin/owners.php';
            $db->close();

        } catch (Exception $e) {
            die("Error loading owners: " . $e->getMessage());
        }
    }

    // ============================================================
    //  AJAX — Owner Details Dossier Inspector
    //  GET /admin/owners/details?id=...
    // ============================================================

    public function ownerDetails(): void
    {
        header('Content-Type: application/json');
        try {
            $rawId = $_GET['id'] ?? '';
            $ownerId = $this->resolveOwnerId($rawId);

            $db = Database::connect();
            $model = new OwnerModel($db);
            $dossier = $model->getOwnerDetails($ownerId);

            if (!$dossier) {
                echo json_encode(['success' => false, 'message' => 'Owner not found.']);
                $db->close();
                return;
            }

            $dossier['owner']['encrypted_id'] = CryptoHelper::encrypt((string) $dossier['owner']['id']);
            
            // Format dates
            $dossier['owner']['formatted_joined'] = !empty($dossier['owner']['created_at']) 
                ? date('d M Y, h:i A', strtotime($dossier['owner']['created_at'])) 
                : 'N/A';
            $dossier['owner']['formatted_last_login'] = !empty($dossier['owner']['last_login']) 
                ? date('d M Y, h:i A', strtotime($dossier['owner']['last_login'])) 
                : 'Never logged in';

            echo json_encode([
                'success' => true,
                'data'    => $dossier,
            ]);
            $db->close();

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ============================================================
    //  EXPORT OWNERS TO CSV
    //  GET /admin/owners/export
    // ============================================================

    public function exportOwners(): void
    {
        try {
            $db = Database::connect();
            $model = new OwnerModel($db);
            $owners = $model->getAllOwners();

            $filename = "farmlelo_owners_export_" . date('Y-m-d_His') . ".csv";

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $output = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($output, [
                'ID',
                'Owner Name',
                'Email',
                'Phone',
                'Status',
                'Online Presence',
                'Total Properties Listed',
                'Total Bookings Received',
                'Registration Date',
                'Last Login Time'
            ]);

            foreach ($owners as $o) {
                fputcsv($output, [
                    $o['id'],
                    $o['name'],
                    $o['email'] ?? 'N/A',
                    $o['phone'] ?? 'N/A',
                    ucfirst($o['status'] ?? 'active'),
                    !empty($o['is_online']) ? 'Online' : 'Offline',
                    $o['property_count'] ?? 0,
                    $o['total_bookings_received'] ?? 0,
                    $o['created_at'] ?? 'N/A',
                    $o['last_login'] ?? 'Never'
                ]);
            }

            fclose($output);
            $db->close();
            exit;

        } catch (Exception $e) {
            die("Export error: " . $e->getMessage());
        }
    }

    // ============================================================
    //  PRIVATE HELPERS
    // ============================================================

    private function resolveOwnerId(string $raw): int
    {
        if (empty($raw)) {
            throw new Exception("Missing owner_id.");
        }

        if (ctype_digit($raw)) {
            return (int) $raw;
        }

        $decrypted = CryptoHelper::decrypt($raw);
        if (!$decrypted || !ctype_digit($decrypted)) {
            throw new Exception("Invalid or tampered owner_id.");
        }
        return (int) $decrypted;
    }

    private function validateOwnerInput(array $data, bool $requirePw): void
    {
        if (empty(trim($data['name'] ?? ''))) {
            $this->redirectError("Name is required.");
        }

        if (empty(trim($data['email'] ?? '')) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->redirectError("A valid email address is required.");
        }

        if (empty(trim($data['phone'] ?? ''))) {
            $this->redirectError("Phone number is required.");
        }

        if ($requirePw && empty($data['password'])) {
            $this->redirectError("Password is required when creating a new owner.");
        }

        if (!empty($data['password']) && strlen($data['password']) < 6) {
            $this->redirectError("Password must be at least 6 characters.");
        }
    }

    private function redirectSuccess(string $msg): never
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['owner_success'] = $msg;
        redirect('admin/owners');
    }

    private function redirectError(string $msg): never
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['owner_error'] = $msg;
        redirect('admin/owners');
    }
}