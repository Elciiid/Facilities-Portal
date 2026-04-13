<?php
/**
 * Delete Announcement - Database Version
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
    $stmt = $conn->prepare("DELETE FROM fcl_carousel_announcements WHERE id = ?");
    $stmt->execute([$input['id']]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Announcement not found']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Announcement deleted from database']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>