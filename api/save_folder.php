<?php
/**
 * Save Folder - Database Version
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
    // Check for toggle or full save
    $isToggle = isset($input['isToggle']) && $input['isToggle'];

    if ($isToggle) {
        $stmt = $conn->prepare("UPDATE fcl_folders SET enabled = ? WHERE name = ?");
        $stmt->execute([
            isset($input['enabled']) ? (bool)$input['enabled'] : false,
            $input['name']
        ]);
    } else {
        // Calculate order if not provided
        $order = isset($input['order']) && $input['order'] !== '' ? (int)$input['order'] : null;
        if ($order === null) {
            $stmt = $conn->query("SELECT MAX(\"order\") FROM fcl_folders");
            $maxOrder = $stmt->fetchColumn();
            $order = ($maxOrder !== null) ? $maxOrder + 1 : 1;
        }

        $query = "INSERT INTO fcl_folders (name, enabled, \"order\")
                  VALUES (?, ?, ?)
                  ON CONFLICT (name) DO UPDATE SET 
                    enabled = EXCLUDED.enabled,
                    \"order\" = EXCLUDED.\"order\"";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            trim($input['name']),
            isset($input['enabled']) ? (bool)$input['enabled'] : true,
            $order
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Folder saved successfully to database']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>