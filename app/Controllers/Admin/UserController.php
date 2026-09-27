<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Helpers\CryptoHelper;
use App\Models\Admin\UserModel;
use Exception;

class UserController
{
    // ============================================================
    //  MAIN ENTRY — /admin/users
    // ============================================================

    public function users(): void
    {
        try {
            $db    = Database::connect();
            $model = new UserModel($db);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';

                // ── ADD USER ────────────────────────────────────
                if ($action === 'add_user') {
                    $this->validateUserInput($_POST, true);

                    $model->createUser([
                        'name'          => trim($_POST['name']),
                        'email'         => trim($_POST['email']),
                        'phone'         => trim($_POST['phone']),
                        'gender'        => $_POST['gender']        ?? null,
                        'date_of_birth' => $_POST['date_of_birth'] ?? null,
                        'password'      => $_POST['password'],
                        'status'        => in_array($_POST['status'] ?? '', ['active', 'blocked']) ? $_POST['status'] : 'active',
                    ]);
                    $this->redirectSuccess("User account created successfully.");
                }

                // ── EDIT USER ───────────────────────────────────
                if ($action === 'edit_user') {
                    $userId = $this->resolveUserId($_POST['user_id'] ?? '');
                    $this->validateUserInput($_POST, false);

                    $model->updateUser($userId, [
                        'name'          => trim($_POST['name']),
                        'email'         => trim($_POST['email']),
                        'phone'         => trim($_POST['phone']),
                        'gender'        => $_POST['gender']        ?? null,
                        'date_of_birth' => $_POST['date_of_birth'] ?? null,
                        'status'        => in_array($_POST['status'] ?? '', ['active', 'blocked']) ? $_POST['status'] : 'active',
                        'password'      => $_POST['password']      ?? '',
                    ]);
                    $this->redirectSuccess("User account updated successfully.");
                }

                // ── TOGGLE STATUS ───────────────────────────────
                if ($action === 'toggle_status') {
                    $userId = $this->resolveUserId($_POST['user_id'] ?? '');
                    $status = ($_POST['status'] === 'active') ? 'active' : 'blocked';
                    
                    $model->updateStatus($userId, $status);
                    
                    if ($status === 'blocked') {
                        $model->forceLogout($userId);
                        $this->redirectSuccess("User account suspended and active session terminated.");
                    } else {
                        $this->redirectSuccess("User account activated successfully.");
                    }
                }

                // ── BULK STATUS ─────────────────────────────────
                if ($action === 'bulk_status') {
                    $rawIds = $_POST['user_ids'] ?? [];
                    $targetStatus = in_array($_POST['target_status'] ?? '', ['active', 'blocked']) ? $_POST['target_status'] : 'active';
                    
                    if (!empty($rawIds) && is_array($rawIds)) {
                        $resolvedIds = [];
                        foreach ($rawIds as $raw) {
                            try {
                                $resolvedIds[] = $this->resolveUserId($raw);
                            } catch (Exception $e) {
                                // Skip invalid
                            }
                        }
                        $count = $model->bulkUpdateStatus($resolvedIds, $targetStatus);
                        $this->redirectSuccess("Updated status for {$count} user(s) to {$targetStatus}.");
                    }
                    $this->redirectError("No users selected for bulk action.");
                }

                // ── BULK DELETE ─────────────────────────────────
                if ($action === 'bulk_delete') {
                    $rawIds = $_POST['user_ids'] ?? [];
                    if (!empty($rawIds) && is_array($rawIds)) {
                        $resolvedIds = [];
                        foreach ($rawIds as $raw) {
                            try {
                                $resolvedIds[] = $this->resolveUserId($raw);
                            } catch (Exception $e) {
                                // Skip invalid
                            }
                        }
                        $count = $model->bulkDelete($resolvedIds);
                        $this->redirectSuccess("Permanently deleted {$count} user(s).");
                    }
                    $this->redirectError("No users selected for deletion.");
                }

                // ── FORCE LOGOUT ────────────────────────────────
                if ($action === 'force_logout') {
                    $userId = $this->resolveUserId($_POST['user_id'] ?? '');
                    $model->forceLogout($userId);
                    $this->redirectSuccess("User session formally terminated.");
                }

                // ── DELETE USER ─────────────────────────────────
                if ($action === 'delete') {
                    $userId = $this->resolveUserId($_POST['user_id'] ?? '');
                    $model->deleteUser($userId);
                    $this->redirectSuccess("User permanently deleted from system.");
                }
            }

            // ── LOAD VIEW DATA ──────────────────────────────────
            $allUsers = $model->getAllUsers();
            $stats    = $model->getUserStats();

            // Encrypt each user's ID
            $allUsers = array_map(function (array $u): array {
                $u['encrypted_id'] = CryptoHelper::encrypt((string) $u['id']);
                return $u;
            }, $allUsers);

            // Filtering parameters
            $searchQuery  = trim($_GET['q'] ?? '');
            $statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
            $sortBy       = strtolower(trim($_GET['sort'] ?? 'newest'));

            $filteredUsers = array_filter($allUsers, function($u) use ($searchQuery, $statusFilter) {
                // Search match
                if ($searchQuery !== '') {
                    $haystack = strtolower($u['name'] . ' ' . $u['email'] . ' ' . $u['phone'] . ' ' . ($u['gender'] ?? ''));
                    if (strpos($haystack, strtolower($searchQuery)) === false) {
                        return false;
                    }
                }

                // Status match
                if ($statusFilter === 'active' && $u['status'] !== 'active') return false;
                if ($statusFilter === 'blocked' && $u['status'] !== 'blocked') return false;
                if ($statusFilter === 'online' && empty($u['is_online'])) return false;

                return true;
            });

            // Sorting
            usort($filteredUsers, function($a, $b) use ($sortBy) {
                return match($sortBy) {
                    'oldest'        => strtotime($a['created_at']) <=> strtotime($b['created_at']),
                    'name_asc'      => strcasecmp($a['name'], $b['name']),
                    'name_desc'     => strcasecmp($b['name'], $a['name']),
                    'most_bookings' => ($b['booking_count'] ?? 0) <=> ($a['booking_count'] ?? 0),
                    'most_wishlist' => ($b['wishlist_count'] ?? 0) <=> ($a['wishlist_count'] ?? 0),
                    default         => strtotime($b['created_at']) <=> strtotime($a['created_at']),
                };
            });

            // Pagination calculations
            $perPage     = max(5, min(100, (int) ($_GET['per_page'] ?? 10)));
            $totalCount  = count($filteredUsers);
            $totalPages  = max(1, (int) ceil($totalCount / $perPage));
            $currentPage = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));
            $offset      = ($currentPage - 1) * $perPage;

            // Paginated slice
            $users = array_slice($filteredUsers, $offset, $perPage);

            if (session_status() === PHP_SESSION_NONE) session_start();
            $success_message = $_SESSION['user_success'] ?? null;
            $error_message   = $_SESSION['user_error'] ?? null;
            unset($_SESSION['user_success'], $_SESSION['user_error']);

            include __DIR__ . '/../../Views/admin/users.php';
            $db->close();

        } catch (Exception $e) {
            die("Error loading users: " . $e->getMessage());
        }
    }

    // ============================================================
    //  AJAX — User Details Dossier Inspector
    //  GET /admin/users/details?id=...
    // ============================================================

    public function userDetails(): void
    {
        header('Content-Type: application/json');
        try {
            $rawId = $_GET['id'] ?? '';
            $userId = $this->resolveUserId($rawId);

            $db = Database::connect();
            $model = new UserModel($db);
            $dossier = $model->getUserDetails($userId);

            if (!$dossier) {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
                $db->close();
                return;
            }

            $dossier['user']['encrypted_id'] = CryptoHelper::encrypt((string) $dossier['user']['id']);
            
            // Format dates
            $dossier['user']['formatted_joined'] = !empty($dossier['user']['created_at']) 
                ? date('d M Y, h:i A', strtotime($dossier['user']['created_at'])) 
                : 'N/A';
            $dossier['user']['formatted_last_login'] = !empty($dossier['user']['last_login']) 
                ? date('d M Y, h:i A', strtotime($dossier['user']['last_login'])) 
                : 'Never logged in';
            $dossier['user']['formatted_dob'] = !empty($dossier['user']['date_of_birth']) 
                ? date('d M Y', strtotime($dossier['user']['date_of_birth'])) 
                : 'Not specified';

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
    //  AJAX — Live online/offline badge refresh
    //  GET /admin/users/online-status
    // ============================================================

    public function onlineStatus(): void
    {
        header('Content-Type: application/json');
        try {
            $db     = Database::connect();
            $model  = new UserModel($db);
            
            $stats  = $model->getUserStats();
            $users  = $model->getAllUsers();
            $map    = [];
            
            foreach ($users as $u) {
                $encId       = CryptoHelper::encrypt((string) $u['id']);
                $map[$encId] = (bool) $u['is_online']; 
            }

            echo json_encode([
                'success'      => true,
                'online_count' => $stats['online'],
                'user_status'  => $map,
            ]);
            
            $db->close();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ============================================================
    //  EXPORT USERS TO CSV
    //  GET /admin/users/export
    // ============================================================

    public function exportUsers(): void
    {
        try {
            $db = Database::connect();
            $model = new UserModel($db);
            $users = $model->getAllUsers();

            $filename = "farmlelo_users_export_" . date('Y-m-d_His') . ".csv";

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $output = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($output, [
                'ID',
                'Full Name',
                'Email',
                'Phone',
                'Gender',
                'Date of Birth',
                'Status',
                'Online Status',
                'Total Bookings',
                'Wishlist Items',
                'Inquiries Submitted',
                'Registration Date',
                'Last Login Time'
            ]);

            foreach ($users as $u) {
                fputcsv($output, [
                    $u['id'],
                    $u['name'],
                    $u['email'] ?? 'N/A',
                    $u['phone'] ?? 'N/A',
                    ucfirst($u['gender'] ?? 'Not specified'),
                    $u['date_of_birth'] ?? 'N/A',
                    ucfirst($u['status'] ?? 'active'),
                    !empty($u['is_online']) ? 'Online' : 'Offline',
                    $u['booking_count'] ?? 0,
                    $u['wishlist_count'] ?? 0,
                    $u['inquiry_count'] ?? 0,
                    $u['created_at'] ?? 'N/A',
                    $u['last_login'] ?? 'Never'
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

    private function resolveUserId(string $raw): int
    {
        if (empty($raw)) {
            throw new Exception("Missing user_id.");
        }

        if (ctype_digit($raw)) {
            return (int) $raw;
        }

        $decrypted = CryptoHelper::decrypt($raw);
        if (!$decrypted || !ctype_digit($decrypted)) {
            throw new Exception("Invalid or tampered user_id.");
        }
        return (int) $decrypted;
    }

    private function validateUserInput(array $data, bool $requirePw): void
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
            $this->redirectError("Password is required when creating a new user.");
        }

        if (!empty($data['password']) && strlen($data['password']) < 6) {
            $this->redirectError("Password must be at least 6 characters.");
        }
    }

    private function redirectSuccess(string $msg): never
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user_success'] = $msg;
        redirect('admin/users');
    }

    private function redirectError(string $msg): never
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user_error'] = $msg;
        redirect('admin/users');
    }
}