<?php
namespace App\Services;

use App\Config\Database;
use mysqli;
use Exception;
use Throwable;

class OtpService {

    private mysqli $db;
    private array $config;

    public function __construct(?mysqli $db = null) {
        $this->db = $db ?? Database::connect();
        $this->config = require __DIR__ . '/../Config/sms.php';
    }

    /**
     * Get client IP address
     */
    public function getClientIp(): string {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] 
            ?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
            ?? $_SERVER['REMOTE_ADDR'] 
            ?? '127.0.0.1';

        // In case of multiple comma-separated IPs
        if (str_contains($ip, ',')) {
            $parts = explode(',', $ip);
            $ip = trim($parts[0]);
        }

        return substr($ip, 0, 45);
    }

    /**
     * Check if a new OTP request is allowed under rate limits and cooldown
     */
    public function checkRateLimits(string $identifier, string $purpose): array {
        $ip = $this->getClientIp();
        $otpConfig = $this->config['otp'] ?? [];
        $cooldownSeconds = $otpConfig['cooldown_seconds'] ?? 60;
        $maxResendsHour  = $otpConfig['max_resends_hour'] ?? 5;
        $maxIpRate       = $otpConfig['rate_limit_ip'] ?? 15;

        // 1. IP Rate Limiting: Max requests per IP in the last 15 minutes
        $stmtIp = $this->db->prepare(
            "SELECT COUNT(*) AS cnt FROM otp_verifications 
             WHERE ip_address = ? AND created_at > (NOW() - INTERVAL 15 MINUTE)"
        );
        if ($stmtIp) {
            $stmtIp->bind_param("s", $ip);
            $stmtIp->execute();
            $ipCount = (int)($stmtIp->get_result()->fetch_assoc()['cnt'] ?? 0);
            $stmtIp->close();

            if ($ipCount >= $maxIpRate) {
                return [
                    'allowed' => false,
                    'error'   => 'Too many OTP requests from this connection. Please wait 15 minutes before trying again.',
                    'wait_seconds' => 900,
                ];
            }
        }

        // 2. Hourly Limit per identifier
        $stmtHour = $this->db->prepare(
            "SELECT COUNT(*) AS cnt FROM otp_verifications 
             WHERE identifier = ? AND purpose = ? AND created_at > (NOW() - INTERVAL 1 HOUR)"
        );
        if ($stmtHour) {
            $stmtHour->bind_param("ss", $identifier, $purpose);
            $stmtHour->execute();
            $hourCount = (int)($stmtHour->get_result()->fetch_assoc()['cnt'] ?? 0);
            $stmtHour->close();

            if ($hourCount >= $maxResendsHour) {
                return [
                    'allowed' => false,
                    'error'   => "Maximum OTP limit reached for this account. Please try again in 1 hour.",
                    'wait_seconds' => 3600,
                ];
            }
        }

        // 3. Cooldown check: Most recent OTP for identifier + purpose
        $stmtRecent = $this->db->prepare(
            "SELECT created_at, TIMESTAMPDIFF(SECOND, created_at, NOW()) AS elapsed 
             FROM otp_verifications 
             WHERE identifier = ? AND purpose = ? 
             ORDER BY id DESC LIMIT 1"
        );
        if ($stmtRecent) {
            $stmtRecent->bind_param("ss", $identifier, $purpose);
            $stmtRecent->execute();
            $recent = $stmtRecent->get_result()->fetch_assoc();
            $stmtRecent->close();

            if ($recent && isset($recent['elapsed'])) {
                $elapsed = (int)$recent['elapsed'];
                if ($elapsed < $cooldownSeconds) {
                    $remaining = $cooldownSeconds - $elapsed;
                    return [
                        'allowed'      => false,
                        'error'        => "Please wait {$remaining} seconds before requesting another code.",
                        'wait_seconds' => $remaining,
                    ];
                }
            }
        }

        return ['allowed' => true, 'wait_seconds' => 0];
    }

    /**
     * Generate a new secure 6-digit OTP and store in database
     *
     * @param string $identifier Mobile number or Email
     * @param string $purpose registration | phone_change | forgot_password_sms | forgot_password_email
     * @param int|null $userId User ID if known
     * @param string $role users | owners | admins
     * @param array $metaData Any session context (e.g. pending registration values)
     * @return array
     */
    public function generateOtp(
        string $identifier, 
        string $purpose, 
        ?int $userId = null, 
        string $role = 'users', 
        array $metaData = []
    ): array {
        // Enforce rate limits
        $rateCheck = $this->checkRateLimits($identifier, $purpose);
        if (!$rateCheck['allowed']) {
            return [
                'success'      => false,
                'otp'          => null,
                'error'        => $rateCheck['error'],
                'wait_seconds' => $rateCheck['wait_seconds'] ?? 60,
            ];
        }

        // Generate cryptographically secure 6-digit number
        try {
            $otp = (string)random_int(100000, 999999);
        } catch (Throwable $e) {
            // High-entropy fallback
            $otp = (string)mt_rand(100000, 999999);
        }

        $otpConfig = $this->config['otp'] ?? [];
        $expirySeconds = (int)($otpConfig['expiry_seconds'] ?? 600); // 10 minutes
        $maxAttempts   = (int)($otpConfig['max_attempts'] ?? 5);
        $expiresAt     = date('Y-m-d H:i:s', time() + $expirySeconds);
        $ip            = $this->getClientIp();
        $metaJson      = !empty($metaData) ? json_encode($metaData, JSON_UNESCAPED_UNICODE) : null;

        // Invalidate any previously unverified OTPs for this identifier & purpose
        $stmtInvalidate = $this->db->prepare(
            "UPDATE otp_verifications 
             SET expires_at = NOW() 
             WHERE identifier = ? AND purpose = ? AND is_verified = 0 AND expires_at > NOW()"
        );
        if ($stmtInvalidate) {
            $stmtInvalidate->bind_param("ss", $identifier, $purpose);
            $stmtInvalidate->execute();
            $stmtInvalidate->close();
        }

        // Insert new OTP record
        $stmtInsert = $this->db->prepare(
            "INSERT INTO otp_verifications 
             (identifier, otp_code, purpose, user_id, role, meta_data, attempts, max_attempts, expires_at, is_verified, ip_address, created_at)
             VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?, 0, ?, NOW())"
        );

        if (!$stmtInsert) {
            return [
                'success' => false,
                'otp'     => null,
                'error'   => "Database Error: " . $this->db->error,
            ];
        }

        $stmtInsert->bind_param(
            "sssississ",
            $identifier,
            $otp,
            $purpose,
            $userId,
            $role,
            $metaJson,
            $maxAttempts,
            $expiresAt,
            $ip
        );

        if ($stmtInsert->execute()) {
            $insertId = $stmtInsert->insert_id;
            $stmtInsert->close();

            return [
                'success'        => true,
                'otp_id'         => $insertId,
                'otp'            => $otp,
                'expires_at'     => $expiresAt,
                'expiry_seconds' => $expirySeconds,
                'cooldown'       => $otpConfig['cooldown_seconds'] ?? 60,
                'error'          => null,
            ];
        }

        $errorMsg = $stmtInsert->error;
        $stmtInsert->close();
        return [
            'success' => false,
            'otp'     => null,
            'error'   => "Failed to store OTP: " . $errorMsg,
        ];
    }

    /**
     * Validate an entered OTP
     *
     * @param string $identifier Mobile or Email
     * @param string $purpose Context
     * @param string $inputOtp Code entered by user
     * @return array
     */
    public function validateOtp(string $identifier, string $purpose, string $inputOtp): array {
        $inputOtp = trim($inputOtp);

        if (empty($inputOtp) || !preg_match('/^[0-9]{6}$/', $inputOtp)) {
            return [
                'success' => false,
                'status'  => 'format_error',
                'error'   => 'Please enter a valid 6-digit verification code.',
            ];
        }

        // Fetch the latest OTP record for this identifier and purpose
        $stmt = $this->db->prepare(
            "SELECT * FROM otp_verifications 
             WHERE identifier = ? AND purpose = ? 
             ORDER BY id DESC LIMIT 1"
        );

        if (!$stmt) {
            return [
                'success' => false,
                'status'  => 'db_error',
                'error'   => "Database error: " . $this->db->error,
            ];
        }

        $stmt->bind_param("ss", $identifier, $purpose);
        $stmt->execute();
        $record = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$record) {
            return [
                'success' => false,
                'status'  => 'not_found',
                'error'   => 'No active verification code found for this account. Please request a new code.',
            ];
        }

        // 1. Check if already verified/used
        if ((int)$record['is_verified'] === 1) {
            return [
                'success' => false,
                'status'  => 'already_used',
                'error'   => 'This verification code has already been used. Please request a new one.',
            ];
        }

        // 2. Check max attempts lockout
        if ((int)$record['attempts'] >= (int)$record['max_attempts']) {
            return [
                'success' => false,
                'status'  => 'locked',
                'error'   => 'Too many incorrect attempts. For security, this code has been deactivated. Please request a new code.',
            ];
        }

        // 3. Check expiration
        if (strtotime($record['expires_at']) < time()) {
            return [
                'success' => false,
                'status'  => 'expired',
                'error'   => 'This verification code has expired. Please click resend to get a new code.',
            ];
        }

        // 4. Secure string comparison
        $isMatch = hash_equals((string)$record['otp_code'], (string)$inputOtp);

        if (!$isMatch) {
            // Increment failed attempts
            $newAttempts = (int)$record['attempts'] + 1;
            $stmtUp = $this->db->prepare("UPDATE otp_verifications SET attempts = ? WHERE id = ?");
            if ($stmtUp) {
                $stmtUp->bind_param("ii", $newAttempts, $record['id']);
                $stmtUp->execute();
                $stmtUp->close();
            }

            $remaining = (int)$record['max_attempts'] - $newAttempts;
            if ($remaining <= 0) {
                return [
                    'success'            => false,
                    'status'             => 'locked',
                    'remaining_attempts' => 0,
                    'error'              => 'Too many incorrect attempts. This code is now invalid. Please request a new one.',
                ];
            }

            return [
                'success'            => false,
                'status'             => 'invalid',
                'remaining_attempts' => $remaining,
                'error'              => "Invalid verification code. You have {$remaining} attempt(s) remaining.",
            ];
        }

        // 5. Success! Mark as verified
        $stmtVerify = $this->db->prepare(
            "UPDATE otp_verifications 
             SET is_verified = 1, verified_at = NOW() 
             WHERE id = ?"
        );
        if ($stmtVerify) {
            $stmtVerify->bind_param("i", $record['id']);
            $stmtVerify->execute();
            $stmtVerify->close();
        }

        $metaData = !empty($record['meta_data']) ? json_decode($record['meta_data'], true) : [];

        return [
            'success'   => true,
            'status'    => 'verified',
            'record_id' => $record['id'],
            'user_id'   => $record['user_id'],
            'role'      => $record['role'],
            'meta_data' => $metaData,
            'error'     => null,
        ];
    }
}
