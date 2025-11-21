<?php
// subtitle_proxy.php - serves .vtt or converts .srt to .vtt
require_once __DIR__ . '/../common/config.php';

$path = $_GET['path'] ?? '';
if (empty($path)) {
    http_response_code(400);
    echo "Missing path";
    exit;
}

// allow external full URLs
if (preg_match('/^https?:\\/\\//', $path)) {
    $ch = curl_init($path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'AdeptCinema/1.0'
    ]);
    $body = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    if (!$body) { http_response_code(502); echo "Upstream fetch failed"; exit; }
    $ext = pathinfo(parse_url($path, PHP_URL_PATH), PATHINFO_EXTENSION);
    if (strtolower($ext) === 'srt' || stripos($info['content_type'] ?? '', 'subrip') !== false) {
        header('Content-Type: text/vtt');
        echo "WEBVTT\n\n";
        echo preg_replace('/(\\d{2}:\\d{2}:\\d{2}),(\d{3})/', '$1.$2', $body);
        exit;
    } else {
        header('Content-Type: ' . ($info['content_type'] ?? 'text/vtt'));
        echo $body;
        exit;
    }
}

// local file - ensure inside uploads/subtitles
$base = realpath(__DIR__ . '/../uploads/subtitles');
$requested = realpath(__DIR__ . '/../' . ltrim($path, '/'));
if (!$requested || strpos($requested, $base) !== 0 || !file_exists($requested)) {
    http_response_code(404);
    echo "Not found";
    exit;
}
$ext = strtolower(pathinfo($requested, PATHINFO_EXTENSION));
if ($ext === 'vtt') {
    header('Content-Type: text/vtt');
    readfile($requested);
    exit;
} elseif ($ext === 'srt') {
    header('Content-Type: text/vtt');
    echo "WEBVTT\n\n";
    $content = file_get_contents($requested);
    echo preg_replace('/(\\d{2}:\\d{2}:\\d{2}),(\d{3})/', '$1.$2', $content);
    exit;
}
http_response_code(400);
echo "Unsupported format";
exit;
?>
