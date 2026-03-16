<?php
header('Content-Type: application/json');

// Disable display of errors to prevent JSON corruption
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../data/api_error.log');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id']) || !isset($input['enabled'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    // Load current left panel data
    $leftPanelData = json_decode(file_get_contents('../data/left_panel.json'), true);

    if (!$leftPanelData) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to load current data']);
        exit;
    }

    // Find and update the announcement
    $found = false;
    foreach ($leftPanelData['announcements'] as &$announcement) {
        if ($announcement['id'] === $input['id']) {
            $announcement['enabled'] = (bool) $input['enabled'];
            $found = true;
            break;
        }
    }

    if (!$found) {
        http_response_code(404);
        echo json_encode(['error' => 'Announcement not found']);
        exit;
    }

    // Save updated data
    $result = file_put_contents('../data/left_panel.json', json_encode($leftPanelData, JSON_PRETTY_PRINT));

    if ($result === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update announcement']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Announcement status updated successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>