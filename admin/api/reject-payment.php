<?php
// admin/api/reject-payment.php
require_once __DIR__ . '/../../common/config.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$input = json_decode(file_get_contents('php://input'), true);
$payment_id = (int)($input['payment_id'] ?? 0);
$reason = trim($input['reason'] ?? 'Rejected by admin');

if (!$payment_id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Missing payment_id']); exit; }

try {
    $stmt = $conn->prepare("UPDATE payments SET status='rejected', admin_notes=?, approved_by=?, approved_at=NOW() WHERE id=? AND status='pending'");
    $admin_id = (int)$_SESSION['admin_id'];
    $stmt->bind_param('sii', $reason, $admin_id, $payment_id);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        echo json_encode(['success'=>false,'error'=>'No pending payment updated']);
    } else {
        echo json_encode(['success'=>true,'message'=>'Payment rejected']);
    }
    $stmt->close();
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
    exit;
}
?>
