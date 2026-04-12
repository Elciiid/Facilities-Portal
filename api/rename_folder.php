<?php
/**
 * Rename Folder - Database Version
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$oldName = $input['oldName'] ?? '';
$newName = $input['newName'] ?? '';

if (empty($oldName) || empty($newName)) {
    http_response_code(400);
    echo json_encode(['error' => 'Old and new names are required']);
    exit;
}

try {
    $conn->beginTransaction();

    // 1. Create the new folder entry (copying settings)
    $stmt = $conn->prepare("INSERT INTO fcl_folders (name, enabled, \"order\") 
                            SELECT ?, enabled, \"order\" FROM fcl_folders WHERE name = ?");
    $stmt->execute([$newName, $oldName]);

    if ($stmt->rowCount() === 0) {
        $conn->rollBack();
        http_response_code(404);
        echo json_encode(['error' => 'Folder not found']);
        exit;
    }

    // 2. Update all apps to point to the new folder name
    $stmt = $conn->prepare("UPDATE fcl_apps SET folder_name = ? WHERE folder_name = ?");
    $stmt->execute([$newName, $oldName]);

    // 3. Delete the old folder entry
    $stmt = $conn->prepare("DELETE FROM fcl_folders WHERE name = ?");
    $stmt->execute([$oldName]);

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Folder renamed successfully in database']);
} catch (PDOException $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>