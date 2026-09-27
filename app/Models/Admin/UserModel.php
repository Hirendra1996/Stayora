<?php
namespace App\Models\Admin;

use mysqli;

class UserModel
{
    private $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    // ============================================================
    //  READ
    // ============================================================

    public function getAllUsers(): array
    {
        $sql = "SELECT
                    u.id,
                    u.name,
                    u.email,
                    u.phone,
                    u.gender,
                    u.date_of_birth,
                    u.status,
                    u.created_at,
                    u.last_login,
                    u.profile_image,
                    u.token_expiry,
                    u.is_logged_in,
                    CASE
                        WHEN u.is_logged_in = 1 
                        AND (
                            (u.remember_token IS NOT NULL AND u.token_expiry IS NOT NULL AND u.token_expiry > NOW())
                             OR (u.last_login IS NOT NULL AND u.last_login >= NOW() - INTERVAL 30 MINUTE)
                        )
                        THEN 1
                        ELSE 0
                    END AS is_online,
                    (SELECT COUNT(*) FROM booking_requests br WHERE br.user_id = u.id) AS booking_count,
                    (SELECT COUNT(*) FROM wishlist w WHERE w.user_id = u.id) AS wishlist_count,
                    (SELECT COUNT(*) FROM inquiries i WHERE i.user_id = u.id) AS inquiry_count
                FROM users u
                ORDER BY u.created_at DESC";

        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getUserById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT
                u.*,
                CASE
                    WHEN u.is_logged_in = 1 
                    AND (
                        (u.remember_token IS NOT NULL AND u.token_expiry IS NOT NULL AND u.token_expiry > NOW())
                         OR (u.last_login IS NOT NULL AND u.last_login >= NOW() - INTERVAL 30 MINUTE)
                    )
                    THEN 1
                    ELSE 0
                END AS is_online,
                (SELECT COUNT(*) FROM booking_requests br WHERE br.user_id = u.id) AS booking_count,
                (SELECT COUNT(*) FROM wishlist w WHERE w.user_id = u.id) AS wishlist_count,
                (SELECT COUNT(*) FROM inquiries i WHERE i.user_id = u.id) AS inquiry_count
             FROM users u
             WHERE u.id = ?
             LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Get Comprehensive User Dossier (User + Bookings + Wishlist + Inquiries)
     */
    public function getUserDetails(int $id): ?array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return null;
        }

        // Clean sensitive data
        unset($user['password'], $user['remember_token'], $user['reset_token']);

        // Fetch User's Recent Booking Requests with Farmhouse Details
        $bookingsStmt = $this->db->prepare(
            "SELECT 
                br.id,
                br.farmhouse_id,
                br.check_in,
                br.check_out,
                br.start_date,
                br.end_date,
                br.guests,
                br.price,
                br.status,
                br.created_at,
                br.message,
                f.title AS farmhouse_title,
                f.location AS farmhouse_location,
                (SELECT img.image_url FROM images img WHERE img.farmhouse_id = f.id ORDER BY img.id ASC LIMIT 1) AS thumb_url
             FROM booking_requests br
             LEFT JOIN farmhouses f ON br.farmhouse_id = f.id
             WHERE br.user_id = ?
             ORDER BY br.created_at DESC"
        );
        $bookingsStmt->bind_param("i", $id);
        $bookingsStmt->execute();
        $bookings = $bookingsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Fetch User's Wishlist Items
        $wishlistStmt = $this->db->prepare(
            "SELECT 
                w.id,
                w.farmhouse_id,
                f.title AS farmhouse_title,
                f.location AS farmhouse_location,
                f.price AS farmhouse_price,
                f.status AS farmhouse_status,
                (SELECT img.image_url FROM images img WHERE img.farmhouse_id = f.id ORDER BY img.id ASC LIMIT 1) AS thumb_url
             FROM wishlist w
             INNER JOIN farmhouses f ON w.farmhouse_id = f.id
             WHERE w.user_id = ?
             ORDER BY w.id DESC"
        );
        $wishlistStmt->bind_param("i", $id);
        $wishlistStmt->execute();
        $wishlist = $wishlistStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Fetch Inquiries submitted by User
        $inquiriesStmt = $this->db->prepare(
            "SELECT 
                i.id,
                i.farmhouse_id,
                i.type,
                i.name,
                i.phone,
                i.message,
                i.status,
                i.created_at,
                f.title AS farmhouse_title
             FROM inquiries i
             LEFT JOIN farmhouses f ON i.farmhouse_id = f.id
             WHERE i.user_id = ?
             ORDER BY i.created_at DESC"
        );
        $inquiriesStmt->bind_param("i", $id);
        $inquiriesStmt->execute();
        $inquiries = $inquiriesStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'user'      => $user,
            'bookings'  => $bookings,
            'wishlist'  => $wishlist,
            'inquiries' => $inquiries,
        ];
    }

    // ============================================================
    //  STATS
    // ============================================================

    public function getUserStats(): array
    {
        $stats = [
            'total'   => 0,
            'active'  => 0,
            'blocked' => 0,
            'online'  => 0,
        ];

        $result = $this->db->query(
            "SELECT status, COUNT(*) AS cnt FROM users GROUP BY status"
        );
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $key = $row['status'];
                if (isset($stats[$key])) {
                    $stats[$key] = (int) $row['cnt'];
                }
                $stats['total'] += (int) $row['cnt'];
            }
        }

        $onlineResult = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM users
             WHERE is_logged_in = 1 
               AND (
                 (remember_token IS NOT NULL AND token_expiry IS NOT NULL AND token_expiry > NOW())
                  OR (last_login IS NOT NULL AND last_login >= NOW() - INTERVAL 30 MINUTE)
               )"
        );
        if ($onlineResult) {
            $stats['online'] = (int) $onlineResult->fetch_assoc()['cnt'];
        }

        return $stats;
    }

    // ============================================================
    //  CREATE, UPDATE, STATUS 
    // ============================================================

    public function createUser(array $data): bool
    {
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $status         = in_array($data['status'] ?? '', ['active', 'blocked']) ? $data['status'] : 'active';
        $gender         = !empty($data['gender']) ? $data['gender'] : null;
        $dob            = !empty($data['date_of_birth']) ? $data['date_of_birth'] : null;

        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, phone, gender, date_of_birth, password, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssss", $data['name'], $data['email'], $data['phone'], $gender, $dob, $hashedPassword, $status);
        return $stmt->execute();
    }

    public function updateUser(int $id, array $data): bool
    {
        $gender = !empty($data['gender']) ? $data['gender'] : null;
        $dob    = !empty($data['date_of_birth']) ? $data['date_of_birth'] : null;
        $status = in_array($data['status'] ?? '', ['active', 'blocked']) ? $data['status'] : 'active';

        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt = $this->db->prepare(
                "UPDATE users SET name=?, email=?, phone=?, gender=?, date_of_birth=?, status=?, password=? WHERE id=?"
            );
            $stmt->bind_param("sssssssi", $data['name'], $data['email'], $data['phone'], $gender, $dob, $status, $hashedPassword, $id);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE users SET name=?, email=?, phone=?, gender=?, date_of_birth=?, status=? WHERE id=?"
            );
            $stmt->bind_param("ssssssi", $data['name'], $data['email'], $data['phone'], $gender, $dob, $status, $id);
        }
        return $stmt->execute();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $validStatus = ($status === 'active') ? 'active' : 'blocked';
        $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $validStatus, $id);
        return $stmt->execute();
    }

    public function bulkUpdateStatus(array $ids, string $status): int
    {
        if (empty($ids)) return 0;
        $validStatus = ($status === 'active') ? 'active' : 'blocked';
        $intIds = array_map('intval', $ids);
        $inList = implode(',', $intIds);
        $sql = "UPDATE users SET status = '$validStatus' WHERE id IN ($inList)";
        $this->db->query($sql);
        
        if ($validStatus === 'blocked') {
            $this->db->query("UPDATE users SET remember_token = NULL, token_expiry = NULL, is_logged_in = 0 WHERE id IN ($inList)");
        }
        return $this->db->affected_rows;
    }

    public function bulkDelete(array $ids): int
    {
        if (empty($ids)) return 0;
        $intIds = array_map('intval', $ids);
        $inList = implode(',', $intIds);
        $this->db->query("DELETE FROM users WHERE id IN ($inList)");
        return $this->db->affected_rows;
    }

    /**
     * Force Logout kills valid token and sets logged_in completely false
     */
    public function forceLogout(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users
             SET remember_token = NULL,
                 token_expiry = NULL,
                 is_logged_in = 0
             WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // ============================================================
    //  DELETE
    // ============================================================

    public function deleteUser(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // ============================================================
    //  ENCRYPTION HELPERS
    // ============================================================

    public static function encryptId(int $id): string {
        return \App\Helpers\CryptoHelper::encrypt((string) $id);
    }
    public static function decryptId(string $encrypted): int {
        return (int) \App\Helpers\CryptoHelper::decrypt($encrypted);
    }
}