<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Models\ReviewModel;
use Exception;

class ReviewController {
    private $db;
    private ReviewModel $model;

    public function __construct() {
        try {
            $this->db = Database::connect();
            $this->model = new ReviewModel($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    public function reviews(): void {
        $statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
        $validFilter  = in_array($statusFilter, ['pending', 'approved', 'rejected']) ? $statusFilter : null;

        $reviews = $this->model->getAllReviews($validFilter);
        $stats   = $this->model->getReviewStats();

        $pageTitle  = "Guest Reviews Moderation Desk";
        $activePage = "reviews";
        require_once __DIR__ . '/../../Views/admin/reviews.php';
    }

    public function approve(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->updateStatus($id, 'approved', 'Approved by Admin on ' . date('d M Y'));
            $_SESSION['success_msg'] = "Review #REV-{$id} approved and published live!";
        }
        redirect('admin/reviews');
    }

    public function reject(): void {
        $id = (int)($_POST['id'] ?? 0);
        $reason = trim($_POST['rejection_reason'] ?? 'Did not meet community review guidelines');
        if ($id > 0) {
            $this->model->updateStatus($id, 'rejected', $reason);
            $_SESSION['success_msg'] = "Review #REV-{$id} flagged as rejected.";
        }
        redirect('admin/reviews');
    }

    public function delete(): void {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->model->deleteReview($id);
            $_SESSION['success_msg'] = "Review deleted permanently.";
        }
        redirect('admin/reviews');
    }
}
