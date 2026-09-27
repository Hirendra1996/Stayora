<?php
namespace App\Controllers\User;
use App\Middleware\AuthMiddleware;
use App\Config\Database;
use App\Models\User\DashboardModel;

class DashboardController {
     public function __construct() {
        AuthMiddleware::check(); // runs on every request
    }

    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }

        $userId = $_SESSION['user_id'];

        // Connect to DB and Model
        $db = Database::connect();
        $dashboardModel = new DashboardModel($db);

        // Fetch all required data
        $userData = $dashboardModel->getUserInfo($userId);
        $stats    = $dashboardModel->getStats($userId);
        $requests = $dashboardModel->getAllRequests($userId); // Fetches ALL details
        $wishlist = $dashboardModel->getUserWishlist($userId); // Fetches wishlist data

        // Pass data to the view
        include __DIR__ . '/../../Views/user/dashboard.php';
    }
}