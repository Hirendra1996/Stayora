<?php
namespace App\Models\Admin;
use mysqli;

class AvailabilityModel {
    private $db;

    public function __construct(mysqli $db) { $this->db = $db; }

    public function getAllFarms() {
        $res = $this->db->query("SELECT id, title FROM farmhouses ORDER BY title ASC");
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function getFarmById($id) {
        $stmt = $this->db->prepare("SELECT * FROM farmhouses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getInquiriesByFarm($id) {
        $stmt = $this->db->prepare("SELECT * FROM inquiries WHERE farmhouse_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE farmhouses SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
}