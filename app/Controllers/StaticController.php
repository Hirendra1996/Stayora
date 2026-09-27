<?php

namespace App\Controllers;

use App\Config\Database;
use Exception;

class StaticController {
    
    public function about() {
        include __DIR__ . '/../Views/about.php';
    }

    public function contact() {
        // Only needed if you aren't already starting sessions in index.php
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        include __DIR__ . '/../Views/contact.php';
    }

    public function cancellation() {
        include __DIR__ . '/../Views/cancellation_policy.php';
    }

    public function cookiePolicy() {
        include __DIR__ . '/../Views/cookie_policy.php';
    }

    public function submitContact() {
        // Standardize sessions
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        // Block GET requests from direct access
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('contact');
        }

        // Grab values exactly as defined in the HTML form names 
        $fullName = trim($_POST['name'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $message  = trim($_POST['message'] ?? '');

        // Validation
        if (empty($fullName) || empty($email) || empty($message)) {
            $_SESSION['error'] = "Name, Email, and Message fields are required.";
            redirect('contact');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Please provide a valid email format.";
            redirect('contact');
        }

        try {
            $db = Database::connect();

            // SQL insert mapping to table definition (id and created_at usually generate automatically in MySQL)
            $sql = "INSERT INTO `contact_inquiries` (`full_name`, `phone`, `email`, `Message`) VALUES (?, ?, ?, ?)";
            
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                throw new Exception("SQL Preparation Failed.");
            }

            // Execute database bind (ssss means 4 strings are being inserted)
            $stmt->bind_param("ssss", $fullName, $phone, $email, $message);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Message sent successfully! Our team will get back to you shortly.";
            } else {
                throw new Exception("Execution Failed.");
            }

            $stmt->close();
            $db->close();

        } catch (Exception $e) {
            error_log("Form Save Error: " . $e->getMessage()); // Invisible to user, recorded in PHP error log
            $_SESSION['error'] = "Server Error: Unable to submit at this time. Please try again later.";
        }

        // Send user back to view alerts
        redirect('contact');
    }
}
?>