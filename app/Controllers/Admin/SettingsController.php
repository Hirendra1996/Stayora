<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Models\Admin\SettingsModel;
use Exception;

class SettingsController {

    private $db;
    private SettingsModel $model;

    public function __construct() {
        try {
            $this->db = Database::connect();
            $this->model = new SettingsModel($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    public function settings(): void {
        try {
            $uploadDir = __DIR__ . '/../../../assets/images/uploads/settings/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // ── Handle POST Form Submissions ─────────────────────────
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? 'save_settings';

                // 1. Remove Single Media Item
                if ($action === 'remove_media') {
                    $field = $_POST['field'] ?? '';
                    if (in_array($field, ['logo', 'favicon', 'payment_qr_code'], true)) {
                        $current = $this->model->getSettings();
                        if (!empty($current[$field]) && file_exists($uploadDir . $current[$field])) {
                            @unlink($uploadDir . $current[$field]);
                        }
                        $this->model->clearImage($field);
                        $this->flash('success_msg', ucfirst(str_replace('_', ' ', $field)) . " removed successfully.");
                    }
                    redirect('admin/settings');
                }

                // 2. Save All Settings
                if ($action === 'save_settings') {
                    $data = [
                        'site_name'            => trim($_POST['site_name']            ?? 'FarmLelo'),
                        'tagline'              => trim($_POST['tagline']              ?? ''),
                        'mobile_number'        => trim($_POST['mobile_number']        ?? ''),
                        'whatsapp_number'      => trim($_POST['whatsapp_number']      ?? ''),
                        'email'                => trim($_POST['email']                ?? ''),
                        'address'              => trim($_POST['address']              ?? ''),
                        'google_map_link'      => trim($_POST['google_map_link']      ?? ''),
                        'facebook_link'        => trim($_POST['facebook_link']        ?? ''),
                        'instagram_link'       => trim($_POST['instagram_link']       ?? ''),
                        'youtube_link'         => trim($_POST['youtube_link']         ?? ''),
                        'twitter_link'         => trim($_POST['twitter_link']         ?? ''),
                        'upi_id'               => trim($_POST['upi_id']               ?? ''),
                        'payment_instructions' => trim($_POST['payment_instructions'] ?? ''),
                        'footer_text'          => trim($_POST['footer_text']          ?? ''),
                        'meta_description'     => trim($_POST['meta_description']     ?? ''),
                    ];

                    // Handle Logo Upload
                    if (!empty($_FILES['logo']['name'])) {
                        $logoName = $this->uploadFile($_FILES['logo'], $uploadDir, 'logo_');
                        if ($logoName) {
                            $data['logo'] = $logoName;
                        }
                    }

                    // Handle Favicon Upload
                    if (!empty($_FILES['favicon']['name'])) {
                        $favName = $this->uploadFile($_FILES['favicon'], $uploadDir, 'favicon_');
                        if ($favName) {
                            $data['favicon'] = $favName;
                        }
                    }

                    // Handle Payment QR Code Upload
                    if (!empty($_FILES['payment_qr_code']['name'])) {
                        $qrName = $this->uploadFile($_FILES['payment_qr_code'], $uploadDir, 'payment_qr_');
                        if ($qrName) {
                            $data['payment_qr_code'] = $qrName;
                        }
                    }

                    if ($this->model->updateSettings($data)) {
                        $this->flash('success_msg', "System settings and payment configurations updated successfully!");
                    } else {
                        $this->flash('error_msg', "Failed to update system settings.");
                    }
                    redirect('admin/settings');
                }
            }

            // ── Load Current Settings ────────────────────────────────
            $settings = $this->model->getSettings();
            $success_message = $this->popFlash('success_msg');
            $error_message   = $this->popFlash('error_msg');

            include __DIR__ . '/../../Views/admin/settings.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading settings: " . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Helper to process single image upload
     */
    private function uploadFile(array $file, string $targetDir, string $prefix = 'file_'): ?string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'ico'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts, true)) {
            return null;
        }

        $fileName = $prefix . uniqid() . '_' . time() . '.' . $ext;
        $targetPath = $targetDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $fileName;
        }

        return null;
    }

    // Flash Messaging Helpers
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
