<?php
namespace App\Controllers\Owner;

use App\Config\Database;
use App\Models\Owner\OwnerProfileModel;
use Exception;

/**
 * OwnerProfileController
 * Handles: View profile | Update profile | Change password | Upload avatar
 * Route: /owner/profile
 */
class OwnerProfileController {

    private $db;
    private OwnerProfileModel $model;

    public function __construct() {
        try {
            $this->db    = Database::connect();
            $this->model = new OwnerProfileModel($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  PROFILE PAGE  →  GET /owner/profile
    // ================================================================

    public function index(): void {
        try {
            $ownerId = $this->resolveOwnerId();
            $owner   = $this->model->getOwnerById($ownerId);

            if (!$owner) {
                redirect('owner/login');
            }

            $success_message = $this->popFlash('success_message');
            $error_message   = $this->popFlash('error_message');

            extract([
                'owner'           => $owner,
                'success_message' => $success_message,
                'error_message'   => $error_message,
            ]);

            include __DIR__ . '/../../Views/owner/profile.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading profile: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  UPDATE PROFILE  →  POST /owner/profile/update
    // ================================================================

    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('owner/profile');
        }

        try {
            $ownerId = $this->resolveOwnerId();

            $name  = trim($_POST['name']  ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');

            // ── Validations ────────────────────────────────────────────
            if (empty($name)) {
                $this->flash('error_message', "Full name is required.");
                redirect('owner/profile');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->flash('error_message', "Invalid email format.");
                redirect('owner/profile');
            }

            if (!preg_match('/^[0-9]{10}$/', $phone)) {
                $this->flash('error_message', "Phone number must be exactly 10 digits.");
                redirect('owner/profile');
            }

            // Check email uniqueness (exclude current owner)
            if ($this->model->emailExistsForOther($email, $ownerId)) {
                $this->flash('error_message', "This email is already registered to another account.");
                redirect('owner/profile');
            }

            // ── Handle profile image upload ─────────────────────────────
            $profileImage = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $profileImage = $this->handleAvatarUpload($_FILES['profile_image']);
                if (!$profileImage) {
                    $this->flash('error_message', "Invalid image type. Only JPG, PNG, and WEBP are allowed.");
                    redirect('owner/profile');
                }
            }

            $profileData = [
                'name'  => $name,
                'email' => $email,
                'phone' => $phone,
            ];

            if ($profileImage) {
                $profileData['profile_image'] = $profileImage;
            }

            if ($this->model->updateProfile($ownerId, $profileData)) {
                // Keep session in sync
                $_SESSION['user_name']  = $name;
                $_SESSION['user_email'] = $email;
                if ($profileImage) {
                    $_SESSION['profile_image'] = $profileImage;
                }
                $this->flash('success_message', "Profile updated successfully.");
            } else {
                $this->flash('error_message', "Could not update profile. Please try again.");
            }

            redirect('owner/profile');

        } catch (Exception $e) {
            die("Error updating profile: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  CHANGE PASSWORD  →  POST /owner/profile/change-password
    // ================================================================

    public function changePassword(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('owner/profile');
        }

        try {
            $ownerId     = $this->resolveOwnerId();
            $current     = $_POST['current_password']      ?? '';
            $newPass     = $_POST['new_password']          ?? '';
            $confirm     = $_POST['confirm_password']      ?? '';

            if (empty($current) || empty($newPass) || empty($confirm)) {
                $this->flash('error_message', "All password fields are required.");
                redirect('owner/profile');
            }

            if ($newPass !== $confirm) {
                $this->flash('error_message', "New passwords do not match.");
                redirect('owner/profile');
            }

            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $newPass)) {
                $this->flash('error_message', "Password must be at least 8 characters and contain letters and numbers.");
                redirect('owner/profile');
            }

            $owner = $this->model->getOwnerById($ownerId);
            if (!$owner || !password_verify($current, $owner['password'])) {
                $this->flash('error_message', "Current password is incorrect.");
                redirect('owner/profile');
            }

            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            if ($this->model->updatePassword($ownerId, $hashed)) {
                $this->flash('success_message', "Password changed successfully.");
            } else {
                $this->flash('error_message', "Could not change password. Please try again.");
            }

            redirect('owner/profile');

        } catch (Exception $e) {
            die("Error changing password: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  PRIVATE HELPERS
    // ================================================================

    private function resolveOwnerId(): int {
        $id = (int) ($_SESSION['user_id'] ?? 0);
        if (!$id) {
            redirect('owner/login');
        }
        return $id;
    }

    /**
     * Move and rename an uploaded avatar image.
     * Returns the saved filename on success, null on failure.
     */
    private function handleAvatarUpload(array $file): ?string {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExts  = ['jpg', 'jpeg', 'png', 'webp'];

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file['type'], $allowedTypes, true) || !in_array($ext, $allowedExts, true)) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../../assets/images/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newName  = 'avatar_' . uniqid() . time() . '.' . $ext;
        $destPath = $uploadDir . $newName;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return $newName;
        }

        return null;
    }

    private function flash(string $key, string $message): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[$key] = $message;
    }

    private function popFlash(string $key): ?string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $msg = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $msg;
    }
}
