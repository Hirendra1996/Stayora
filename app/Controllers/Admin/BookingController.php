<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Models\Admin\BookingModel;

class BookingController {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function bookings() {
        $model = new BookingModel($this->db);
        
        // 1. Determine active tab/filter
        $statusFilter = $_GET['tab'] ?? 'all';
        
        // 2. Process actions (Cancel/Complete)
        if (isset($_POST['action'])) {
            $model->updateStatus((int)$_POST['id'], $_POST['action_status']);
        }

        // 3. Get Bookings
        $bookings = $model->getFilteredBookings($statusFilter);

        // Include view
        include __DIR__ . '/../../Views/admin/bookings.php';
    }
}