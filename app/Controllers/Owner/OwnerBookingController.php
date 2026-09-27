<?php
namespace App\Controllers\Owner;

use App\Config\Database;
use App\Helpers\CryptoHelper;
use App\Models\Owner\OwnerBookingModel;
use App\Models\Owner\OwnerProfileModel;
use Exception;

/**
 * OwnerBookingController
 * Handles booking reservation listing, details inspection, and lifecycle management for Farmhouse Hosts.
 *
 * CRITICAL PRIVACY REQUIREMENT:
 * Guest personal contact information (phone, email) is NEVER exposed to the owner.
 */
class OwnerBookingController {

    private $db;
    private OwnerBookingModel $model;

    public function __construct() {
        try {
            $this->db = Database::connect();
            $this->model = new OwnerBookingModel($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Show bookings listing for the authenticated owner
     * Route: /owner/bookings
     */
    public function index(): void {
        try {
            $ownerId = (int)($_SESSION['user_id'] ?? 0);
            if (!$ownerId) {
                redirect('owner/login');
            }

            // Read filters
            $statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
            $farmFilter   = trim($_GET['farmhouse_id'] ?? '');
            $searchQuery  = trim($_GET['q'] ?? '');
            $sortBy       = strtolower(trim($_GET['sort'] ?? 'newest'));
            $perPage      = max(5, min(100, (int)($_GET['per_page'] ?? 10)));
            $currentPage  = max(1, (int)($_GET['page'] ?? 1));
            $offset       = ($currentPage - 1) * $perPage;

            $filters = [
                'status'       => $statusFilter,
                'farmhouse_id' => $farmFilter,
                'q'            => $searchQuery,
            ];

            $bookings   = $this->model->getBookingsByOwner($ownerId, $filters, $sortBy, $perPage, $offset);
            $totalCount = $this->model->getTotalBookingCount($ownerId, $filters);
            $totalPages = max(1, (int)ceil($totalCount / $perPage));
            $stats      = $this->model->getOwnerBookingStats($ownerId);
            $farmhouses = $this->model->getOwnerFarmhousesList($ownerId);

            // Fetch owner profile for header display
            $profModel  = new OwnerProfileModel($this->db);
            $owner      = $profModel->getOwnerById($ownerId) ?? [];

            // Encrypt IDs for UI links
            $bookings = array_map(function(array $b): array {
                $b['encrypted_id'] = CryptoHelper::encrypt((string)$b['booking_id']);
                // Ensure stay night count is calculated
                $ci = !empty($b['check_in']) ? $b['check_in'] : ($b['start_date'] ?? null);
                $co = !empty($b['check_out']) ? $b['check_out'] : ($b['end_date'] ?? null);
                if ($ci && $co) {
                    $nights = (int)ceil((strtotime($co) - strtotime($ci)) / 86400);
                    $b['nights'] = max(1, $nights);
                } else {
                    $b['nights'] = 1;
                }
                return $b;
            }, $bookings);

            $success_message = $this->popFlash('owner_success_msg');
            $error_message   = $this->popFlash('owner_error_msg');

            include __DIR__ . '/../../Views/owner/bookings.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading owner bookings: " . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * AJAX Endpoint: Get single booking details for modal inspector
     * Route: /owner/bookings/details?id=...
     */
    public function details(): void {
        header('Content-Type: application/json');
        try {
            $ownerId = (int)($_SESSION['user_id'] ?? 0);
            if (!$ownerId) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                $this->db->close();
                return;
            }

            $rawId = $_GET['id'] ?? '';
            $bookingId = $this->resolveId($rawId);

            $detail = $this->model->getBookingDetailForOwner($bookingId, $ownerId);
            if (!$detail) {
                echo json_encode(['success' => false, 'message' => 'Booking not found or not authorized.']);
                $this->db->close();
                return;
            }

            // Explicit safety: unset any potential contact keys
            unset($detail['phone'], $detail['email'], $detail['user_phone'], $detail['user_email'], $detail['contact_phone'], $detail['whatsapp_number']);

            $detail['encrypted_id'] = CryptoHelper::encrypt((string)$detail['booking_id']);
            $detail['booking_type'] = strtolower($detail['booking_type'] ?? 'complete');
            $detail['booking_type_label'] = ($detail['booking_type'] === 'per_room') ? 'Per Room Booking' : 'Complete Farmhouse';
            $detail['rooms_allocated'] = max(1, (int)($detail['rooms'] ?? 1));
            $detail['formatted_created'] = !empty($detail['created_at'])
                ? date('d M Y, h:i A', strtotime($detail['created_at']))
                : 'N/A';

            $checkIn  = !empty($detail['check_in']) ? $detail['check_in'] : ($detail['start_date'] ?? null);
            $checkOut = !empty($detail['check_out']) ? $detail['check_out'] : ($detail['end_date'] ?? null);

            $detail['formatted_check_in']  = $checkIn ? date('d M Y', strtotime($checkIn)) : 'N/A';
            $detail['formatted_check_out'] = $checkOut ? date('d M Y', strtotime($checkOut)) : 'N/A';

            if ($checkIn && $checkOut) {
                $days = (int)ceil((strtotime($checkOut) - strtotime($checkIn)) / 86400);
                $detail['stay_nights'] = max(1, $days);
            } else {
                $detail['stay_nights'] = 1;
            }

            $detail['formatted_price'] = number_format((float)($detail['price'] ?? 0));

            echo json_encode([
                'success' => true,
                'data'    => $detail,
            ]);
            $this->db->close();

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Handle Owner status update (e.g. mark as completed or cancelled)
     * Route: /owner/bookings/update-status (POST)
     */
    public function updateStatus(): void {
        try {
            $ownerId = (int)($_SESSION['user_id'] ?? 0);
            if (!$ownerId) {
                redirect('owner/login');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('owner/bookings');
            }

            $rawId     = $_POST['booking_id'] ?? '';
            $bookingId = $this->resolveId($rawId);
            $newStatus = strtolower(trim($_POST['status'] ?? ''));

            if (!in_array($newStatus, ['approved', 'completed', 'cancelled'], true)) {
                $this->flash('owner_error_msg', 'Invalid status update requested.');
                redirect('owner/bookings');
            }

            $updated = $this->model->updateBookingStatusByOwner($bookingId, $ownerId, $newStatus);
            if ($updated) {
                $label = ucfirst($newStatus);
                if ($newStatus === 'approved') $label = 'Confirmed';
                $this->flash('owner_success_msg', "Booking #{$bookingId} has been updated to {$label}.");

                // Dispatch in-app notification & transactional email (F56, F57)
                try {
                    require_once __DIR__ . '/../../Services/NotificationService.php';
                    require_once __DIR__ . '/../../Services/MailNotificationService.php';
                    $notifService = new \App\Services\NotificationService($this->db);
                    
                    // Fetch guest user_id for this booking
                    $stmtB = $this->db->prepare("SELECT b.*, f.title as farmhouse_title, u.email as customer_email, u.name as customer_name FROM booking_requests b JOIN farmhouses f ON b.farmhouse_id = f.id LEFT JOIN users u ON b.user_id = u.id WHERE b.id = ?");
                    if ($stmtB) {
                        $stmtB->bind_param("i", $bookingId);
                        $stmtB->execute();
                        $bRow = $stmtB->get_result()->fetch_assoc();
                        if ($bRow) {
                            $notifService->notify(
                                $bRow['user_id'] ? (int)$bRow['user_id'] : null,
                                'customer',
                                "Booking {$label}! 🎉",
                                "Your reservation at {$bRow['farmhouse_title']} has been updated to {$label}.",
                                'booking/voucher?id=' . $bookingId,
                                'booking'
                            );
                            if ($newStatus === 'approved') {
                                $mailService = new \App\Services\MailNotificationService($this->db);
                                $mailService->triggerBookingApproved($bRow);
                            }
                        }
                    }
                } catch (\Throwable $t) {
                    error_log("Notification dispatch error: " . $t->getMessage());
                }
            } else {
                $this->flash('owner_error_msg', "Failed to update booking status or you are not authorized to modify this property's booking.");
            }

            redirect('owner/bookings');

        } catch (Exception $e) {
            $this->flash('owner_error_msg', "Error updating booking: " . htmlspecialchars($e->getMessage()));
            redirect('owner/bookings');
        }
    }

    private function resolveId(string $raw): int {
        $raw = trim($raw);
        if (is_numeric($raw) && (int)$raw > 0) {
            return (int)$raw;
        }
        $dec = CryptoHelper::decrypt($raw);
        if ($dec && is_numeric($dec) && (int)$dec > 0) {
            return (int)$dec;
        }
        throw new Exception("Invalid or unreadable booking identifier.");
    }

    private function flash(string $key, string $msg): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[$key] = $msg;
    }

    private function popFlash(string $key): ?string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION[$key])) {
            $m = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $m;
        }
        return null;
    }
}
