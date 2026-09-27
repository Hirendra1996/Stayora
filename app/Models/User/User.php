<?php
namespace App\Models\User;

class User {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Create a new user (Registration)
     */
    public function register($data) {
        // 1. Hash the password for security
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", 
            $data['name'], 
            $data['email'], 
            $data['phone'], 
            $hashedPassword
        );

        return $stmt->execute();
    }

    /**
     * Authenticate a user (Login)
     */
    public function login($email, $password) {
    $sql = "SELECT id, name, password, status FROM users WHERE email = ?";
    
    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $this->db->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {

        if (isset($user['status']) && $user['status'] === 'blocked') {
            return ['error' => 'Your account is blocked.'];
        }

        if (password_verify($password, $user['password'])) {
            $this->updateLastLogin($user['id']);
            unset($user['password']);
            return ['success' => true, 'user' => $user];
        }
    }

    return ['error' => 'Invalid email or password.'];
}

    /**
     * Check if email already exists
     */
    public function emailExists($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    /**
     * Update the last login time
     */
    private function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }
}