<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Models\Admin\PropertyTypeModel;

class PropertyTypeController {
    private $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->model = new PropertyTypeModel();
    }

    public function index(): void {
        $propertyTypes = $this->model->getAll();
        $pageTitle = "Property Types & Categories";
        require_once __DIR__ . '/../../Views/admin/property_types.php';
    }

    public function save(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/property-types');
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            $_SESSION['error_msg'] = "Property type name is required.";
            redirect('admin/property-types');
        }

        $data = [
            'id'            => $id,
            'name'          => $name,
            'slug'          => trim($_POST['slug'] ?? ''),
            'description'   => trim($_POST['description'] ?? ''),
            'icon_class'    => trim($_POST['icon_class'] ?? 'villa'),
            'display_order' => (int)($_POST['display_order'] ?? 0),
            'status'        => trim($_POST['status'] ?? 'active'),
        ];

        if ($this->model->save($data)) {
            $_SESSION['success_msg'] = $id > 0 ? "Property type updated successfully." : "New property type created successfully.";
        } else {
            $_SESSION['error_msg'] = "Failed to save property type. Please check for duplicate names.";
        }

        redirect('admin/property-types');
    }

    public function toggle(): void {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id > 0) {
            $this->model->toggleStatus($id);
            $_SESSION['success_msg'] = "Property type status updated.";
        }
        redirect('admin/property-types');
    }

    public function delete(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            if ($this->model->delete($id)) {
                $_SESSION['success_msg'] = "Property type deleted successfully.";
            } else {
                $_SESSION['error_msg'] = "Cannot delete property type: properties are currently assigned to this category.";
            }
        }
        redirect('admin/property-types');
    }
}
