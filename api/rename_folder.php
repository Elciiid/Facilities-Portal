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

if (!$input || empty($input['name']) || empty($input['original_name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Folder name and original name are required']);
    exit;
}

try {
    $newName = trim($input['name']);
    $originalName = trim($input['original_name']);
    $enabled = $input['enabled'] ?? true;

    // 1. Update folders.json
    $foldersFile = '../data/folders.json';
    if (file_exists($foldersFile)) {
        $folders = json_decode(file_get_contents($foldersFile), true);
        if ($folders) {
            $found = false;
            foreach ($folders as &$folder) {
                if (strcasecmp($folder['name'], $originalName) === 0) {
                    $folder['name'] = $newName;
                    $folder['enabled'] = $enabled;
                    $found = true;
                    // Keep order
                }
            }
            if (!$found) {
                // Might be a folder that existed implicitly via apps but not in folders.json
                $folders[] = [
                    'name' => $newName,
                    'enabled' => $enabled,
                    'order' => count($folders) + 1
                ];
            }
            file_put_contents($foldersFile, json_encode($folders, JSON_PRETTY_PRINT));
        }
    } else {
        // Create new
        $folders = [
            [
                'name' => $newName,
                'enabled' => $enabled,
                'order' => 1
            ]
        ];
        file_put_contents($foldersFile, json_encode($folders, JSON_PRETTY_PRINT));
    }

    // 2. Update apps.json (Renaming all apps in this folder)
    $appsFile = '../data/apps.json';
    if (file_exists($appsFile)) {
        $apps = json_decode(file_get_contents($appsFile), true);
        if ($apps) {
            $updated = false;
            foreach ($apps as &$app) {
                if (isset($app['folder']) && strcasecmp($app['folder'], $originalName) === 0) {
                    $app['folder'] = $newName;
                    $updated = true;
                }
            }
            if ($updated) {
                file_put_contents($appsFile, json_encode($apps, JSON_PRETTY_PRINT));
            }
        }
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>