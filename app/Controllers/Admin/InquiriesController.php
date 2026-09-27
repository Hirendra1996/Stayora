<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Models\Admin\InquiriesModel;
use Exception;

class InquiriesController {
    
    public function inquiries() {
        try {
            // 1. Establish Database Connection
            $db = Database::connect();
            
            // 2. Load the Inquiries Model
            $model = new InquiriesModel($db);

            // ============================================
            // 3. HANDLE POST ACTIONS (UPDATE / DELETE)
            // ============================================
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['action'])) {
                    
                    $inquiry_id = (int)$_POST['inquiry_id'];

                    if ($_POST['action'] === 'update_status' && !empty($_POST['status'])) {
                        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;
                        $follow_up_date = !empty($_POST['follow_up_date']) ? trim($_POST['follow_up_date']) : null;
                        $model->updateStatus($inquiry_id, $_POST['status'], $notes, $follow_up_date);
                        
                        if (session_status() === PHP_SESSION_NONE) session_start();
                        $_SESSION['success_msg'] = "Inquiry status updated successfully.";
                        redirect('admin/inquiries');
                    } 
                    elseif ($_POST['action'] === 'delete') {
                        $model->deleteInquiry($inquiry_id);
                        
                        if (session_status() === PHP_SESSION_NONE) session_start();
                        $_SESSION['success_msg'] = "Inquiry securely deleted.";
                        redirect('admin/inquiries');
                    }
                }
            }

            // ============================================
            // 4. FETCH DATA FOR VIEW
            // ============================================
            
            // Look for `?status=new` in URL for Filtering
            $currentStatusFilter = $_GET['status'] ?? null;
            
            // Grab specific matching inquiries and overarching number stats
            $inquiries = $model->getAllInquiries($currentStatusFilter);
            $stats     = $model->getInquiryStats();

            // Set Up Success message UI if coming from redirect
            if (session_status() === PHP_SESSION_NONE) session_start();
            $success_message = $_SESSION['success_msg'] ?? null;
            unset($_SESSION['success_msg']); // clear after showing

            // Send these exact variables to view
            $data = [
                'inquiries'           => $inquiries,
                'stats'               => $stats,
                'currentStatusFilter' => $currentStatusFilter,
                'success_message'     => $success_message
            ];
            
            extract($data); 

            // Load View
            include __DIR__ . '/../../Views/admin/inquiries.php';
            
            // Cleanup Database Call
            $db->close();

        } catch (Exception $e) {
            die("Dashboard Error loading inquiries: " . htmlspecialchars($e->getMessage()));
        }
    }
}
?>