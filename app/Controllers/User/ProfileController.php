<?php
namespace App\Controllers\User;

use App\Config\Database;
use App\Models\User\ProfileModel;

class ProfileController {

    public function index() {
        // 1. Authenticate user session
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }

        // 2. Fetch User Data
        $db = Database::connect();
$model = new ProfileModel($db);
$user = $model->getUserDetails($_SESSION['user_id']);
// Fetch booking count for the stats bar
$stats_sql = "SELECT COUNT(*) as total FROM booking_requests WHERE user_id = ?";
$stmt = $db->prepare($stats_sql);
if (!$stmt) {
    die("Prepare failed: " . $db->error);
}
$stmt->bind_param("i", $_SESSION['user_id']);

$stmt->execute();
$booking_count = $stmt->get_result()->fetch_assoc()['total'];

include __DIR__ . '/../../Views/user/profile.php';
}

    /**
     * Logic for handling profile update POST request
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            if (!isset($_SESSION['user_id'])) {
                redirect('login');
            }

            $db = Database::connect();
            $model = new ProfileModel($db);
            $userId = (int)$_SESSION['user_id'];

            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            // 1. Validation
            if (empty($name)) {
                redirect('user/profile?error=name_required');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                redirect('user/profile?error=invalid_email');
            }

            if (!empty($phone) && !preg_match('/^[0-9]{10}$/', $phone)) {
                redirect('user/profile?error=invalid_phone');
            }

            // Check duplicate email
            $dupEmail = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
            if ($dupEmail) {
                $dupEmail->bind_param("si", $email, $userId);
                $dupEmail->execute();
                if ($dupEmail->get_result()->num_rows > 0) {
                    redirect('user/profile?error=email_exists');
                }
                $dupEmail->close();
            }

            // Check duplicate phone
            if (!empty($phone)) {
                $dupPhone = $db->prepare("SELECT id FROM users WHERE phone = ? AND id != ? LIMIT 1");
                if ($dupPhone) {
                    $dupPhone->bind_param("si", $phone, $userId);
                    $dupPhone->execute();
                    if ($dupPhone->get_result()->num_rows > 0) {
                        redirect('user/profile?error=phone_exists');
                    }
                    $dupPhone->close();
                }
            }

            // Fetch current user details to check if phone has changed
            $currentUser = $model->getUserDetails($userId);
            $currentPhone = $currentUser['phone'] ?? '';
            $phoneChanged = (!empty($phone) && $phone !== $currentPhone);

            // 2. Update Basic Info (directly save name, email, and phone)
            $data = [
                'name'  => $name,
                'email' => $email,
                'phone' => $phone
            ];
            $model->updateBasicInfo($userId, $data);
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;

            // 3. Handle Profile Image Upload
            if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../../assets/images/uploads/profiles/';
                
                // Create directory if not exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
                $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                
                if (in_array($fileExtension, $allowedTypes, true)) {
                    $newFileName = 'user_' . $userId . '_' . time() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadPath)) {
                        $model->updateProfileImage($userId, $newFileName);
                        $_SESSION['profile_image'] = $newFileName;
                    }
                }
            }

            if (isset($_SESSION['pending_phone_change'])) {
                unset($_SESSION['pending_phone_change']);
            }

            $_SESSION['success'] = "Profile details updated successfully!";
            redirect('user/profile?msg=updated');
        }
    }

    /**
     * Verify phone change OTP (Fallback redirect)
     */
    public function verifyPhoneOtp() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['pending_phone_change']);
        redirect('user/profile');
    }

    /**
     * Resend phone change OTP (Fallback redirect)
     */
    public function resendPhoneOtp() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['pending_phone_change']);
        redirect('user/profile');
    }

    /**
     * Cancel pending phone change
     */
    public function cancelPhoneChange() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['pending_phone_change']);
        redirect('user/profile');
    }
}