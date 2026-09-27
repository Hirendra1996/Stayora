<?php
namespace App\Controllers\User;

use App\Config\Database;
use App\Models\User\User; // ✅ correct import

class AuthController {
    
    private $userModel;

    public function __construct() {
        $db = Database::connect();
        $this->userModel = new User($db);
    }

    // rest code same...


    public function login() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            // Detect if logging in as user or owner from a hidden input
            $role = $_POST['role'] ?? 'user'; 

            $result = $this->userModel->login($email, $password);

            if (isset($result['success'])) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['user_name'] = $result['user']['name'];
                $_SESSION['role'] = 'user';
                
                redirect('dashboard');
            } else {
                $error = $result['error'];
            }
        }
        include __DIR__ . '/../../Views/user/login.php';
    }

    public function register() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'     => $_POST['name'],
                'email'    => $_POST['email'],
                'phone'    => $_POST['phone'],
                'password' => $_POST['password']
            ];
            
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $error = "Passwords do not match!";
            } elseif ($this->userModel->emailExists($data['email'])) {
                $error = "Email already registered!";
            } else {
                if ($this->userModel->register($data)) {
                    redirect('login?registered=true');
                }
                $error = "Something went wrong.";
            }
        }
        include __DIR__ . '/../../Views/user/register.php';
    }
}