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
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

// Check if this is a toggle operation (only updating enabled state)
$isToggle = isset($input['isToggle']) && $input['isToggle'];

if ($isToggle) {
    // For toggle operations, only id and enabled are required
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'App ID is required']);
        exit;
    }
} else {
    // For full save operations, all fields are required
    $requiredFields = ['id', 'title', 'description', 'icon', 'color', 'link'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => ucfirst($field) . ' is required']);
            exit;
        }
    }
}

try {
    // Load current apps data
    $apps = json_decode(file_get_contents('../data/apps.json'), true);

    if (!$apps) {
        $apps = [];
    }

    // Find existing app or add new one
    $foundIndex = -1;
    foreach ($apps as $index => $app) {
        if ($app['id'] === $input['id']) {
            $foundIndex = $index;
            break;
        }
    }

    // Calculate order - if not provided or if it's a new app, use next available number
    $order = isset($input['order']) && $input['order'] !== '' ? (int) $input['order'] : null;
    if ($order === null || $foundIndex < 0) {
        // Find the highest order number and add 1
        $maxOrder = 0;
        foreach ($apps as $app) {
            if ($app['order'] > $maxOrder) {
                $maxOrder = $app['order'];
            }
        }
        $order = $maxOrder + 1;
    }

    if ($foundIndex >= 0) {
        if ($isToggle) {
            // For toggle operations, only update the enabled field
            $apps[$foundIndex]['enabled'] = $input['enabled'] ?? false;
        } else {
            // Update existing app with full data
            $appData = [
                'id' => $input['id'],
                'title' => trim($input['title']),
                'folder' => trim($input['folder'] ?? ''),
                'description' => trim($input['description']),
                'icon' => trim($input['icon']),
                'color' => $input['color'],
                'link' => trim($input['link']),
                'enabled' => $input['enabled'] ?? true,
                'order' => $order
            ];
            $apps[$foundIndex] = $appData;
        }
    } else {
        if ($isToggle) {
            // Cannot toggle an app that doesn't exist
            http_response_code(404);
            echo json_encode(['error' => 'App not found']);
            exit;
        } else {
            // Add new app
            $appData = [
                'id' => $input['id'],
                'title' => trim($input['title']),
                'folder' => trim($input['folder'] ?? ''),
                'description' => trim($input['description']),
                'icon' => trim($input['icon']),
                'color' => $input['color'],
                'link' => trim($input['link']),
                'enabled' => $input['enabled'] ?? true,
                'order' => $order
            ];
            $apps[] = $appData;
        }
    }

    // Sort apps by order
    usort($apps, function ($a, $b) {
        return $a['order'] <=> $b['order'];
    });

    // Save updated apps data
    $result = file_put_contents('../data/apps.json', json_encode($apps, JSON_PRETTY_PRINT));

    if ($result === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save app']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'App saved successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>