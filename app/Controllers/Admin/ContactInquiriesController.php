<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Models\Admin\ContactInquiriesModel;
use App\Helpers\CryptoHelper;
use Exception;

class ContactInquiriesController
{
    // ============================================
    // MAIN — List all contact inquiries
    // ============================================

    public function index(): void
    {
        try {
            $db    = Database::connect();
            $model = new ContactInquiriesModel($db);

            // ----------------------------------------
            // HANDLE POST ACTIONS (DELETE)
            // ----------------------------------------
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

                $encryptedId = $_POST['encrypted_id'] ?? '';

                if ($_POST['action'] === 'delete' && !empty($encryptedId)) {
                    $model->deleteInquiry($encryptedId);

                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['success_msg'] = "Contact inquiry deleted successfully.";
                    redirect('admin/contact-inquiries');
                }
            }

            // ----------------------------------------
            // FETCH DATA FOR VIEW
            // ----------------------------------------
            $inquiries = $model->getAllInquiries();
            $stats     = $model->getInquiryStats();

            if (session_status() === PHP_SESSION_NONE) session_start();
            $success_message = $_SESSION['success_msg'] ?? null;
            unset($_SESSION['success_msg']);

            $data = [
                'inquiries'       => $inquiries,   // each row has ['encrypted_id'] pre-set
                'stats'           => $stats,
                'success_message' => $success_message,
            ];

            extract($data);
            include __DIR__ . '/../../Views/admin/contact_inquiries.php';

            $db->close();

        } catch (Exception $e) {
            die("Error loading contact inquiries: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ============================================
    // VIEW — Single inquiry detail page
    // ============================================

    public function view(): void
    {
        try {
            // Encrypted ID comes from the URL query string: ?id=<encrypted>
            $encryptedId = $_GET['id'] ?? '';

            if (empty($encryptedId)) {
                redirect('admin/contact-inquiries');
            }

            $db      = Database::connect();
            $model   = new ContactInquiriesModel($db);
            $inquiry = $model->getInquiryByEncryptedId($encryptedId);

            if (!$inquiry) {
                $db->close();
                http_response_code(404);
                die("Inquiry not found.");
            }

            extract(['inquiry' => $inquiry]);
            include __DIR__ . '/../../Views/admin/contact_inquiry_detail.php';

            $db->close();

        } catch (Exception $e) {
            die("Error loading inquiry detail: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ============================================
    // DELETE — Handle standalone delete request
    //          (useful for AJAX or direct route)
    // ============================================

    public function delete(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('admin/contact-inquiries');
            }

            $encryptedId = $_POST['encrypted_id'] ?? '';

            if (empty($encryptedId)) {
                redirect('admin/contact-inquiries');
            }

            $db      = Database::connect();
            $model   = new ContactInquiriesModel($db);
            $deleted = $model->deleteInquiry($encryptedId);
            $db->close();

            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['success_msg'] = $deleted
                ? "Contact inquiry deleted successfully."
                : "Could not delete the inquiry. Please try again.";

            redirect('admin/contact-inquiries');

        } catch (Exception $e) {
            die("Error deleting inquiry: " . htmlspecialchars($e->getMessage()));
        }
    }
}
?>
