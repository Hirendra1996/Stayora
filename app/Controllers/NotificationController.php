<?php
namespace App\Controllers;

use App\Config\Database;
use App\Services\NotificationService;
use Exception;

class NotificationController {
    private $db;
    private NotificationService $service;

    public function __construct() {
        try {
            $this->db = Database::connect();
            $this->service = new NotificationService($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    private function getAuthContext(): array {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userType = 'customer';
        $userId = (int)($_SESSION['user_id'] ?? 0);

        if (!empty($_SESSION['admin_logged_in']) || !empty($_SESSION['admin_id'])) {
            $userType = 'admin';
            $userId = (int)($_SESSION['admin_id'] ?? $userId);
        } elseif (!empty($_SESSION['owner_logged_in'])) {
            $userType = 'owner';
        }

        return ['userId' => $userId, 'userType' => $userType];
    }

    /**
     * JSON API: Get unread count and latest notifications
     */
    public function apiGet(): void {
        header('Content-Type: application/json');
        $ctx = $this->getAuthContext();
        $unread = $this->service->getUnreadCount($ctx['userId'], $ctx['userType']);
        $items = $this->service->getRecent($ctx['userId'], $ctx['userType'], 10);
        echo json_encode([
            'status' => 'success',
            'unread' => $unread,
            'items'  => $items
        ]);
        exit;
    }

    /**
     * JSON API: Mark specific notification as read
     */
    public function apiMarkRead(): void {
        header('Content-Type: application/json');
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->service->markAsRead($id);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing ID']);
        }
        exit;
    }

    /**
     * JSON API: Mark all as read
     */
    public function apiMarkAllRead(): void {
        header('Content-Type: application/json');
        $ctx = $this->getAuthContext();
        $this->service->markAllAsRead($ctx['userId'], $ctx['userType']);
        echo json_encode(['status' => 'success']);
        exit;
    }

    /**
     * View: Notification Center
     */
    public function index(): void {
        $ctx = $this->getAuthContext();
        $notifications = $this->service->getRecent($ctx['userId'], $ctx['userType'], 50);
        $pageTitle = "Notification Center";

        require_once __DIR__ . '/../Views/notifications.php';
    }
}
