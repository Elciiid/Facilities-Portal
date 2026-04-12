<?php
/**
 * Delete Folder - Database Version
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Folder name is required']);
    exit;
}

try {
    // 1. Unset folder from all apps in this folder
    $stmt = $conn->prepare("UPDATE fcl_apps SET folder_name = NULL WHERE folder_name = ?");
    $stmt->execute([$input['name']]);

    // 2. Delete the folder
    $stmt = $conn->prepare("DELETE FROM fcl_folders WHERE name = ?");
    $stmt->execute([$input['name']]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Folder not found']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Folder deleted successfully from database']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>