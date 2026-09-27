<?php
namespace App\Controllers\Owner;

use App\Config\Database;
use App\Models\Owner\OwnerDashboardModel;
use Exception;

/**
 * OwnerDashboardController
 * Handles: Owner Dashboard overview
 * Route: /owner/dashboard
 */
class OwnerDashboardController {

    private $db;
    private OwnerDashboardModel $model;

    public function __construct() {
        try {
            $this->db    = Database::connect();
            $this->model = new OwnerDashboardModel($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    //  DASHBOARD  →  /owner/dashboard
    // ================================================================

    public function index(): void {
        try {
            $ownerId = (int) ($_SESSION['user_id'] ?? 0);

            if (!$ownerId) {
                redirect('owner/login');
            }

            $stats      = $this->model->getDashboardStats($ownerId);
            $recent     = $this->model->getRecentFarmhouses($ownerId, 5);
            $bookings   = $this->model->getRecentBookingRequests($ownerId, 5);

            extract([
                'stats'    => $stats,
                'recent'   => $recent,
                'bookings' => $bookings,
            ]);

            include __DIR__ . '/../../Views/owner/dashboard.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Dashboard Error: " . htmlspecialchars($e->getMessage()));
        }
    }
}
