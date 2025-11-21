<?php
// common/config.php
// Loads .env (if present) and opens DB connection

$baseDir = realpath(__DIR__ . '/..');

$envPath = $baseDir . '/.env';
$env = [];
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $val] = array_map('trim', explode('=', $line, 2) + [1 => '']);
        $env[$key] = $val;
    }
} else {
    // fallback to example values
    $envDefaults = parse_ini_file($baseDir . '/.env.example');
    $env = $envDefaults ?: [];
}

define('DB_HOST', $env['DB_HOST'] ?? '127.0.0.1');
define('DB_PORT', $env['DB_PORT'] ?? '3306');
define('DB_NAME', $env['DB_DATABASE'] ?? 'adept_cinema');
define('DB_USER', $env['DB_USERNAME'] ?? 'root');
define('DB_PASS', $env['DB_PASSWORD'] ?? 'secret');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);
if ($mysqli->connect_error) {
    die("Database Connection Failed: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

define('APP_NAME', $env['APP_NAME'] ?? 'Adept Cinema');
define('APP_URL', $env['APP_URL'] ?? 'http://localhost:8080');

define('UPLOADS_DIR', $baseDir . '/uploads/');
define('UPLOADS_WEB', '/uploads/');

// ensure upload dirs exist
$subdirs = ['banners','payments','subtitles','posters'];
foreach ($subdirs as $d) {
    $dir = UPLOADS_DIR . $d;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}

// short alias
$conn = $mysqli;
?>
