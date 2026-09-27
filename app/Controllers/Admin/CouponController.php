<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Models\Admin\CouponModel;
use App\Middleware\AuthMiddleware;
use Exception;

class CouponController {
    private $db;
    private CouponModel $model;

    public function __construct() {
        AuthMiddleware::adminOnly();
        try {
            $this->db = Database::connect();
            $this->model = new CouponModel($this->db);
        } catch (Exception $e) {
            die("System error: " . $e->getMessage());
        }
    }

    public function index() {
        $coupons = $this->model->getAllCoupons();
        $message = $_GET['msg'] ?? null;
        $error   = $_GET['error'] ?? null;
        include __DIR__ . '/../../Views/admin/coupons.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/coupons');
        }

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $code = strtoupper(trim($_POST['code'] ?? ''));
        if (empty($code)) {
            redirect('admin/coupons?error=' . urlencode('Coupon code is required.'));
        }

        $data = [
            'code'                => $code,
            'description'         => trim($_POST['description'] ?? ''),
            'discount_type'       => $_POST['discount_type'] ?? 'percentage',
            'discount_value'      => (float)($_POST['discount_value'] ?? 0.0),
            'min_booking_amount'  => (float)($_POST['min_booking_amount'] ?? 0.0),
            'max_discount_amount' => !empty($_POST['max_discount_amount']) ? (float)$_POST['max_discount_amount'] : null,
            'valid_from'          => !empty($_POST['valid_from']) ? $_POST['valid_from'] : null,
            'valid_until'         => !empty($_POST['valid_until']) ? $_POST['valid_until'] : null,
            'usage_limit'         => max(1, (int)($_POST['usage_limit'] ?? 1000)),
            'status'              => $_POST['status'] ?? 'active'
        ];

        $ok = $this->model->saveCoupon($data, $id);
        if ($ok) {
            redirect('admin/coupons?msg=' . urlencode('Coupon saved successfully!'));
        } else {
            redirect('admin/coupons?error=' . urlencode('Failed to save coupon. Code may already exist.'));
        }
    }

    public function toggle() {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->toggleStatus($id);
            redirect('admin/coupons?msg=' . urlencode('Coupon status updated.'));
        }
        redirect('admin/coupons');
    }

    public function delete() {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->deleteCoupon($id);
            redirect('admin/coupons?msg=' . urlencode('Coupon deleted successfully.'));
        }
        redirect('admin/coupons');
    }
}
