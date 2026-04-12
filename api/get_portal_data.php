<?php
/**
 * Get Portal Data - Consolidated API
 * Fetches all necessary data from PostgreSQL for the main portal display.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/database.php';

try {
    // 1. Fetch Announcement
    $stmt = $conn->query("SELECT * FROM fcl_announcements WHERE active = true LIMIT 1");
    $announcement = $stmt->fetch();

    // 2. Fetch Apps
    $stmt = $conn->query("SELECT * FROM fcl_apps WHERE enabled = true ORDER BY \"order\" ASC");
    $apps = $stmt->fetchAll();

    // 3. Fetch Folders
    $stmt = $conn->query("SELECT * FROM fcl_folders WHERE enabled = true ORDER BY \"order\" ASC");
    $folders = $stmt->fetchAll();

    // 4. Fetch carousel announcements
    $stmt = $conn->query("SELECT * FROM fcl_carousel_announcements WHERE enabled = true ORDER BY \"order\" ASC");
    $carousel = $stmt->fetchAll();

    // 5. Fetch Settings
    $stmt = $conn->query("SELECT * FROM fcl_portal_settings");
    $settingsRaw = $stmt->fetchAll();
    $settings = [];
    foreach ($settingsRaw as $s) {
        $settings[$s['key']] = json_decode($s['value'], true);
    }

    // Default left panel data structure to maintain compatibility
    $leftPanel = [
        'weather_enabled' => $settings['weather']['enabled'] ?? true,
        'background_enabled' => $settings['background']['enabled'] ?? true,
        'announcements' => $carousel,
        'logos' => []
    ];

    echo json_encode([
        'announcement' => $announcement,
        'apps' => $apps,
        'folders' => $folders,
        'left_panel' => $leftPanel
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
