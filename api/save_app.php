<?php
/**
 * Save App - Database Version
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/database.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

$id = $input['id'] ?? '';
$isToggle = isset($input['isToggle']) && $input['isToggle'];

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['error' => 'App ID is required']);
    exit;
}

try {
    if ($isToggle) {
        // Toggle operation
        $stmt = $conn->prepare("UPDATE fcl_apps SET enabled = ? WHERE id = ?");
        $stmt->execute([
            isset($input['enabled']) ? (bool)$input['enabled'] : false,
            $id
        ]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'App not found']);
            exit;
        }
    } else {
        // Full save (Insert or Update)
        // Ensure folder exists or set to NULL
        $folder = !empty($input['folder']) ? $input['folder'] : null;
        if ($folder) {
            $stmt = $conn->prepare("SELECT name FROM fcl_folders WHERE name = ?");
            $stmt->execute([$folder]);
            if (!$stmt->fetch()) {
                // If folder doesn't exist, create it if you want, or just set to null
                $folder = null; 
            }
        }

        // Calculate order if not provided
        $order = isset($input['order']) && $input['order'] !== '' ? (int)$input['order'] : null;
        if ($order === null) {
            $stmt = $conn->query("SELECT MAX(\"order\") FROM fcl_apps");
            $maxOrder = $stmt->fetchColumn();
            $order = ($maxOrder !== null) ? $maxOrder + 1 : 1;
        }

        $query = "INSERT INTO fcl_apps (id, title, description, icon, color, link, folder_name, enabled, \"order\")
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                  ON CONFLICT (id) DO UPDATE SET 
                    title = EXCLUDED.title,
                    description = EXCLUDED.description,
                    icon = EXCLUDED.icon,
                    color = EXCLUDED.color,
                    link = EXCLUDED.link,
                    folder_name = EXCLUDED.folder_name,
                    enabled = EXCLUDED.enabled,
                    \"order\" = EXCLUDED.\"order\"";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $id,
            trim($input['title'] ?? ''),
            trim($input['description'] ?? ''),
            trim($input['icon'] ?? ''),
            $input['color'] ?? 'slate',
            trim($input['link'] ?? ''),
            $folder,
            isset($input['enabled']) ? (bool)$input['enabled'] : true,
            $order
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'App saved successfully to database']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>