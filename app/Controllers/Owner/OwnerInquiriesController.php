<?php
namespace App\Controllers\Owner;

use App\Config\Database;
use Exception;

class OwnerInquiriesController {
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

        $statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
        $farmFilter   = !empty($_GET['farmhouse_id']) ? (int)$_GET['farmhouse_id'] : 0;
        $search       = trim($_GET['q'] ?? '');

        // Fetch owner properties for filter dropdown
        $stmtFarms = $this->db->prepare("SELECT id, title, location FROM farmhouses WHERE owner_id = ? ORDER BY title ASC");
        $stmtFarms->bind_param("i", $ownerId);
        $stmtFarms->execute();
        $farmhouses = $stmtFarms->get_result()->fetch_all(MYSQLI_ASSOC);

        // Build query for inquiries belonging to owner's farmhouses
        $sql = "
            SELECT i.*, f.title as farmhouse_title, f.location as farmhouse_location
            FROM inquiries i
            JOIN farmhouses f ON i.farmhouse_id = f.id
            WHERE f.owner_id = ?
        ";
        $params = [$ownerId];
        $types  = "i";

        if ($farmFilter > 0) {
            $sql .= " AND i.farmhouse_id = ?";
            $params[] = $farmFilter;
            $types .= "i";
        }

        if ($statusFilter !== 'all' && in_array($statusFilter, ['new', 'contacted', 'converted', 'closed'])) {
            $sql .= " AND i.status = ?";
            $params[] = $statusFilter;
            $types .= "s";
        }

        if (!empty($search)) {
            $sql .= " AND (i.name LIKE ? OR i.phone LIKE ? OR i.message LIKE ?)";
            $wildcard = "%{$search}%";
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
            $types .= "sss";
        }

        $sql .= " ORDER BY i.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $inquiries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calculate stats
        $stmtStats = $this->db->prepare("
            SELECT i.status, COUNT(*) as cnt
            FROM inquiries i
            JOIN farmhouses f ON i.farmhouse_id = f.id
            WHERE f.owner_id = ?
            GROUP BY i.status
        ");
        $stmtStats->bind_param("i", $ownerId);
        $stmtStats->execute();
        $resStats = $stmtStats->get_result();

        $stats = ['all' => 0, 'new' => 0, 'contacted' => 0, 'converted' => 0, 'closed' => 0];
        while ($row = $resStats->fetch_assoc()) {
            $s = strtolower($row['status']);
            if (isset($stats[$s])) {
                $stats[$s] = (int)$row['cnt'];
            }
            $stats['all'] += (int)$row['cnt'];
        }

        $pageTitle = "Property Inquiries & Customer Leads";
        $activePage = "inquiries";
        require_once __DIR__ . '/../../Views/owner/inquiries.php';
    }

    public function updateStatus(): void {
        $ownerId = $this->getOwnerId();
        if (!$ownerId) {
            redirect('owner/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('owner/inquiries');
        }

        $inquiryId = (int)($_POST['inquiry_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');
        $notes     = isset($_POST['notes']) ? trim($_POST['notes']) : null;
        $followUp  = !empty($_POST['follow_up_date']) ? trim($_POST['follow_up_date']) : null;

        if (!in_array($newStatus, ['new', 'contacted', 'converted', 'closed'])) {
            $_SESSION['error'] = 'Invalid status selected.';
            redirect('owner/inquiries');
        }

        // Verify inquiry belongs to owner's property
        $stmtCheck = $this->db->prepare("
            SELECT i.id FROM inquiries i
            JOIN farmhouses f ON i.farmhouse_id = f.id
            WHERE i.id = ? AND f.owner_id = ?
        ");
        $stmtCheck->bind_param("ii", $inquiryId, $ownerId);
        $stmtCheck->execute();
        if (!$stmtCheck->get_result()->fetch_assoc()) {
            $_SESSION['error'] = 'Unauthorized inquiry record.';
            redirect('owner/inquiries');
        }

        $stmt = $this->db->prepare("
            UPDATE inquiries 
            SET status = ?, notes = COALESCE(?, notes), follow_up_date = COALESCE(?, follow_up_date) 
            WHERE id = ?
        ");
        $stmt->bind_param("sssi", $newStatus, $notes, $followUp, $inquiryId);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Inquiry updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update inquiry status.';
        }

        redirect('owner/inquiries');
    }
}
