<?php
namespace App\Controllers\User;

use App\Config\Database;
use App\Models\User\BookingModel;

class BookingController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }

        // 1. Get the booking ID from the URL (e.g. ?id=5)
        $bookingId = $_GET['id'] ?? null;

        if (!$bookingId) {
            $_SESSION['error'] = "Booking ID is missing.";
            redirect('dashboard');
        }

        // 2. Fetch Data from the Model
        $db = Database::connect();
        $bookingModel = new BookingModel($db); 
        $booking = $bookingModel->getBookingDetails($bookingId);

        if (!$booking) {
            $_SESSION['error'] = "The requested booking could not be found.";
            redirect('dashboard');
        }

        // 3. Data Processing (Calculate Total Nights)
        $startDateStr = (!empty($booking['start_date']) && $booking['start_date'] !== '0000-00-00') 
            ? $booking['start_date'] 
            : ($booking['check_in'] ?? 'now');
        $endDateStr = (!empty($booking['end_date']) && $booking['end_date'] !== '0000-00-00') 
            ? $booking['end_date'] 
            : ($booking['check_out'] ?? '+1 day');

        try {
            $checkIn = new \DateTime($startDateStr);
        } catch (\Exception $e) {
            $checkIn = new \DateTime('now');
        }
        try {
            $checkOut = new \DateTime($endDateStr);
        } catch (\Exception $e) {
            $checkOut = new \DateTime('+1 day');
        }
        $nightCount = max(1, (int)$checkIn->diff($checkOut)->days);

        $userData = [
            'name' => $_SESSION['user_name'] ?? 'User',
            'profile_image' => $_SESSION['profile_image'] ?? ''
        ];

        // 4. Pass data to the HTML View file
        include __DIR__ . '/../../Views/user/booking.php';
    }
}