<?php
/**
 * Toggle Announcement - Database Version
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Announcement ID is required']);
    exit;
}

try {
    $id = $input['id'];
    // Route to the correct table: explicit 'carousel' target OR legacy id prefix
    $table = (isset($input['target']) && $input['target'] === 'carousel')
             ? 'fcl_carousel_announcements'
             : 'fcl_announcements';

    $column = ($table === 'fcl_announcements') ? 'active' : 'enabled';

    // Cast to int (1/0) for reliable PostgreSQL boolean binding via PDO
    $value = (int)(bool)($input['enabled'] ?? $input['active'] ?? false);

    $stmt = $conn->prepare("UPDATE $table SET $column = ? WHERE id = ?");
    $stmt->execute([$value, $id]);

    echo json_encode(['success' => true, 'message' => "Announcement visibility toggled in $table"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>