<?php
require_once __DIR__ . '/../common/config.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }
$pid = isset($_GET['payment_id']) ? (int)$_GET['payment_id'] : 0;
if (!$pid) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Missing payment_id']); exit; }
$pay = $conn->query("SELECT * FROM payments WHERE id = $pid AND user_id = " . (int)$_SESSION['user_id'] . " LIMIT 1")->fetch_assoc();
if (!$pay) { http_response_code(404); echo json_encode(['success'=>false,'error'=>'Not found']); exit; }
echo json_encode(['success'=>true,'status'=>$pay['status'],'payment'=>$pay]);
exit;
?>
