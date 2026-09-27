<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Models\Admin\DashboardModel;
use Exception;

class DashboardController {
    
    public function dashboard() {
        try {
            // 1. Establish Database Connection using your DB class
            $db = Database::connect();

            // 2. Initialize Model with mysqli connection
            $dashboardModel = new DashboardModel($db);

            // 3. Fetch data from DB
            $stats            = $dashboardModel->getStatistics();
            $recentBookings   = $dashboardModel->getRecentBookingRequests(6);
            $recentInquiries  = $dashboardModel->getRecentInquiries(6);
            $inquiries        = $dashboardModel->getAllInquiries();
            $recentProperties = $dashboardModel->getRecentProperties(4);

            // 4. Extract data for the view
            $data = [
                'stats'            => $stats,
                'recentBookings'   => $recentBookings,
                'recentInquiries'  => $recentInquiries,
                'inquiries'        => $inquiries,
                'recentProperties' => $recentProperties,
            ];
            extract($data); 

            // 5. Load the View
            include __DIR__ . '/../../Views/admin/dashboard.php';

            // Optional: Close connection when done
            $db->close();

        } catch (Exception $e) {
            // Handle DB Connection error (e.g. show an error view)
            echo "Dashboard Error: " . htmlspecialchars($e->getMessage());
            // include __DIR__ . '/../../Views/errors/500.php';
        }
    }
}
?>