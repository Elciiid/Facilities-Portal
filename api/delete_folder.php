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

if (!$input || empty($input['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Folder name is required']);
    exit;
}

try {
    $folderName = trim($input['name']);

    // 1. Remove from folders.json
    $foldersFile = '../data/folders.json';
    if (file_exists($foldersFile)) {
        $folders = json_decode(file_get_contents($foldersFile), true);
        if ($folders) {
            $folders = array_values(array_filter($folders, function ($f) use ($folderName) {
                return strcasecmp($f['name'], $folderName) !== 0;
            }));
            file_put_contents($foldersFile, json_encode($folders, JSON_PRETTY_PRINT));
        }
    }

    // 2. Clear folder from apps.json
    $appsFile = '../data/apps.json';
    if (file_exists($appsFile)) {
        $apps = json_decode(file_get_contents($appsFile), true);
        if ($apps) {
            $updated = false;
            foreach ($apps as &$app) {
                if (isset($app['folder']) && strcasecmp($app['folder'], $folderName) === 0) {
                    $app['folder'] = ''; // Clear folder assignment
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