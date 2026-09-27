<?php
namespace App\Controllers\Owner;

use App\Config\Database;
use Exception;

class OwnerEarningsController {
    private $db;

    public function __construct() {
        try {
            $this->db = Database::connect();
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    private function getOwnerId(): int {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    public function index(): void {
        $ownerId = $this->getOwnerId();
        if (!$ownerId) {
            redirect('owner/login');
        }

        // Get owner farmhouses
        $stmtFarms = $this->db->prepare("SELECT id, title FROM farmhouses WHERE owner_id = ?");
        $stmtFarms->bind_param("i", $ownerId);
        $stmtFarms->execute();
        $farmhouses = $stmtFarms->get_result()->fetch_all(MYSQLI_ASSOC);

        $farmIds = array_column($farmhouses, 'id');
        $bookings = [];
        $grossRevenue = 0.00;
        $totalCommission = 0.00;
        $netEarnings = 0.00;

        if (!empty($farmIds)) {
            $inClause = implode(',', array_map('intval', $farmIds));

            // Get approved or paid booking requests
            $stmtBookings = $this->db->query("
                SELECT b.*, f.title as farmhouse_title, u.name as customer_name
                FROM booking_requests b
                JOIN farmhouses f ON b.farmhouse_id = f.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.farmhouse_id IN ($inClause)
                  AND (b.status = 'approved' OR b.payment_status = 'paid')
                ORDER BY b.created_at DESC
            ");

            if ($stmtBookings) {
                while ($row = $stmtBookings->fetch_assoc()) {
                    $gross = (float)($row['total_price'] ?? 0);
                    // If platform_fee is set, use it; otherwise standard 10% commission
                    $comm = isset($row['platform_fee']) && (float)$row['platform_fee'] > 0
                        ? (float)$row['platform_fee']
                        : round($gross * 0.10, 2);
                    $net = max(0, $gross - $comm);

                    $row['calculated_gross'] = $gross;
                    $row['calculated_comm']  = $comm;
                    $row['calculated_net']   = $net;

                    $grossRevenue    += $gross;
                    $totalCommission += $comm;
                    $netEarnings     += $net;

                    $bookings[] = $row;
                }
            }
        }

        // Get payouts history from owner_payouts
        $stmtPayouts = $this->db->prepare("
            SELECT * FROM owner_payouts
            WHERE owner_id = ?
            ORDER BY created_at DESC
        ");
        $stmtPayouts->bind_param("i", $ownerId);
        $stmtPayouts->execute();
        $payouts = $stmtPayouts->get_result()->fetch_all(MYSQLI_ASSOC);

        $totalPaidOut    = 0.00;
        $totalProcessing = 0.00;

        foreach ($payouts as $p) {
            if ($p['payout_status'] === 'completed') {
                $totalPaidOut += (float)$p['net_payout'];
            } elseif (in_array($p['payout_status'], ['pending', 'processing'])) {
                $totalProcessing += (float)$p['net_payout'];
            }
        }

        $availableBalance = max(0, $netEarnings - ($totalPaidOut + $totalProcessing));

        $stats = [
            'gross_revenue'     => $grossRevenue,
            'total_commission'  => $totalCommission,
            'net_earnings'      => $netEarnings,
            'total_paid_out'    => $totalPaidOut,
            'total_processing'  => $totalProcessing,
            'available_balance' => $availableBalance,
            'bookings_count'    => count($bookings),
            'payouts_count'     => count($payouts),
        ];

        $pageTitle = "Owner Earnings & Payouts Ledger";
        $activePage = "earnings";
        require_once __DIR__ . '/../../Views/owner/earnings.php';
    }

    public function requestPayout(): void {
        $ownerId = $this->getOwnerId();
        if (!$ownerId) {
            redirect('owner/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('owner/earnings');
        }

        $amount = (float)($_POST['amount'] ?? 0);
        $notes  = trim($_POST['payout_details'] ?? '');

        if ($amount < 1000) {
            $_SESSION['error'] = 'Minimum payout withdrawal threshold is ₹1,000.';
            redirect('owner/earnings');
        }

        // Recalculate available balance
        $stmtFarms = $this->db->prepare("SELECT id FROM farmhouses WHERE owner_id = ?");
        $stmtFarms->bind_param("i", $ownerId);
        $stmtFarms->execute();
        $farmhouses = $stmtFarms->get_result()->fetch_all(MYSQLI_ASSOC);
        $farmIds = array_column($farmhouses, 'id');

        $netEarnings = 0.00;
        if (!empty($farmIds)) {
            $inClause = implode(',', array_map('intval', $farmIds));
            $res = $this->db->query("
                SELECT total_price, platform_fee
                FROM booking_requests
                WHERE farmhouse_id IN ($inClause)
                  AND (status = 'approved' OR payment_status = 'paid')
            ");
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $g = (float)$r['total_price'];
                    $c = isset($r['platform_fee']) && (float)$r['platform_fee'] > 0 ? (float)$r['platform_fee'] : round($g * 0.10, 2);
                    $netEarnings += max(0, $g - $c);
                }
            }
        }

        $resPayouts = $this->db->query("SELECT SUM(net_payout) as committed FROM owner_payouts WHERE owner_id = $ownerId AND payout_status IN ('completed', 'pending', 'processing')");
        $committed = (float)($resPayouts->fetch_assoc()['committed'] ?? 0);
        $available = max(0, $netEarnings - $committed);

        if ($amount > $available) {
            $_SESSION['error'] = 'Requested withdrawal amount (₹' . number_format($amount, 2) . ') exceeds your available balance (₹' . number_format($available, 2) . ').';
            redirect('owner/earnings');
        }

        $stmt = $this->db->prepare("
            INSERT INTO owner_payouts (owner_id, gross_amount, platform_commission, net_payout, payout_status, notes, created_at)
            VALUES (?, ?, 0.00, ?, 'pending', ?, NOW())
        ");
        $stmt->bind_param("idds", $ownerId, $amount, $amount, $notes);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Payout request of ₹' . number_format($amount, 2) . ' submitted successfully. Our finance desk will disburse to your registered bank account within 24-48 hours.';
        } else {
            $_SESSION['error'] = 'Failed to submit payout request: ' . htmlspecialchars($this->db->error);
        }

        redirect('owner/earnings');
    }
}
