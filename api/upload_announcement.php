<?php
/**
 * Upload Announcement - Supabase Storage Version
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/database.php';

// Supabase Configuration from Environment
$supabaseUrl = getenv('SUPABASE_URL');
$serviceRoleKey = getenv('SUPABASE_SERVICE_ROLE_KEY');
$bucketName = 'fcl_assets'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $title = $_POST['title'] ?? 'Untitled';
    $subtitle = $_POST['subtitle'] ?? '';
    $id = !empty($_POST['id']) ? $_POST['id'] : ('announcement_' . time());
    $enabled = isset($_POST['enabled']) && ($_POST['enabled'] == '1' || $_POST['enabled'] == 'true');
    $imageUrl = $_POST['existing_image'] ?? '';

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = $id . '_' . basename($file['name']);
        
        $uploadSuccess = false;
        $supabaseError = '';
        $localError = '';

        // 1. Try Uploading to Supabase Storage
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                $imageUrl = "$supabaseUrl/storage/v1/object/public/$bucketName/$fileName";
                $uploadSuccess = true;
            } else {
                $supabaseError = "Supabase HTTP $httpCode: " . ($curlError ?: $response);
            }
        } else {
            $supabaseError = 'SUPABASE_URL or SUPABASE_SERVICE_ROLE_KEY not configured in environment.';
        }

        // 2. Fallback to local storage if Supabase failed or is not configured
        if (!$uploadSuccess) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                $imageUrl = 'uploads/' . $fileName;
                $uploadSuccess = true;
            } else {
                $localError = 'move_uploaded_file() failed (filesystem may be read-only on Vercel).';
            }
        }

        if (!$uploadSuccess) {
            throw new Exception(
                "Upload failed. Supabase: [$supabaseError] | Local: [$localError]"
            );
        }
    }

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
        'message' => 'Announcement saved successfully',
        'imageUrl' => $imageUrl
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>