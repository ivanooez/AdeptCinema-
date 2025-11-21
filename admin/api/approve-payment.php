<?php
// admin/api/approve-payment.php
require_once __DIR__ . '/../../common/config.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$input = json_decode(file_get_contents('php://input'), true);
$payment_id = (int)($input['payment_id'] ?? 0);
if (!$payment_id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Missing payment_id']); exit; }

try {
    $conn->begin_transaction();

    $pay = $conn->query("SELECT * FROM payments WHERE id = $payment_id FOR UPDATE")->fetch_assoc();
    if (!$pay) throw new Exception('Payment not found');
    if ($pay['status'] !== 'pending') throw new Exception('Payment not pending');

    // update payment status
    $stmt = $conn->prepare("UPDATE payments SET status='approved', approved_by=?, approved_at=NOW() WHERE id=?");
    $admin_id = (int)$_SESSION['admin_id'];
    $stmt->bind_param('ii', $admin_id, $payment_id);
    $stmt->execute();
    $stmt->close();

    // create or extend subscription: create subscription row and set users.subscription_end
    $duration = 30; // default duration days (could fetch plan)
    $plan = $conn->query("SELECT * FROM plans WHERE id = " . (int)$pay['plan_id'] . " LIMIT 1")->fetch_assoc();
    if ($plan && !empty($plan['duration_days'])) $duration = (int)$plan['duration_days'];

    // set user's subscription_end
    $user_id = (int)$pay['user_id'];
    $current_end = $conn->query("SELECT subscription_end FROM users WHERE id = $user_id")->fetch_assoc()['subscription_end'];
    $start_ts = time();
    if ($current_end && strtotime($current_end) > time()) $start_ts = strtotime($current_end);
    $new_end = date('Y-m-d H:i:s', strtotime("+$duration days", $start_ts));

    $stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan_id, status, start_date, end_date, auto_renew, created_at) VALUES (?, ?, 'active', NOW(), ?, 1, NOW()) ON DUPLICATE KEY UPDATE end_date = VALUES(end_date), status='active'");
    $stmt->bind_param('iis', $user_id, $pay['plan_id'], $new_end);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE users SET subscription_end = ? WHERE id = ?");
    $stmt->bind_param('si', $new_end, $user_id);
    $stmt->execute();
    $stmt->close();

    // commit
    $conn->commit();
    echo json_encode(['success'=>true,'message'=>'Payment approved']);
    exit;
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
    exit;
}
?>
