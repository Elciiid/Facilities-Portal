<?php
/**
 * Save Announcement - Database Version
 * Replaces JSON-based storage with Supabase PostgreSQL.
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

try {
    // For now, we only have one announcement (top banner)
    // We'll update the first one or create it if missing
    $query = "INSERT INTO fcl_announcements (id, active, title, message, updated_at)
              VALUES (1, ?, ?, ?, NOW())
              ON CONFLICT (id) DO UPDATE SET 
                active = EXCLUDED.active,
                title = EXCLUDED.title,
                message = EXCLUDED.message,
                updated_at = NOW()";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([
        $input['active'] ?? false,
        trim($input['title'] ?? ''),
        trim($input['message'] ?? '')
    ]);

    echo json_encode(['success' => true, 'message' => 'Announcement saved successfully to database']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>