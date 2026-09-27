<?php
namespace App\Controllers\Owner;

use App\Config\Database;
use Exception;

class OwnerKycController {
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

        // Get owner profile / info
        $stmtOwner = $this->db->prepare("SELECT id, name, email, phone, is_verified FROM users WHERE id = ?");
        $stmtOwner->bind_param("i", $ownerId);
        $stmtOwner->execute();
        $owner = $stmtOwner->get_result()->fetch_assoc();

        // Get owner's farmhouses
        $stmtFarms = $this->db->prepare("SELECT id, title, location FROM farmhouses WHERE owner_id = ? ORDER BY title ASC");
        $stmtFarms->bind_param("i", $ownerId);
        $stmtFarms->execute();
        $farmhouses = $stmtFarms->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get uploaded KYC documents
        $stmtDocs = $this->db->prepare("
            SELECT d.*, f.title as farmhouse_title
            FROM owner_documents d
            LEFT JOIN farmhouses f ON d.farmhouse_id = f.id
            WHERE d.owner_id = ?
            ORDER BY d.created_at DESC
        ");
        $stmtDocs->bind_param("i", $ownerId);
        $stmtDocs->execute();
        $documents = $stmtDocs->get_result()->fetch_all(MYSQLI_ASSOC);

        // Document count by status
        $stats = [
            'total'    => count($documents),
            'verified' => 0,
            'pending'  => 0,
            'rejected' => 0
        ];
        foreach ($documents as $doc) {
            if ($doc['status'] === 'verified') $stats['verified']++;
            elseif ($doc['status'] === 'pending') $stats['pending']++;
            elseif ($doc['status'] === 'rejected') $stats['rejected']++;
        }

        $pageTitle = "KYC & Ownership Verification";
        $activePage = "kyc";
        require_once __DIR__ . '/../../Views/owner/kyc.php';
    }

    public function upload(): void {
        $ownerId = $this->getOwnerId();
        if (!$ownerId) {
            redirect('owner/login');
        }

        $documentType   = trim($_POST['document_type'] ?? '');
        $documentNumber = trim($_POST['document_number'] ?? '');
        $farmhouseId    = !empty($_POST['farmhouse_id']) ? (int)$_POST['farmhouse_id'] : null;

        $validTypes = ['aadhaar', 'pan', 'electricity_bill', 'property_registry', 'fssai_license', 'other'];
        if (!in_array($documentType, $validTypes)) {
            $_SESSION['error'] = 'Invalid document type selected.';
            redirect('owner/kyc');
        }

        if (empty($_FILES['document_file']['name']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Please select a valid document file (PDF, JPG, PNG).';
            redirect('owner/kyc');
        }

        $file     = $_FILES['document_file'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];

        if (!in_array($ext, $allowed)) {
            $_SESSION['error'] = 'Allowed file formats: JPG, PNG, PDF, WEBP.';
            redirect('owner/kyc');
        }

        if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
            $_SESSION['error'] = 'File size exceeds maximum allowable limit of 10MB.';
            redirect('owner/kyc');
        }

        $uploadDir = __DIR__ . '/../../../assets/images/uploads/kyc/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = 'kyc_' . $ownerId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            $relativePath = 'assets/images/uploads/kyc/' . $fileName;

            $stmt = $this->db->prepare("
                INSERT INTO owner_documents (owner_id, farmhouse_id, document_type, document_number, file_path, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->bind_param("iisss", $ownerId, $farmhouseId, $documentType, $documentNumber, $relativePath);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Document submitted successfully! Our compliance team will review and verify it shortly.';
            } else {
                $_SESSION['error'] = 'Failed to record document details in database.';
            }
        } else {
            $_SESSION['error'] = 'Failed to upload document file. Please check server folder permissions.';
        }

        redirect('owner/kyc');
    }

    public function delete(): void {
        $ownerId = $this->getOwnerId();
        if (!$ownerId) {
            redirect('owner/login');
        }

        $docId = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($docId <= 0) {
            redirect('owner/kyc');
        }

        // Verify document belongs to owner and is not yet verified
        $stmt = $this->db->prepare("SELECT id, file_path, status FROM owner_documents WHERE id = ? AND owner_id = ?");
        $stmt->bind_param("ii", $docId, $ownerId);
        $stmt->execute();
        $doc = $stmt->get_result()->fetch_assoc();

        if ($doc && $doc['status'] !== 'verified') {
            $fullPath = __DIR__ . '/../../../' . $doc['file_path'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            $delStmt = $this->db->prepare("DELETE FROM owner_documents WHERE id = ? AND owner_id = ?");
            $delStmt->bind_param("ii", $docId, $ownerId);
            $delStmt->execute();
            $_SESSION['success'] = 'Document deleted successfully.';
        } else {
            $_SESSION['error'] = 'Cannot delete a verified document or document does not exist.';
        }

        redirect('owner/kyc');
    }
}
