<?php
header('Content-Type: application/json');

// Production settings
// Production settings
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable for production
ini_set('log_errors', 1);
ini_set('error_log', '../data/api_error.log');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Process the request normally

// For toggling active state, we don't require title/message to be filled
// Only require them for full form submission
if (!isset($input['active'])) {
    if (empty($input['title']) || empty($input['message'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and message are required']);
        exit;
    }
}

$data = [
    'active' => $input['active'] ?? false,
    'title' => trim($input['title'] ?? ''),
    'message' => trim($input['message'] ?? ''),
    'created_at' => $input['created_at'] ?? date('c'),
    'updated_at' => $input['updated_at'] ?? date('c')
];

try {
    $result = file_put_contents('../data/announcements.json', json_encode($data, JSON_PRETTY_PRINT));

    if ($result === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save announcement']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Announcement saved successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>