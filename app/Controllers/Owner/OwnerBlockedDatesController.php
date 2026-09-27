<?php
namespace App\Controllers\Owner;

use App\Config\Database;
use App\Helpers\CryptoHelper;
use App\Models\Owner\OwnerBlockedDatesModel;
use App\Models\Owner\OwnerProfileModel;
use Exception;

/**
 * OwnerBlockedDatesController
 *
 * Allows owners to manually lock / unlock date ranges for their farmhouses.
 * Blocked dates prevent online bookings for those periods.
 *
 * Routes:
 *   GET  /owner/blocked-dates           → index()   – list + UI
 *   POST /owner/blocked-dates/lock      → lock()    – add a block
 *   POST /owner/blocked-dates/unlock    → unlock()  – remove a block
 */
class OwnerBlockedDatesController {

    private $db;
    private OwnerBlockedDatesModel $model;

    public function __construct() {
        try {
            $this->db    = Database::connect();
            $this->model = new OwnerBlockedDatesModel($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  INDEX  →  GET /owner/blocked-dates
    // ================================================================

    public function index(): void {
        try {
            $ownerId = $this->resolveOwnerId();

            // Selected farmhouse (from query param, optional)
            $rawFarmId    = trim($_GET['farmhouse_id'] ?? '');
            $farmhouseId  = 0;

            // Try to decrypt if it looks encrypted, else use as-is int
            if (!empty($rawFarmId)) {
                if (is_numeric($rawFarmId)) {
                    $farmhouseId = (int)$rawFarmId;
                } else {
                    $dec = CryptoHelper::decrypt($rawFarmId);
                    $farmhouseId = ($dec && is_numeric($dec)) ? (int)$dec : 0;
                }
            }

            $farmhouses  = $this->model->getOwnerFarmhouses($ownerId);

            // Default to first farmhouse if none selected
            if ($farmhouseId <= 0 && !empty($farmhouses)) {
                $farmhouseId = (int)$farmhouses[0]['id'];
            }

            $blockedDates      = ($farmhouseId > 0)
                ? $this->model->getBlockedDates($ownerId, $farmhouseId)
                : [];

            $allBlockedDates   = $this->model->getAllBlockedDatesByOwner($ownerId);

            // Selected farmhouse details
            $selectedFarmhouse = null;
            foreach ($farmhouses as $fh) {
                if ((int)$fh['id'] === $farmhouseId) {
                    $selectedFarmhouse = $fh;
                    break;
                }
            }

            // Owner profile for header
            $profModel = new OwnerProfileModel($this->db);
            $owner     = $profModel->getOwnerById($ownerId) ?? [];

            $success_message = $this->popFlash('owner_bd_success');
            $error_message   = $this->popFlash('owner_bd_error');

            include __DIR__ . '/../../Views/owner/blocked-dates.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading date locks: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  LOCK  →  POST /owner/blocked-dates/lock
    // ================================================================

    public function lock(): void {
        try {
            $ownerId = $this->resolveOwnerId();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('owner/blocked-dates');
            }

            $rawFarmId   = trim($_POST['farmhouse_id'] ?? '');
            $farmhouseId = $this->resolveFarmhouseId($rawFarmId);
            $startDate   = trim($_POST['start_date'] ?? '');
            $endDate     = trim($_POST['end_date']   ?? '');
            $reason      = trim($_POST['reason']     ?? '');

            // --- Validation ---
            if ($farmhouseId <= 0) {
                $this->flash('owner_bd_error', "Please select a valid farmhouse.");
                redirect('owner/blocked-dates');
            }

            if (empty($startDate) || empty($endDate)) {
                $this->flash('owner_bd_error', "Start date and end date are required.");
                redirect('owner/blocked-dates?farmhouse_id=' . $farmhouseId);
            }

            $startTs = strtotime($startDate);
            $endTs   = strtotime($endDate);

            if (!$startTs || !$endTs) {
                $this->flash('owner_bd_error', "Invalid date format supplied.");
                redirect('owner/blocked-dates?farmhouse_id=' . $farmhouseId);
            }

            if ($endTs <= $startTs) {
                $this->flash('owner_bd_error', "End date must be after start date.");
                redirect('owner/blocked-dates?farmhouse_id=' . $farmhouseId);
            }

            // Warn if overlapping approved bookings exist (but still allow locking)
            $overlap = $this->model->countOverlappingApprovedBookings($farmhouseId, $startDate, $endDate);

            if ($this->model->addBlockedDates($ownerId, $farmhouseId, $startDate, $endDate, $reason)) {
                $msg = "✅ Dates locked successfully from " . date('d M Y', $startTs) . " to " . date('d M Y', $endTs) . ".";
                if ($overlap > 0) {
                    $msg .= " ⚠️ Note: {$overlap} already-approved booking(s) overlap these dates — please coordinate with Admin.";
                }
                $this->flash('owner_bd_success', $msg);
            } else {
                $this->flash('owner_bd_error', "Failed to lock dates. Make sure this farmhouse belongs to you.");
            }

            redirect('owner/blocked-dates?farmhouse_id=' . $farmhouseId);

        } catch (Exception $e) {
            $this->flash('owner_bd_error', "Error: " . htmlspecialchars($e->getMessage()));
            redirect('owner/blocked-dates');
        }
    }

    // ================================================================
    //  UNLOCK  →  POST /owner/blocked-dates/unlock
    // ================================================================

    public function unlock(): void {
        try {
            $ownerId = $this->resolveOwnerId();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('owner/blocked-dates');
            }

            $blockId     = (int)($_POST['block_id']     ?? 0);
            $farmhouseId = (int)($_POST['farmhouse_id'] ?? 0);

            if ($blockId <= 0) {
                $this->flash('owner_bd_error', "Invalid block identifier.");
                redirect('owner/blocked-dates');
            }

            if ($this->model->removeBlockedDate($blockId, $ownerId)) {
                $this->flash('owner_bd_success', "Date lock removed successfully. Those dates are now available for booking again.");
            } else {
                $this->flash('owner_bd_error', "Could not remove this lock. You may only remove locks you have set yourself (admin-set locks cannot be removed from here).");
            }

            $redir = 'owner/blocked-dates';
            if ($farmhouseId > 0) {
                $redir .= '?farmhouse_id=' . $farmhouseId;
            }
            redirect($redir);

        } catch (Exception $e) {
            $this->flash('owner_bd_error', "Error: " . htmlspecialchars($e->getMessage()));
            redirect('owner/blocked-dates');
        }
    }

    // ================================================================
    //  PRIVATE HELPERS
    // ================================================================

    private function resolveOwnerId(): int {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id = (int)($_SESSION['user_id'] ?? 0);
        if (!$id) redirect('owner/login');
        return $id;
    }

    private function resolveFarmhouseId(string $raw): int {
        $raw = trim($raw);
        if (is_numeric($raw)) return (int)$raw;
        $dec = CryptoHelper::decrypt($raw);
        return ($dec && is_numeric($dec)) ? (int)$dec : 0;
    }

    private function flash(string $key, string $msg): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[$key] = $msg;
    }

    private function popFlash(string $key): ?string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION[$key])) {
            $m = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $m;
        }
        return null;
    }
}
