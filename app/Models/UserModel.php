<?php
namespace App\Models;

use App\Config\Database;

class UserModel {

    protected $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    private function validateTable($table) {
        $allowed = ['users', 'admins', 'owners'];
        if (!in_array($table, $allowed)) {
            die("Invalid table access");
        }
        return $table;
    }

    // ── NEW: METHOD FOR MIDDLEWARE TO CHECK is_logged_in VALUE ──────
    public function getLoginStatus($userId, $table) {
        $table = $this->validateTable($table);
        $query = "SELECT is_logged_in, status FROM $table WHERE id = ? LIMIT 1";
    
        $stmt = $this->db->prepare($query);
        if (!$stmt) die("SQL Error: " . $this->db->error);
    
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    
        $result = $stmt->get_result()->fetch_assoc();
    
        // Return 0 if not found, not logged in, OR blocked
        if (!$result) return 0;
        if (strtolower($result['status'] ?? '') !== 'active') return 0;   // ← blocks mid-session too
        return (int)$result['is_logged_in'];
    }
    // ────────────────────────────────────────────────────────────────

    public function findUsersByCredentials($login, $table): array {
        $table = $this->validateTable($table);
        $allowPhoneLogin = in_array($table, ['users', 'owners']);

        $cleanLogin = trim($login);
        $digitsOnly = preg_replace('/[^0-9]/', '', $cleanLogin);
        $isPhone = false;
        $cleanPhone = $cleanLogin;

        if ($allowPhoneLogin && !empty($digitsOnly)) {
            if (strlen($digitsOnly) === 10) {
                $isPhone = true;
                $cleanPhone = $digitsOnly;
            } elseif (strlen($digitsOnly) === 12 && str_starts_with($digitsOnly, '91')) {
                // e.g. +91 9876543210
                $isPhone = true;
                $cleanPhone = substr($digitsOnly, 2);
            } elseif (strlen($digitsOnly) === 11 && str_starts_with($digitsOnly, '0')) {
                // e.g. 09876543210
                $isPhone = true;
                $cleanPhone = substr($digitsOnly, 1);
            }
        }

        if ($isPhone) {
            $query = "SELECT * FROM $table WHERE phone = ? ORDER BY id DESC";
            $stmt = $this->db->prepare($query);
            if (!$stmt) die("SQL Error: " . $this->db->error);
            $stmt->bind_param("s", $cleanPhone);
        } else {
            $query = "SELECT * FROM $table WHERE email = ? ORDER BY id DESC";
            $stmt = $this->db->prepare($query);
            if (!$stmt) die("SQL Error: " . $this->db->error);
            $stmt->bind_param("s", $cleanLogin);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findUserByCredentials($login, $table) {
        $users = $this->findUsersByCredentials($login, $table);
        return !empty($users) ? $users[0] : null;
    }

    public function updateRememberToken($userId, $table, $token, $expiry) {
        $table = $this->validateTable($table);

        $query = "UPDATE $table 
                  SET remember_token = ?, token_expiry = ? 
                  WHERE id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("SQL Error: " . $this->db->error);
        }

        $stmt->bind_param("ssi", $token, $expiry, $userId);
        return $stmt->execute();
    }

    public function findByToken($token, $table) {
        $table = $this->validateTable($table);

        $query = "SELECT * FROM $table 
                  WHERE remember_token = ? 
                  AND token_expiry > NOW() 
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("SQL Error: " . $this->db->error);
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function updateLastLogin($userId, $table) {
        $table = $this->validateTable($table);

        $query = "UPDATE $table SET last_login = NOW() WHERE id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("SQL Error: " . $this->db->error);
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }

    public function markLoggedIn($userId, $table)
    {
        $table = $this->validateTable($table);
    
        $query = "UPDATE $table
                  SET last_login = NOW(),
                      is_logged_in = 1
                  WHERE id = ?";
    
        $stmt = $this->db->prepare($query);
    
        if (!$stmt) {
            die("SQL Error: " . $this->db->error);
        }
    
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    public function updateSessionToken(int $userId, string $table): void
    {
        $table  = $this->validateTable($table);
        $token  = bin2hex(random_bytes(16));
        $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));
 
        $stmt = $this->db->prepare(
            "UPDATE $table
             SET remember_token = ?, token_expiry = ?
             WHERE id = ?
               AND (remember_token IS NULL OR token_expiry <= NOW())"
        );
        $stmt->bind_param("ssi", $token, $expiry, $userId);
        $stmt->execute();
    }
 
    public function refreshSessionToken(int $userId): void
    {
        $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));
 
        $stmt = $this->db->prepare(
            "UPDATE users
             SET token_expiry = ?
             WHERE id = ?
               AND remember_token IS NOT NULL
               AND token_expiry IS NOT NULL
               AND token_expiry < NOW() + INTERVAL 2 HOUR"
        );
        $stmt->bind_param("si", $expiry, $userId);
        $stmt->execute();
    }
    
    public function createUser($data, $table) {
        $table = $this->validateTable($table);
    
        $fields = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        
        $query = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("SQL Error: " . $this->db->error);
        }
    
        $types = str_repeat("s", count($data)); 
        $values = array_values($data);
        $stmt->bind_param($types, ...$values);
    
        return $stmt->execute();
    }
    
    public function emailExists($email, $table) {
        $table = $this->validateTable($table);
        $query = "SELECT id FROM $table WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    
    public function logoutUser($userId, $table)
    {
        $table = $this->validateTable($table);

        $query = "UPDATE $table
                  SET remember_token = NULL,
                      token_expiry = NULL,
                      is_logged_in = 0
                  WHERE id = ?";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            die("SQL Error: " . $this->db->error);
        }

        $stmt->bind_param("i", $userId);

        return $stmt->execute();
    }
    public function phoneExists($phone, $table) {
        $table = $this->validateTable($table);
        $query = "SELECT id FROM $table WHERE phone = ? LIMIT 1";
        $stmt  = $this->db->prepare($query);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // ── ADMIN SINGLE DEVICE ACTIVE SESSION METHODS ─────────────────
    public function updateAdminActiveSession(int $adminId, string $sessionToken): bool {
        $stmt = $this->db->prepare("UPDATE admins SET active_session_id = ? WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("si", $sessionToken, $adminId);
        return $stmt->execute();
    }

    public function clearAdminActiveSession(int $adminId): bool {
        $stmt = $this->db->prepare("UPDATE admins SET active_session_id = NULL WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $adminId);
        return $stmt->execute();
    }

    public function validateAdminActiveSession(int $adminId, string $currentSessionToken): bool {
        if ($adminId <= 0 || empty($currentSessionToken)) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT active_session_id, is_logged_in, status FROM admins WHERE id = ? LIMIT 1");
        if (!$stmt) return false;
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) return false;
        if (strtolower((string)($row['status'] ?? '')) !== 'active') return false;
        if ((int)($row['is_logged_in'] ?? 0) !== 1) return false;

        $activeId = (string)($row['active_session_id'] ?? '');
        if ($activeId === '') return false;

        return hash_equals($activeId, $currentSessionToken);
    }

    public function findOrCreateGoogleUser(string $googleId, string $email, string $name, ?string $avatarUrl = null): ?array {
        // 1. Check if user with this google_id already exists
        $stmt = $this->db->prepare("SELECT * FROM users WHERE google_id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $googleId);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($user) {
                $this->db->query("UPDATE users SET last_login = NOW(), is_logged_in = 1, is_email_verified = 1 WHERE id = " . (int)$user['id']);
                return $user;
            }
        }

        // 2. Check if user with this email already exists
        if (!empty($email)) {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($user) {
                    $upStmt = $this->db->prepare("UPDATE users SET google_id = ?, oauth_provider = 'google', is_email_verified = 1, last_login = NOW(), is_logged_in = 1 WHERE id = ?");
                    if ($upStmt) {
                        $upStmt->bind_param("si", $googleId, $user['id']);
                        $upStmt->execute();
                        $upStmt->close();
                    }
                    return $user;
                }
            }
        }

        // 3. New User Registration via Google OAuth
        $dummyPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $phone = '';
        $avatar = $avatarUrl ?: 'default_profile.png';
        
        $insert = $this->db->prepare("INSERT INTO users (name, email, google_id, oauth_provider, password, profile_image, avatar_url, phone, status, is_logged_in, is_email_verified, is_phone_verified, created_at, last_login) VALUES (?, ?, ?, 'google', ?, ?, ?, ?, 'active', 1, 1, 0, NOW(), NOW())");
        if (!$insert) {
            return null;
        }
        $insert->bind_param("sssssss", $name, $email, $googleId, $dummyPassword, $avatar, $avatarUrl, $phone);
        $ok = $insert->execute();
        $newId = $this->db->insert_id;
        $insert->close();

        if ($ok && $newId > 0) {
            $fetch = $this->db->query("SELECT * FROM users WHERE id = $newId LIMIT 1");
            return $fetch ? $fetch->fetch_assoc() : null;
        }

        return null;
    }
}