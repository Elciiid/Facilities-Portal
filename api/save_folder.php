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
    $foldersFile = '../data/folders.json';
    $folders = [];

    if (file_exists($foldersFile)) {
        $folders = json_decode(file_get_contents($foldersFile), true);
        if (!$folders)
            $folders = [];
    }

    $folderName = trim($input['name']);
    $enabled = $input['enabled'] ?? true;

    $foundIndex = -1;
    foreach ($folders as $index => $folder) {
        if (strcasecmp($folder['name'], $folderName) === 0) {
            $foundIndex = $index;
            break;
        }
    }

    if ($foundIndex >= 0) {
        // Update existing
        $folders[$foundIndex]['enabled'] = $enabled;
    } else {
        // Add new
        $folders[] = [
            'name' => $folderName,
            'enabled' => $enabled,
            'order' => count($folders) + 1
        ];
    }

    if (file_put_contents($foldersFile, json_encode($folders, JSON_PRETTY_PRINT))) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to write to file');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>