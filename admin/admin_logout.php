<?php
// Start session to ensure we can destroy it
session_start();

// Destroy all session data
session_destroy();

// Clear any session cookies (optional, but good practice)
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to portal instead of login to avoid confusion
header("Location: ../portal.php");
exit();
?>