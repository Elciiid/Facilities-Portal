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
    // Load current apps data
    $appsData = json_decode(file_get_contents('../data/apps.json'), true);

    if (!$appsData) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to load current data']);
        exit;
    }

    // Find and remove the app
    $found = false;
    $appsData = array_filter($appsData, function ($app) use ($input, &$found) {
        if ($app['id'] === $input['id']) {
            $found = true;
            return false; // Remove this app
        }
        return true; // Keep this app
    });

    if (!$found) {
        http_response_code(404);
        echo json_encode(['error' => 'App not found']);
        exit;
    }

    // Re-index the array
    $appsData = array_values($appsData);

    // Save updated data
    $result = file_put_contents('../data/apps.json', json_encode($appsData, JSON_PRETTY_PRINT));

    if ($result === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete app']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'App deleted successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>