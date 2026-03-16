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

// Create uploads directory if it doesn't exist
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle file upload
$uploadedFile = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $fileType = $_FILES['image']['type'];

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
        exit;
    }

    // Validate file size (5MB max)
    if ($fileSize > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => 'File size too large. Maximum 5MB allowed.']);
        exit;
    }

    // Generate unique filename
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = 'announcement_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        $uploadedFile = $newFileName;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save uploaded file']);
        exit;
    }
}

$input = $_POST;

// Validate required fields
if (empty($input['title'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Title is required']);
    exit;
}

try {
    // Load current left panel data
    $leftPanelData = json_decode(file_get_contents('../data/left_panel.json'), true);

    if (!$leftPanelData) {
        $leftPanelData = ['weather_enabled' => true, 'announcements' => [], 'logos' => []];
    }

    $announcementData = [
        'id' => $input['id'] ?: ('announcement_' . time() . '_' . uniqid()),
        'type' => $input['type'] ?: 'image',
        'title' => trim($input['title']),
        'subtitle' => trim($input['subtitle'] ?? ''),
        'image' => $uploadedFile ?: $input['existing_image'] ?? '',
        'enabled' => isset($input['enabled']) ? (bool) $input['enabled'] : true
    ];

    // Find existing announcement or add new one
    $foundIndex = -1;
    foreach ($leftPanelData['announcements'] as $index => $announcement) {
        if ($announcement['id'] === $announcementData['id']) {
            $foundIndex = $index;
            break;
        }
    }

    if ($foundIndex >= 0) {
        // Update existing announcement
        $leftPanelData['announcements'][$foundIndex] = $announcementData;
    } else {
        // Add new announcement
        $leftPanelData['announcements'][] = $announcementData;
    }

    // Save updated data
    $result = file_put_contents('../data/left_panel.json', json_encode($leftPanelData, JSON_PRETTY_PRINT));

    if ($result === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save announcement']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Announcement saved successfully',
        'image' => $uploadedFile
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>