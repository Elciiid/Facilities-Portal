<?php
/**
 * Admin Logout - Centralized Session Handling
 */
require_once __DIR__ . '/../connection/database.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session in the database
session_destroy();

// Redirect to login page
header("Location: /admin/admin_login.php");
exit();
?>