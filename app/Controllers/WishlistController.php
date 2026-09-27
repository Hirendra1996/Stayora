<?php
namespace App\Controllers;

use App\Models\WishlistModel; // Make sure this matches your filename
use App\Helpers\CryptoHelper;

class WishlistController
{
     public function toggle() {
        // CLEAN UP: Wipe any accidental spaces/errors that might have already printed
        if (ob_get_length()) ob_clean(); 

        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Auth Check
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Please Login']);
            exit;
        }

        // 2. Data Check
        $input = json_decode(file_get_contents('php://input'), true);
        $farmhouseId = CryptoHelper::decrypt($input['farmhouse_id'] ?? '');

        if (!$farmhouseId) {
            echo json_encode(['success' => false, 'message' => 'Security Error']);
            exit;
        }

        // 3. Database Toggle
        try {
            $result = WishlistModel::toggleWishlist($_SESSION['user_id'], $farmhouseId);
            echo json_encode([
                'success' => true, 
                'action'  => $result['status'] // 'added' or 'removed'
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'DB Error']);
        }
        exit;
    }

    public function wishlist() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }
        $myWishlist = WishlistModel::getUserWishlist($_SESSION['user_id']);
        require_once __DIR__ . '/../Views/wishlist.php';
    }
}