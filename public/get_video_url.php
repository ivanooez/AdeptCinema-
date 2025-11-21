<?php
// Returns JSON with video url + type + optional subtitle proxy URL
require_once __DIR__ . '/../common/config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success'=>false,'error'=>'Unauthorized']);
    exit;
}

$movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;
if (!$movie_id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Missing movie_id']); exit; }

$stmt = $conn->prepare("SELECT watch_link, subtitle_url FROM movies WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $movie_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) { http_response_code(404); echo json_encode(['success'=>false,'error'=>'Not found']); exit; }
$video = trim($row['watch_link'] ?? '');
$sub = trim($row['subtitle_url'] ?? '');

if (empty($video)) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'No video configured']); exit; }

$subtitle_proxy = null;
if ($sub) {
    if (preg_match('/^https?:\\/\\/.*\\.vtt($|\\?)/i', $sub)) {
        $subtitle_proxy = $sub;
    } else {
        $subtitle_proxy = '/subtitle_proxy.php?path=' . urlencode($sub);
    }
}

// Google Drive handling
if (strpos($video, 'drive.google.com') !== false) {
    if (preg_match('/\\/d\\/([a-zA-Z0-9_-]+)/', $video, $m)) {
        $file_id = $m[1];
        $direct = 'https://drive.google.com/uc?export=download&id=' . $file_id;
        echo json_encode(['success'=>true,'url'=>$direct,'type'=>'google_drive_direct','subtitle_url'=>$subtitle_proxy]);
        exit;
    } else {
        echo json_encode(['success'=>true,'url'=>$video,'type'=>'google_drive_embed','subtitle_url'=>$subtitle_proxy]);
        exit;
    }
}

echo json_encode(['success'=>true,'url'=>$video,'type'=>'direct','subtitle_url'=>$subtitle_proxy]);
exit;
?>
