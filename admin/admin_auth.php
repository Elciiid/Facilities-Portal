<?php
/**
 * Admin Authentication - Supabase/PostgreSQL Edition
 * Handles login verification using fcl_app_users table.
 */

// 1. Initialize Database & Session (Centralized)
require_once __DIR__ . '/../connection/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!empty($username) && !empty($password)) {
        try {
            // Query for the user in fcl_app_users
            $query = "SELECT * FROM fcl_app_users WHERE LOWER(username) = LOWER(?) LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set session variables from database
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['full_name'];
                $_SESSION['employee_id'] = $user['employee_id'];
                $_SESSION['department'] = $user['department'];
                $_SESSION['role'] = $user['role'];
                
                $_SESSION['admin_authenticated'] = true;
                $_SESSION['is_admin'] = ($user['role'] === 'admin');
                $_SESSION['admin_login_time'] = time();

                // Redirect to admin panel
                header("Location: /admin/admin.php");
                exit();
            } else {
                // Invalid credentials
                header("Location: /admin/admin_login.php?error=invalid");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Auth Error: " . $e->getMessage());
            header("Location: /admin/admin_login.php?error=system");
            exit();
        }
    } else {
        header("Location: /admin/admin_login.php?error=missing");
        exit();
    }
} else {
    // Not a POST request
    header("Location: /admin/admin_login.php");
    exit();
}
?>