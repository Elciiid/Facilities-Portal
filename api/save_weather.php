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

if (!$input) {
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

    // Update settings if provided
    if (isset($input['weather_enabled'])) {
        $leftPanelData['weather_enabled'] = (bool) $input['weather_enabled'];
    }
    if (isset($input['background_enabled'])) {
        $leftPanelData['background_enabled'] = (bool) $input['background_enabled'];
    }

    // Save updated data
    $result = file_put_contents('../data/left_panel.json', json_encode($leftPanelData, JSON_PRETTY_PRINT));

    if ($result === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save weather settings']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Weather settings saved successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>