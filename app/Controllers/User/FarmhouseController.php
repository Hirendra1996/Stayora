<?php

namespace App\Controllers\User;

use App\Models\User\FarmhouseModel;

class FarmhouseController
{
    private FarmhouseModel $model;

    public function __construct()
    {
        $this->model = new FarmhouseModel();
    }

    // -------------------------------------------------------------------------
    // Entry point — routes to listing or detail based on ?id= param
    // Call this method from your router for ALL /farmhouses requests.
    // -------------------------------------------------------------------------
    public function index(): void
    {
        if (!empty($_GET['id'])) {
            $this->detail();
        } else {
            $this->listing();
        }
    }

    // -------------------------------------------------------------------------
    // Listing page — /user/farmhouses delegates to main catalog
    // -------------------------------------------------------------------------
    private function listing(): void
    {
        require_once __DIR__ . '/../FarmhousesController.php';
        (new \App\Controllers\FarmhousesController())->farmhouses();
    }

    // -------------------------------------------------------------------------
    // Detail page — /farmhouses?id=X
    // -------------------------------------------------------------------------
    private function detail(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->redirect404();
            return;
        }

        $farmhouse = $this->model->getById($id);

        if (!$farmhouse) {
            $this->redirect404();
            return;
        }

        $unavailableDates = $this->model->getUnavailableDates($id);
        $adminSettings    = $this->model->getAdminSettings();

        // Handle inquiry form POST
        $inquirySuccess = false;
        $inquiryError   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
            $data = [
                'name'    => trim($_POST['name']    ?? ''),
                'phone'   => trim($_POST['phone']   ?? ''),
                'type'    => trim($_POST['type']    ?? 'call'),
                'message' => trim($_POST['message'] ?? ''),
            ];

            if (empty($data['name']) || empty($data['phone'])) {
                $inquiryError = 'Name and phone number are required.';
            } elseif (!preg_match('/^[0-9+\-\s]{7,15}$/', $data['phone'])) {
                $inquiryError = 'Please enter a valid phone number.';
            } else {
                $userId = $_SESSION['user_id'] ?? null;
                $saved  = $this->model->submitInquiry($id, $data, $userId);
                if ($saved) {
                    $inquirySuccess = true;
                } else {
                    $inquiryError = 'Something went wrong. Please try again.';
                }
            }
        }

        include __DIR__ . '/../../Views/user/farmhouses.php';
    }

    // -------------------------------------------------------------------------
    // 404 handler
    // -------------------------------------------------------------------------
    private function redirect404(): void
    {
        http_response_code(404);
        // Use your existing 404 view or a simple message
        if (file_exists(__DIR__ . '/../../Views/errors/404.php')) {
            include __DIR__ . '/../../Views/errors/404.php';
        } else {
            echo '<h1>404 - Page Not Found</h1>';
        }
        exit;
    }
}
