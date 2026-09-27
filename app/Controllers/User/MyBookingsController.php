<?php
namespace App\Controllers\User;

use App\Config\Database;
use App\Models\User\MyBookingModel;

class MyBookingsController {

    public function index() {
        // 1. Check if session is started and user is logged in
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }

        $userId = $_SESSION['user_id'];

        // 2. Fetch data via Model
        $db = Database::connect();
        $model = new MyBookingModel($db);

        $status = $_GET['status'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        // We fetch requests with optional status & search filters
        $userRequests = $model->getUserRequests($userId, $status, $search);

        // 3. Load the View
        include __DIR__ . '/../../Views/user/my-bookings.php';
    }

    /**
     * Handle cancellation if a user clicks a cancel button
     */
    public function cancel() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $requestId = $_POST['request_id'];
            $userId = $_SESSION['user_id'];

            $db = Database::connect();
            $model = new MyBookingModel($db);

            if ($model->cancelRequest($requestId, $userId)) {
                redirect('user/my-bookings?msg=cancelled');
            } else {
                redirect('user/my-bookings?error=cannot_cancel');
            }
        }
    }
}