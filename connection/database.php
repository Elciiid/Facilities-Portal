<?php
/**
 * Database Connection & Session Management
 * Centralized file for connecting to Supabase PostgreSQL and handling stateless sessions.
 */

// 1. Timezone & Error Reporting
date_default_timezone_set('Asia/Manila');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED); 
ini_set('display_errors', 0); // Disable in production

// 2. Load Local .env (for localhost testing)
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

// 3. Database Credentials
$databaseUrl = getenv('DATABASE_URL');

if (!$databaseUrl) {
    die("<div style=\"font-family:sans-serif;padding:50px;text-align:center;\">
        <h2>Configuration Error</h2>
        <p>DATABASE_URL environment variable is not set.</p>
    </div>");
}

// Clean the string
$databaseUrl = trim($databaseUrl);
if (strpos($databaseUrl, 'DATABASE_URL=') === 0) {
    $databaseUrl = substr($databaseUrl, 13);
}

// 4. Parse Connection String
$user = $pass = $host = $dbname = null;
$port = 6543; // Transaction Pooler port

if (preg_match('/^postgres(?:ql)?:\/\/([^:]+):(.*)@([^:\/]+)(?::(\d+))?\/(.+)$/', $databaseUrl, $m)) {
    $user   = $m[1];
    $pass   = $m[2];
    $host   = $m[3];
    $port   = $m[4] ?: 6543;
    $dbname = explode('?', $m[5])[0];
} else {
    $parsed = parse_url($databaseUrl);
    $user   = $parsed['user'] ?? null;
    $pass   = $parsed['pass'] ?? null;
    $host   = $parsed['host'] ?? null;
    $port   = $parsed['port'] ?? 6543;
    $dbname = explode('?', ltrim($parsed['path'] ?? '', '/'))[0];
}

// 5. Establish PDO Connection
try {
    $dsn  = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 6. Register Session Handler
    require_once __DIR__ . '/../utils/session_handler.php';
    $handler = new PdoSessionHandler($conn);
    session_set_save_handler($handler, true);

    // 7. Start Session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

} catch (PDOException $e) {
    error_log("DB Connection Error: " . $e->getMessage());
    die("<div style=\"font-family:sans-serif;padding:50px;text-align:center;\">
        <h2>System Unavailable</h2>
        <p>Database connection failed. Please contact your administrator.</p>
    </div>");
}
