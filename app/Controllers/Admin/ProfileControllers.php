<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Models\Admin\ProfileModel;
use Exception;

class ProfileController {
    
    public function profile() {
        try {
            $db = Database::connect();
            $model = new ProfileModel($db);

            // Admin ID for now (usually you would get this from $_SESSION['user_id'])
            $admin_id = $_SESSION['user_id'] ?? 1; 

            $message = null;
            $error = null;

            // ── HANDLE FORM SUBMISSIONS ──
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';

                // 1. Update Personal Profile
                if ($action === 'update_profile') {
                    $name = $_POST['name'] ?? '';
                    $email = $_POST['email'] ?? '';
                    if ($model->updateAdminProfile($admin_id, $name, $email)) {
                        $message = "Profile details updated successfully.";
                    }
                }

                // 2. Change Security / Password
                if ($action === 'change_password') {
                    $current = $_POST['current_password'] ?? '';
                    $new = $_POST['new_password'] ?? '';
                    $hashed = $model->getHashedPassword($admin_id);

                    if (password_verify($current, $hashed)) {
                        $model->updatePassword($admin_id, password_hash($new, PASSWORD_BCRYPT));
                        $message = "Password changed successfully.";
                    } else {
                        $error = "Current password does not match our records.";
                    }
                }

                // 3. Update Site Settings (Mapped to `site_settings` table)
                if ($action === 'update_settings') {
                    
                    // Put all post text-based settings data in an array
                    $settingsData = [
                        'mobile_number'   => $_POST['mobile_number'] ?? '',
                        'whatsapp_number' => $_POST['whatsapp_number'] ?? '',
                        'email'           => $_POST['setting_email'] ?? '', // To differentiate from admin login email
                        'address'         => $_POST['address'] ?? '',
                        'google_map_link' => $_POST['google_map_link'] ?? '',
                        'facebook_link'   => $_POST['facebook_link'] ?? '',
                        'instagram_link'  => $_POST['instagram_link'] ?? '',
                        'site_name'       => $_POST['site_name'] ?? '',
                        'footer_text'     => $_POST['footer_text'] ?? ''
                    ];

                    // *If you are implementing File Uploads for Logo and Favicon, process $_FILES here*
                    // Example: $settingsData['logo'] = handleUpload($_FILES['logo']) ...

                    if ($model->updateSiteSettings($settingsData)) {
                        $message = "Global site settings updated successfully.";
                    } else {
                        $error = "Failed to update site settings.";
                    }
                }
            }

            // ── FETCH DATA FOR VIEW ──
            $adminData = $model->getAdminData($admin_id);
            $siteSettings = $model->getSiteSettings(); // Use this in your view

            // Load View
            include __DIR__ . '/../../Views/admin/profile.php';

            $db->close();

        } catch (Exception $e) {
            die("Profile Error: " . $e->getMessage());
        }
    }
}