<?php
namespace App\Controllers\Owner;

use App\Config\Database;
use Exception;

class OwnerOfflineBookingController {
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

        // Filters
        $farmFilter   = !empty($_GET['farmhouse_id']) ? (int)$_GET['farmhouse_id'] : 0;
        $statusFilter = trim($_GET['status'] ?? 'all');
        $search       = trim($_GET['q'] ?? '');

        // Query owner farmhouses
        $stmtFarms = $this->db->prepare("SELECT id, title, location FROM farmhouses WHERE owner_id = ? ORDER BY title ASC");
        $stmtFarms->bind_param("i", $ownerId);
        $stmtFarms->execute();
        $farmhouses = $stmtFarms->get_result()->fetch_all(MYSQLI_ASSOC);

        // Build query for offline bookings
        $sql = "
            SELECT ob.*, f.title as farmhouse_title, f.location as farmhouse_location,
                   rt.room_name as room_type_name
            FROM owner_offline_bookings ob
            JOIN farmhouses f ON ob.farmhouse_id = f.id
            LEFT JOIN room_types rt ON ob.room_type_id = rt.id
            WHERE ob.owner_id = ?
        ";
        $params = [$ownerId];
        $types  = "i";

        if ($farmFilter > 0) {
            $sql .= " AND ob.farmhouse_id = ?";
            $params[] = $farmFilter;
            $types .= "i";
        }
        if ($statusFilter !== 'all' && in_array($statusFilter, ['confirmed', 'checked_in', 'checked_out', 'cancelled'])) {
            $sql .= " AND ob.status = ?";
            $params[] = $statusFilter;
            $types .= "s";
        }
        if (!empty($search)) {
            $sql .= " AND (ob.guest_name LIKE ? OR ob.guest_phone LIKE ? OR ob.guest_email LIKE ?)";
            $wildcard = "%{$search}%";
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
            $types .= "sss";
        }

        $sql .= " ORDER BY ob.check_in DESC";

        $stmt = $this->db->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Stats calculation
        $stats = [
            'total'          => count($bookings),
            'active'         => 0,
            'total_revenue'  => 0,
            'advance_total'  => 0,
        ];
        foreach ($bookings as $b) {
            if ($b['status'] !== 'cancelled') {
                $stats['total_revenue'] += (float)$b['total_amount'];
                $stats['advance_total'] += (float)$b['advance_collected'];
                if (in_array($b['status'], ['confirmed', 'checked_in'])) {
                    $stats['active']++;
                }
            }
        }

        // Room types for modal dropdown
        $roomTypes = [];
        if (!empty($farmhouses)) {
            $farmIds = array_column($farmhouses, 'id');
            $inClause = implode(',', array_map('intval', $farmIds));
            $resRooms = $this->db->query("SELECT id, farmhouse_id, room_name, base_price FROM room_types WHERE farmhouse_id IN ($inClause)");
            if ($resRooms) {
                while ($r = $resRooms->fetch_assoc()) {
                    $roomTypes[$r['farmhouse_id']][] = $r;
                }
            }
        }

        $pageTitle = "Offline Bookings & Guest Dossiers";
        $activePage = "offline_bookings";
        require_once __DIR__ . '/../../Views/owner/offline_bookings.php';
    }

    public function create(): void {
        $ownerId = $this->getOwnerId();
        if (!$ownerId) {
            redirect('owner/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('owner/offline-bookings');
        }

        $farmhouseId      = (int)($_POST['farmhouse_id'] ?? 0);
        $guestName        = trim($_POST['guest_name'] ?? '');
        $guestPhone       = trim($_POST['guest_phone'] ?? '');
        $guestEmail       = trim($_POST['guest_email'] ?? '');
        $bookingType      = in_array($_POST['booking_type'] ?? '', ['entire', 'room']) ? $_POST['booking_type'] : 'entire';
        $roomTypeId       = !empty($_POST['room_type_id']) ? (int)$_POST['room_type_id'] : null;
        $checkIn          = trim($_POST['check_in'] ?? '');
        $checkOut         = trim($_POST['check_out'] ?? '');
        $guests           = max(1, (int)($_POST['guests'] ?? 1));
        $rooms            = max(1, (int)($_POST['rooms'] ?? 1));
        $totalAmount      = max(0, (float)($_POST['total_amount'] ?? 0));
        $advanceCollected = max(0, (float)($_POST['advance_collected'] ?? 0));
        $paymentMode      = trim($_POST['payment_mode'] ?? 'cash');
        $notes            = trim($_POST['notes'] ?? '');
        $autoLockDates    = !empty($_POST['auto_lock_dates']);

        if (!$farmhouseId || empty($guestName) || empty($guestPhone) || empty($checkIn) || empty($checkOut)) {
            $_SESSION['error'] = 'Please fill in all required fields (Farmhouse, Guest Name, Phone, Check-in, Check-out).';
            redirect('owner/offline-bookings');
        }

        // Ownership check
        $stmtCheck = $this->db->prepare("SELECT id FROM farmhouses WHERE id = ? AND owner_id = ?");
        $stmtCheck->bind_param("ii", $farmhouseId, $ownerId);
        $stmtCheck->execute();
        if (!$stmtCheck->get_result()->fetch_assoc()) {
            $_SESSION['error'] = 'Unauthorized farmhouse selection.';
            redirect('owner/offline-bookings');
        }

        // Insert offline booking
        $stmt = $this->db->prepare("
            INSERT INTO owner_offline_bookings (
                owner_id, farmhouse_id, guest_name, guest_phone, guest_email,
                booking_type, room_type_id, check_in, check_out, guests,
                rooms, total_amount, advance_collected, payment_mode, status, notes, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', ?, NOW())
        ");
        $stmt->bind_param(
            "iissssissiidsss",
            $ownerId, $farmhouseId, $guestName, $guestPhone, $guestEmail,
            $bookingType, $roomTypeId, $checkIn, $checkOut, $guests,
            $rooms, $totalAmount, $advanceCollected, $paymentMode, $notes
        );

        if ($stmt->execute()) {
            $offlineId = $stmt->insert_id;

            // Auto-lock dates if requested so website searchers don't double book
            if ($autoLockDates) {
                $reason = "Offline Stay: " . $guestName . " (#OFF-" . $offlineId . ")";
                $stmtLock = $this->db->prepare("
                    INSERT INTO blocked_dates (farmhouse_id, start_date, end_date, reason, locked_by, owner_id)
                    VALUES (?, ?, ?, ?, 'owner', ?)
                ");
                $stmtLock->bind_param("isssi", $farmhouseId, $checkIn, $checkOut, $reason, $ownerId);
                $stmtLock->execute();
            }

            $_SESSION['success'] = "Offline booking for {$guestName} registered successfully" . ($autoLockDates ? " and calendar dates locked." : ".");
        } else {
            $_SESSION['error'] = "Failed to record offline booking: " . htmlspecialchars($this->db->error);
        }

        redirect('owner/offline-bookings');
    }

    public function cancel(): void {
        $ownerId = $this->getOwnerId();
        if (!$ownerId) {
            redirect('owner/login');
        }

        $bookingId = (int)($_POST['id'] ?? 0);
        if (!$bookingId) {
            redirect('owner/offline-bookings');
        }

        // Verify booking belongs to owner
        $stmt = $this->db->prepare("SELECT * FROM owner_offline_bookings WHERE id = ? AND owner_id = ?");
        $stmt->bind_param("ii", $bookingId, $ownerId);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();

        if ($booking) {
            $stmtUpdate = $this->db->prepare("UPDATE owner_offline_bookings SET status = 'cancelled' WHERE id = ?");
            $stmtUpdate->bind_param("i", $bookingId);
            $stmtUpdate->execute();

            // Unlock any corresponding date locks
            $reasonPattern = "%#OFF-" . $bookingId . "%";
            $stmtUnlock = $this->db->prepare("DELETE FROM blocked_dates WHERE farmhouse_id = ? AND owner_id = ? AND reason LIKE ?");
            $stmtUnlock->bind_param("iis", $booking['farmhouse_id'], $ownerId, $reasonPattern);
            $stmtUnlock->execute();

            $_SESSION['success'] = "Offline booking cancelled and calendar dates unlocked.";
        } else {
            $_SESSION['error'] = "Booking record not found.";
        }

        redirect('owner/offline-bookings');
    }
}
