<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Helpers\CryptoHelper;
use App\Models\Admin\BookingRequestModel;
use Exception;

class BookingRequestController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function bookingRequests(): void
    {
        try {
            $model = new BookingRequestModel($this->db);

            // ── Handle POST Actions ──────────────────────────────────
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';

                // Single Status Update
                if ($action === 'update_status' || isset($_POST['request_id'])) {
                    $rawId  = $_POST['request_id'] ?? '';
                    $status = $_POST['new_status'] ?? 'pending';
                    $reqId  = $this->resolveId($rawId);

                    if ($model->updateStatus($reqId, $status)) {
                        $this->flash('success_msg', "Booking request #{$reqId} marked as " . ucfirst($status) . ".");
                    } else {
                        $this->flash('error_msg', "Failed to update booking request status.");
                    }
                    redirect('admin/booking-requests');
                }

                // Single Delete
                if ($action === 'delete' || isset($_POST['delete_id'])) {
                    $rawId = $_POST['delete_id'] ?? ($_POST['request_id'] ?? '');
                    $reqId = $this->resolveId($rawId);

                    if ($model->deleteRequest($reqId)) {
                        $this->flash('success_msg', "Booking request #{$reqId} removed from records.");
                    } else {
                        $this->flash('error_msg', "Failed to delete booking request.");
                    }
                    redirect('admin/booking-requests');
                }

                // Bulk Status Update
                if ($action === 'bulk_status') {
                    $rawIds = $_POST['request_ids'] ?? [];
                    $targetStatus = in_array($_POST['target_status'] ?? '', ['pending', 'approved', 'rejected', 'completed', 'cancelled'], true) ? $_POST['target_status'] : 'approved';

                    if (!empty($rawIds) && is_array($rawIds)) {
                        $resolvedIds = [];
                        foreach ($rawIds as $raw) {
                            try {
                                $resolvedIds[] = $this->resolveId($raw);
                            } catch (Exception $e) {}
                        }
                        $count = $model->bulkUpdateStatus($resolvedIds, $targetStatus);
                        $this->flash('success_msg', "Updated status for {$count} booking reservation(s) to " . ucfirst($targetStatus) . ".");
                    } else {
                        $this->flash('error_msg', "No bookings selected for bulk status update.");
                    }
                    redirect('admin/booking-requests');
                }

                // Bulk Delete
                if ($action === 'bulk_delete') {
                    $rawIds = $_POST['request_ids'] ?? [];
                    if (!empty($rawIds) && is_array($rawIds)) {
                        $resolvedIds = [];
                        foreach ($rawIds as $raw) {
                            try {
                                $resolvedIds[] = $this->resolveId($raw);
                            } catch (Exception $e) {}
                        }
                        $count = $model->bulkDelete($resolvedIds);
                        $this->flash('success_msg', "Permanently deleted {$count} booking reservation(s).");
                    } else {
                        $this->flash('error_msg', "No bookings selected for deletion.");
                    }
                    redirect('admin/booking-requests');
                }
            }

            // ── Handle GET / Listing ────────────────────────────────
            $searchQuery  = trim($_GET['q'] ?? '');
            $statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
            $farmFilter   = trim($_GET['farmhouse_id'] ?? '');
            $sortBy       = strtolower(trim($_GET['sort'] ?? 'newest'));
            $perPage      = max(5, min(100, (int) ($_GET['per_page'] ?? 10)));
            $currentPage  = max(1, (int) ($_GET['page'] ?? 1));
            $offset       = ($currentPage - 1) * $perPage;

            $filters = [
                'q'            => $searchQuery,
                'status'       => $statusFilter,
                'farmhouse_id' => $farmFilter,
            ];

            $requests   = $model->getAllRequests($filters, $sortBy, $perPage, $offset);
            $totalCount = $model->getTotalRequestCount($filters);
            $totalPages = max(1, (int) ceil($totalCount / $perPage));
            $stats      = $model->getBookingStats();
            $farmhouses = $model->getFarmhousesList();

            // Encrypt Request IDs
            $requests = array_map(function(array $r): array {
                $r['encrypted_id'] = CryptoHelper::encrypt((string)$r['req_id']);
                return $r;
            }, $requests);

            $success_message = $this->popFlash('success_msg');
            $error_message   = $this->popFlash('error_msg');

            include __DIR__ . '/../../Views/admin/booking-requests.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading bookings: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  AJAX — Booking Details Dossier Inspector
    //  GET /admin/booking-requests/details?id=...
    // ================================================================

    public function bookingDetails(): void
    {
        header('Content-Type: application/json');
        try {
            $rawId = $_GET['id'] ?? '';
            $reqId = $this->resolveId($rawId);

            $model   = new BookingRequestModel($this->db);
            $dossier = $model->getBookingDetails($reqId);

            if (!$dossier) {
                echo json_encode(['success' => false, 'message' => 'Booking request not found.']);
                $this->db->close();
                return;
            }

            $dossier['encrypted_id'] = CryptoHelper::encrypt((string)$dossier['req_id']);
            $dossier['formatted_created'] = !empty($dossier['req_date']) 
                ? date('d M Y, h:i A', strtotime($dossier['req_date'])) 
                : 'N/A';
            
            $checkIn  = !empty($dossier['check_in']) ? $dossier['check_in'] : $dossier['start_date'];
            $checkOut = !empty($dossier['check_out']) ? $dossier['check_out'] : $dossier['end_date'];

            $dossier['formatted_check_in'] = !empty($checkIn) ? date('d M Y', strtotime($checkIn)) : 'Not set';
            $dossier['formatted_check_out'] = !empty($checkOut) ? date('d M Y', strtotime($checkOut)) : 'Not set';
            
            if (!empty($checkIn) && !empty($checkOut)) {
                $days = (int) ceil((strtotime($checkOut) - strtotime($checkIn)) / 86400);
                $dossier['stay_nights'] = max(1, $days);
            } else {
                $dossier['stay_nights'] = 1;
            }

            $dossier['formatted_price'] = number_format((float)($dossier['price'] ?? 0));

            echo json_encode([
                'success' => true,
                'data'    => $dossier
            ]);
            $this->db->close();

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ================================================================
    //  EXPORT BOOKINGS TO CSV
    //  GET /admin/booking-requests/export
    // ================================================================

    public function exportBookings(): void
    {
        try {
            $model = new BookingRequestModel($this->db);
            $allBookings = $model->getAllRequests([], 'newest', 10000, 0);

            $filename = "farmlelo_bookings_export_" . date('Y-m-d_His') . ".csv";

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $output = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($output, [
                'Booking ID',
                'Guest Name',
                'Guest Phone',
                'Guest Email',
                'Farmhouse Title',
                'Farmhouse Location',
                'Check-In Date',
                'Check-Out Date',
                'Guests Count',
                'Total Price (INR)',
                'Booking Status',
                'Host Name',
                'Host Phone',
                'Request Timestamp',
                'Customer Message'
            ]);

            foreach ($allBookings as $b) {
                $checkIn  = !empty($b['check_in']) ? $b['check_in'] : ($b['start_date'] ?? 'N/A');
                $checkOut = !empty($b['check_out']) ? $b['check_out'] : ($b['end_date'] ?? 'N/A');

                fputcsv($output, [
                    $b['req_id'],
                    $b['cust_name'] ?? 'Guest User',
                    $b['cust_phone'] ?? 'N/A',
                    $b['cust_email'] ?? 'N/A',
                    $b['farm_title'] ?? 'Untitled Estate',
                    $b['farm_location'] ?? 'N/A',
                    $checkIn,
                    $checkOut,
                    $b['guests'] ?? 1,
                    $b['price'] ?? 0,
                    ucfirst($b['req_status'] ?? 'pending'),
                    $b['host_name'] ?? 'Unassigned',
                    $b['host_phone'] ?? 'N/A',
                    $b['req_date'] ?? 'N/A',
                    $b['message'] ?? ''
                ]);
            }

            fclose($output);
            $this->db->close();
            exit;

        } catch (Exception $e) {
            die("Export error: " . $e->getMessage());
        }
    }

    // ================================================================
    //  ADMIN DATE LOCKS (BLOCKED DATES) MANAGEMENT
    //  GET /admin/blocked-dates
    // ================================================================

    public function blockedDates(): void
    {
        try {
            $model = new BookingRequestModel($this->db);

            $searchQuery  = trim($_GET['q'] ?? '');
            $farmFilter   = trim($_GET['farmhouse_id'] ?? '');
            $lockedBy     = strtolower(trim($_GET['locked_by'] ?? 'all'));

            $filters = [
                'q'            => $searchQuery,
                'farmhouse_id' => $farmFilter,
                'locked_by'    => $lockedBy,
            ];

            $blockedDates = $model->getAllBlockedDates($filters);
            $farmhouses   = $model->getFarmhousesList();

            $success_message = $this->popFlash('admin_bd_success');
            $error_message   = $this->popFlash('admin_bd_error');

            include __DIR__ . '/../../Views/admin/blocked-dates.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading admin date locks: " . htmlspecialchars($e->getMessage()));
        }
    }

    public function lockDate(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('admin/blocked-dates');
            }

            $model       = new BookingRequestModel($this->db);
            $farmhouseId = (int)($_POST['farmhouse_id'] ?? 0);
            $startDate   = trim($_POST['start_date'] ?? '');
            $endDate     = trim($_POST['end_date'] ?? '');
            $reason      = trim($_POST['reason'] ?? '');

            if ($farmhouseId <= 0 || empty($startDate) || empty($endDate)) {
                $this->flash('admin_bd_error', 'Farmhouse, start date, and end date are required.');
                redirect('admin/blocked-dates');
            }

            if (strtotime($endDate) <= strtotime($startDate)) {
                $this->flash('admin_bd_error', 'End date must be after start date.');
                redirect('admin/blocked-dates');
            }

            if ($model->addBlockedDate($farmhouseId, $startDate, $endDate, $reason, 'admin')) {
                $this->flash('admin_bd_success', 'Date lock successfully added.');
            } else {
                $this->flash('admin_bd_error', 'Failed to add date lock.');
            }

            redirect('admin/blocked-dates');

        } catch (Exception $e) {
            $this->flash('admin_bd_error', 'Error: ' . htmlspecialchars($e->getMessage()));
            redirect('admin/blocked-dates');
        }
    }

    public function unlockDate(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('admin/blocked-dates');
            }

            $model   = new BookingRequestModel($this->db);
            $blockId = (int)($_POST['block_id'] ?? 0);

            if ($blockId <= 0) {
                $this->flash('admin_bd_error', 'Invalid lock identifier.');
                redirect('admin/blocked-dates');
            }

            if ($model->deleteBlockedDate($blockId)) {
                $this->flash('admin_bd_success', 'Date lock removed. Dates are available for booking again.');
            } else {
                $this->flash('admin_bd_error', 'Failed to remove date lock.');
            }

            redirect('admin/blocked-dates');

        } catch (Exception $e) {
            $this->flash('admin_bd_error', 'Error: ' . htmlspecialchars($e->getMessage()));
            redirect('admin/blocked-dates');
        }
    }

    // ================================================================
    //  PRIVATE HELPERS
    // ================================================================

    private function resolveId(string $raw): int
    {
        if (empty($raw)) {
            throw new Exception("Missing ID.");
        }
        if (ctype_digit($raw)) {
            return (int) $raw;
        }
        $decrypted = CryptoHelper::decrypt($raw);
        if (!$decrypted || !ctype_digit($decrypted)) {
            throw new Exception("Invalid or tampered ID.");
        }
        return (int) $decrypted;
    }

    private function flash(string $key, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[$key] = $message;
    }

    private function popFlash(string $key): ?string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $msg = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $msg;
    }
}