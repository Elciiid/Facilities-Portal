<?php
/**
 * Save Weather Toggle - Database Version
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$enabled = isset($input['enabled']) ? (bool)$input['enabled'] : true;

try {
    $stmt = $conn->prepare("INSERT INTO fcl_portal_settings (key, value)
                            VALUES ('weather', ?)
                            ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value");
    $stmt->execute([json_encode(['enabled' => $enabled])]);

    echo json_encode(['success' => true, 'message' => 'Weather setting saved to database']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>