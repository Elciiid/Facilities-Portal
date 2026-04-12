<?php
/**
 * Save Setting - Database Version
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

try {
    foreach ($input as $key => $value) {
        $settingKey = '';
        if ($key === 'weather_enabled') $settingKey = 'weather';
        if ($key === 'background_enabled') $settingKey = 'background';
        
        if ($settingKey) {
            $stmt = $conn->prepare("INSERT INTO fcl_portal_settings (key, value)
                                    VALUES (?, ?)
                                    ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value");
            $stmt->execute([$settingKey, json_encode(['enabled' => (bool)$value])]);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Settings saved to database']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>