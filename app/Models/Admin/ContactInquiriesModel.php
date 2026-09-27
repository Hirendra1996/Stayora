<?php
namespace App\Models\Admin;

use mysqli;
use App\Helpers\CryptoHelper;

class ContactInquiriesModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    // ============================================
    // FETCH ALL INQUIRIES (with optional filter)
    // ============================================

    public function getAllInquiries(): array
    {
        $sql = "SELECT id, full_name, phone, email, Message, created_at FROM contact_inquiries ORDER BY created_at DESC";
        $result = $this->db->query($sql);

        if (!$result) {
            error_log("ContactInquiriesModel::getAllInquiries() query failed: " . $this->db->error);
            return [];
        }

        $inquiries = [];
        while ($row = $result->fetch_assoc()) {
            $row['encrypted_id'] = CryptoHelper::encrypt((string)$row['id']);
            $inquiries[] = $row;
        }

        $result->free();
        return $inquiries;
    }

    // ============================================
    // FETCH SINGLE INQUIRY BY ENCRYPTED ID
    // ============================================

    public function getInquiryByEncryptedId(string $encryptedId): ?array
    {
        $id = (int) CryptoHelper::decrypt($encryptedId);

        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT id, full_name, phone, email, Message, created_at FROM contact_inquiries WHERE id = ?"
        );

        if (!$stmt) {
            error_log("ContactInquiriesModel::getInquiryByEncryptedId() prepare failed: " . $this->db->error);
            return null;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            $row['encrypted_id'] = CryptoHelper::encrypt((string)$row['id']);
        }

        return $row ?: null;
    }

    // ============================================
    // DELETE INQUIRY BY ENCRYPTED ID
    // ============================================

    public function deleteInquiry(string $encryptedId): bool
    {
        $id = (int) CryptoHelper::decrypt($encryptedId);

        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM contact_inquiries WHERE id = ?");

        if (!$stmt) {
            error_log("ContactInquiriesModel::deleteInquiry() prepare failed: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    // ============================================
    // STATS — total count
    // ============================================

    public function getInquiryStats(): array
    {
        $stats = ['total' => 0];

        $result = $this->db->query("SELECT COUNT(*) AS total FROM contact_inquiries");

        if ($result) {
            $row           = $result->fetch_assoc();
            $stats['total'] = (int) $row['total'];
            $result->free();
        }

        return $stats;
    }
}
?>
