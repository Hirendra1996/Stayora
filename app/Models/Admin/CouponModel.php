<?php
namespace App\Models\Admin;

use mysqli;

class CouponModel {
    private mysqli $db;

    public function __construct(?mysqli $db = null) {
        $this->db = $db ?? \App\Config\Database::connect();
    }

    public function getAllCoupons(): array {
        $sql = "SELECT * FROM coupons ORDER BY id DESC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getCouponById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM coupons WHERE id = ? LIMIT 1");
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function saveCoupon(array $data, ?int $id = null): bool {
        $code = strtoupper(trim($data['code'] ?? ''));
        $description = trim($data['description'] ?? '');
        $discountType = in_array($data['discount_type'] ?? '', ['percentage', 'flat']) ? $data['discount_type'] : 'percentage';
        $discountValue = (float)($data['discount_value'] ?? 0.0);
        $minBooking = (float)($data['min_booking_amount'] ?? 0.0);
        $maxDiscount = !empty($data['max_discount_amount']) ? (float)$data['max_discount_amount'] : null;
        $validFrom = !empty($data['valid_from']) ? $data['valid_from'] : null;
        $validUntil = !empty($data['valid_until']) ? $data['valid_until'] : null;
        $status = in_array($data['status'] ?? '', ['active', 'inactive']) ? $data['status'] : 'active';
        $usageLimit = max(1, (int)($data['usage_limit'] ?? 1000));

        if ($id && $id > 0) {
            $stmt = $this->db->prepare(
                "UPDATE coupons 
                 SET code = ?, description = ?, discount_type = ?, discount_value = ?, 
                     min_booking_amount = ?, max_discount_amount = ?, valid_from = ?, 
                     valid_until = ?, usage_limit = ?, status = ?
                 WHERE id = ?"
            );
            if (!$stmt) return false;
            $stmt->bind_param(
                "sssdddssisi",
                $code, $description, $discountType, $discountValue,
                $minBooking, $maxDiscount, $validFrom, $validUntil,
                $usageLimit, $status, $id
            );
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO coupons 
                 (code, description, discount_type, discount_value, min_booking_amount, 
                  max_discount_amount, valid_from, valid_until, usage_limit, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) return false;
            $stmt->bind_param(
                "sssdddssis",
                $code, $description, $discountType, $discountValue,
                $minBooking, $maxDiscount, $validFrom, $validUntil,
                $usageLimit, $status
            );
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        }
    }

    public function toggleStatus(int $id): bool {
        $stmt = $this->db->prepare("UPDATE coupons SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function deleteCoupon(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM coupons WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
