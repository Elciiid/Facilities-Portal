<?php
/**
 * Get Admin Data - Consolidated API
 * Fetches all apps, folders, and settings for the Admin Dashboard.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/database.php';

// Security check: Ensure user is admin
if (!isset($_SESSION['admin_authenticated']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

try {
    // 1. Fetch Main Announcement (Banner)
    $stmt = $conn->query("SELECT * FROM fcl_announcements WHERE type = 'info' OR type IS NULL LIMIT 1");
    $announcement = $stmt->fetch();

    // 2. Fetch All Apps (sorted by order)
    $stmt = $conn->query("SELECT * FROM fcl_apps ORDER BY \"order\" ASC");
    $apps = $stmt->fetchAll();

    // 3. Fetch All Folders
    $stmt = $conn->query("SELECT * FROM fcl_folders ORDER BY \"order\" ASC");
    $folders = $stmt->fetchAll();

    // 4. Fetch Carousel Announcements (Image/Panel Announcements)
    $stmt = $conn->query("SELECT * FROM fcl_carousel_announcements ORDER BY created_at DESC");
    $carouselAnnouncements = $stmt->fetchAll();

    // 5. Fetch Settings
    $stmt = $conn->query("SELECT * FROM fcl_portal_settings");
    $settingsRaw = $stmt->fetchAll();
    $settings = [];
    foreach ($settingsRaw as $s) {
        $settings[$s['key']] = json_decode($s['value'], true);
    }

    echo json_encode([
        'announcement' => $announcement,
        'apps' => $apps,
        'folders' => $folders,
        'carousel_announcements' => $carouselAnnouncements,
        'settings' => $settings,
        'success' => true
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
