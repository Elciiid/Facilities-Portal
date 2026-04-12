<?php
/**
 * Upload Announcement - Supabase Storage Version
 * Handles image uploads and metadata persistence in PostgreSQL.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/database.php';

// Supabase Configuration from Environment
$supabaseUrl = getenv('SUPABASE_URL');
$serviceRoleKey = getenv('SUPABASE_SERVICE_ROLE_KEY');
$bucketName = 'fcl_assets'; // We'll assume this bucket exists or needs to be created

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $title = $_POST['title'] ?? 'Untitled';
    $subtitle = $_POST['subtitle'] ?? '';
    $id = $_POST['id'] ?? ('announcement_' . time());
    $imageUrl = '';

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = $id . '_' . basename($file['name']);
        
        // 1. Upload to Supabase Storage via REST API
        if ($supabaseUrl && $serviceRoleKey) {
            $uploadUrl = "$supabaseUrl/storage/v1/object/$bucketName/$fileName";
            
            $ch = curl_init($uploadUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file['tmp_name']));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $serviceRoleKey",
                "Content-Type: " . $file['type'],
                "x-upsert: true"
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                // Successfully uploaded to Supabase Storage
                // The URL is usually: {supabase_url}/storage/v1/object/public/{bucket}/{name}
                $imageUrl = "$supabaseUrl/storage/v1/object/public/$bucketName/$fileName";
            } else {
                error_log("Supabase Upload Error ($httpCode): " . $response);
                // Fallback to local (ephemeral) for demo purposes if storage fails
                $uploadDir = '../uploads/';
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
    } else {
        // Use existing image if provided
        $imageUrl = $_POST['existing_image'] ?? '';
    }

    $enabled = isset($_POST['enabled']) && $_POST['enabled'] == '1';

    // 2. Persist Metadata in fcl_carousel_announcements
    $query = "INSERT INTO fcl_carousel_announcements (id, title, subtitle, image_url, enabled)
              VALUES (?, ?, ?, ?, ?)
              ON CONFLICT (id) DO UPDATE SET 
                title = EXCLUDED.title,
                subtitle = EXCLUDED.subtitle,
                image_url = EXCLUDED.image_url,
                enabled = EXCLUDED.enabled";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$id, $title, $subtitle, $imageUrl, $enabled]);

    echo json_encode([
        'success' => true, 
        'message' => 'Announcement uploaded and saved to database',
        'imageUrl' => $imageUrl
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>