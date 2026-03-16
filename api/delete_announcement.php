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

if (!$input || !isset($input['id'])) {
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

    // Find and remove the announcement
    $foundIndex = -1;
    foreach ($leftPanelData['announcements'] as $index => $announcement) {
        if ($announcement['id'] === $input['id']) {
            $foundIndex = $index;
            break;
        }
    }

    if ($foundIndex === -1) {
        http_response_code(404);
        echo json_encode(['error' => 'Announcement not found']);
        exit;
    }

    // Remove the announcement
    array_splice($leftPanelData['announcements'], $foundIndex, 1);

    // Save updated data
    $result = file_put_contents('../data/left_panel.json', json_encode($leftPanelData, JSON_PRETTY_PRINT));

    if ($result === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete announcement']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Announcement deleted successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>