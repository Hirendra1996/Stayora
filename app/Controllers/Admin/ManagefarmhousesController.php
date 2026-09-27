<?php
namespace App\Controllers\Admin;
use App\Helpers\CryptoHelper;
use App\Config\Database;
use App\Models\Admin\ManageFarmhousesModel;
use Exception;

class ManagefarmhousesController {
    
    public function manage_farmhouses() {
        try {
            $db = Database::connect();
            $model = new ManageFarmhousesModel($db);

            // ============================================
            // 1. HANDLE POST ACTIONS (UPDATE STATUS / DELETE)
            // ============================================
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
                $farmhouse_id = CryptoHelper::decrypt($_POST['farmhouse_id']);

                if ($_POST['action'] === 'toggle_status' && !empty($_POST['status'])) {
                    $model->updateStatus($farmhouse_id, $_POST['status']);
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['success_msg'] = "Property visibility updated successfully.";
                    redirect('admin/managefarmhouses');
                } 
                elseif ($_POST['action'] === 'delete') {
                    
                    $model->deleteFarmhouse($farmhouse_id);
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['success_msg'] = "Farmhouse permanently deleted from the platform.";
                    redirect('admin/managefarmhouses');
                }
            }

            // ============================================
            // 2. FETCH DATA FOR VIEW
            // ============================================
            
            // Allow URL filtering (e.g. ?status=active)
            $currentStatusFilter = $_GET['status'] ?? null;
            
            $farmhouses = $model->getAllFarmhouses($currentStatusFilter);
            $stats = $model->getFarmhouseStats();

            // Handle success toasts safely via sessions
            if (session_status() === PHP_SESSION_NONE) session_start();
            $success_message = $_SESSION['success_msg'] ?? null;
            unset($_SESSION['success_msg']);

            // Send extracted values smoothly to the VIEW HTML page
            $data = [
                'farmhouses'          => $farmhouses,
                'stats'               => $stats,
                'currentStatusFilter' => $currentStatusFilter,
                'success_message'     => $success_message
            ];
            
            extract($data);

            // Load View (Your Manage Farmhouses Grid HTML File)
            include __DIR__ . '/../../Views/admin/manage_farmhouses.php';
            
            $db->close();

        } catch (Exception $e) {
            die("Database Error managing properties: " . htmlspecialchars($e->getMessage()));
        }
    }
}
?>