<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Helpers\CryptoHelper;
use mysqli;
use Exception;

class PaymentController
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    private function flash(string $key, string $msg): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[$key] = $msg;
    }

    private function popFlash(string $key): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION[$key])) {
            $msg = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $msg;
        }
        return null;
    }

    /**
     * Admin Payment Verification & Proof Inspection Desk (F41)
     */
    public function payments(): void
    {
        try {
            $statusFilter = trim($_GET['status'] ?? 'all');
            $methodFilter = trim($_GET['method'] ?? 'all');
            $search       = trim($_GET['q'] ?? '');

            // ── Summary KPI Metrics ───────────────────────────────────────
            $statsSql = "SELECT 
                COUNT(*) AS total_count,
                SUM(CASE WHEN payment_status = 'paid' THEN price ELSE 0 END) AS total_verified_amount,
                SUM(CASE WHEN payment_status = 'pending_verification' THEN 1 ELSE 0 END) AS pending_proofs_count,
                SUM(CASE WHEN payment_status = 'unpaid' THEN 1 ELSE 0 END) AS unpaid_count,
                SUM(CASE WHEN payment_proof IS NOT NULL OR utr_number IS NOT NULL THEN 1 ELSE 0 END) AS with_proof_count
            FROM booking_requests";
            $statsRes = $this->db->query($statsSql);
            $stats = $statsRes ? $statsRes->fetch_assoc() : [];

            // ── Build Filtered Query ─────────────────────────────────────
            $sql = "SELECT 
                        br.id AS req_id,
                        br.user_id,
                        br.farmhouse_id,
                        br.booking_type,
                        br.room_type_name,
                        br.check_in,
                        br.check_out,
                        br.guests,
                        br.rooms,
                        br.price,
                        br.status AS booking_status,
                        br.payment_method,
                        br.payment_status,
                        br.utr_number,
                        br.payment_proof,
                        br.payment_verified_at,
                        br.payment_notes,
                        br.created_at AS req_date,
                        COALESCE(u.name, 'Guest') AS cust_name,
                        u.email AS cust_email,
                        u.phone AS cust_phone,
                        COALESCE(f.title, CONCAT('Property #', br.farmhouse_id)) AS farm_title,
                        COALESCE(f.location, '') AS farm_location,
                        (SELECT image_url FROM images i WHERE i.farmhouse_id = f.id ORDER BY i.id ASC LIMIT 1) AS farm_thumb
                    FROM booking_requests br
                    LEFT JOIN users u ON br.user_id = u.id
                    LEFT JOIN farmhouses f ON br.farmhouse_id = f.id";

            $where = [];

            if ($statusFilter !== 'all' && in_array($statusFilter, ['paid', 'pending_verification', 'unpaid', 'rejected', 'refunded'], true)) {
                $statusEsc = $this->db->real_escape_string($statusFilter);
                $where[] = "br.payment_status = '{$statusEsc}'";
            }

            if ($methodFilter !== 'all' && in_array($methodFilter, ['upi', 'bank_transfer', 'pay_at_property'], true)) {
                $methodEsc = $this->db->real_escape_string($methodFilter);
                $where[] = "br.payment_method = '{$methodEsc}'";
            }

            if (!empty($search)) {
                $searchEsc = $this->db->real_escape_string($search);
                $where[] = "(br.id LIKE '%{$searchEsc}%' OR br.utr_number LIKE '%{$searchEsc}%' OR u.name LIKE '%{$searchEsc}%' OR u.email LIKE '%{$searchEsc}%' OR u.phone LIKE '%{$searchEsc}%' OR f.title LIKE '%{$searchEsc}%')";
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }

            $sql .= " ORDER BY (br.payment_status = 'pending_verification') DESC, br.created_at DESC LIMIT 100";

            $res = $this->db->query($sql);
            $payments = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

            $success_message = $this->popFlash('success_msg');
            $error_message   = $this->popFlash('error_msg');

            include __DIR__ . '/../../Views/admin/payments.php';
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Verify Payment and Mark Paid (F41)
     */
    public function verify(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/payments');
            return;
        }

        try {
            $reqId = (int)($_POST['request_id'] ?? 0);
            $notes = trim($_POST['notes'] ?? 'Payment verified by Admin.');
            $autoApprove = !empty($_POST['auto_approve_booking']);

            if (!$reqId) {
                $this->flash('error_msg', 'Invalid booking request ID.');
                redirect('admin/payments');
                return;
            }

            $this->db->begin_transaction();

            // Update booking_requests
            $sql = "UPDATE booking_requests SET 
                        payment_status = 'paid', 
                        payment_verified_at = NOW(), 
                        payment_notes = ?" . ($autoApprove ? ", status = 'approved'" : "") . " 
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("si", $notes, $reqId);
            $stmt->execute();
            $stmt->close();

            // Update payments ledger
            $pSql = "UPDATE payments SET 
                        payment_status = 'paid', 
                        verified_at = NOW(), 
                        admin_notes = ? 
                     WHERE booking_id = ?";
            $pStmt = $this->db->prepare($pSql);
            $pStmt->bind_param("si", $notes, $reqId);
            $pStmt->execute();
            $pStmt->close();

            $this->db->commit();
            $this->flash('success_msg', "Payment for Booking #{$reqId} successfully verified & marked as Paid.");

            // Dispatch in-app notification & payment receipt email (F56, F57, F58)
            try {
                require_once __DIR__ . '/../../Services/NotificationService.php';
                require_once __DIR__ . '/../../Services/MailNotificationService.php';
                $notifService = new \App\Services\NotificationService($this->db);
                
                $stmtBk = $this->db->prepare("SELECT b.*, f.title as farmhouse_title, u.email as customer_email, u.name as customer_name, u.phone as customer_phone FROM booking_requests b JOIN farmhouses f ON b.farmhouse_id = f.id LEFT JOIN users u ON b.user_id = u.id WHERE b.id = ?");
                if ($stmtBk) {
                    $stmtBk->bind_param("i", $reqId);
                    $stmtBk->execute();
                    $bk = $stmtBk->get_result()->fetch_assoc();
                    if ($bk) {
                        $notifService->notify(
                            $bk['user_id'] ? (int)$bk['user_id'] : null,
                            'customer',
                            'Payment Verified! 🧾',
                            "Your payment for booking #BK-{$reqId} at {$bk['farmhouse_title']} has been verified and confirmed.",
                            'booking/voucher?id=' . $reqId,
                            'payment'
                        );
                        $mailService = new \App\Services\MailNotificationService($this->db);
                        $mailService->triggerPaymentVerified($bk);
                    }
                }
            } catch (\Throwable $t) {
                error_log("Payment verification notification dispatch error: " . $t->getMessage());
            }
        } catch (Exception $e) {
            $this->db->rollback();
            $this->flash('error_msg', "Payment verification failed: " . $e->getMessage());
        }

        redirect('admin/payments');
    }

    /**
     * Reject Payment Proof (F41)
     */
    public function reject(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/payments');
            return;
        }

        try {
            $reqId = (int)($_POST['request_id'] ?? 0);
            $notes = trim($_POST['rejection_reason'] ?? 'Invalid payment proof / UTR mismatch.');

            if (!$reqId) {
                $this->flash('error_msg', 'Invalid booking request ID.');
                redirect('admin/payments');
                return;
            }

            $this->db->begin_transaction();

            $sql = "UPDATE booking_requests SET 
                        payment_status = 'unpaid', 
                        payment_notes = CONCAT(COALESCE(payment_notes, ''), '\n[Rejected]: ', ?) 
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("si", $notes, $reqId);
            $stmt->execute();
            $stmt->close();

            $pSql = "UPDATE payments SET 
                        payment_status = 'rejected', 
                        admin_notes = ? 
                     WHERE booking_id = ?";
            $pStmt = $this->db->prepare($pSql);
            $pStmt->bind_param("si", $notes, $reqId);
            $pStmt->execute();
            $pStmt->close();

            $this->db->commit();
            $this->flash('success_msg', "Payment proof for Booking #{$reqId} marked as rejected (Set to Unpaid).");
        } catch (Exception $e) {
            $this->db->rollback();
            $this->flash('error_msg', "Rejection failed: " . $e->getMessage());
        }

        redirect('admin/payments');
    }
}
