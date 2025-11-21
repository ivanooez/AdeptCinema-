<?php
require_once __DIR__ . '/../common/config.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }
$uid = (int)$_SESSION['user_id'];
$res = $conn->query("SELECT id,type,title,message,action_url,is_read,created_at FROM notifications WHERE user_id = $uid ORDER BY created_at DESC LIMIT 50");
$notes = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
echo json_encode(['success'=>true,'notifications'=>$notes]);
exit;
?>
