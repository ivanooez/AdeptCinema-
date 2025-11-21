<?php
require_once __DIR__ . '/../common/config.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$method = $_POST['payment_method'] ?? $_REQUEST['payment_method'] ?? null;
$plan_id = (int)($_POST['plan_id'] ?? $_REQUEST['plan_id'] ?? 0);
if (!$method || !$plan_id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Invalid']); exit; }

$plan = $conn->query("SELECT * FROM plans WHERE id = $plan_id LIMIT 1")->fetch_assoc();
if (!$plan) { http_response_code(404); echo json_encode(['success'=>false,'error'=>'Plan not found']); exit; }

$user_id = (int)$_SESSION['user_id'];

if ($method === 'revolut') {
    // Create pending payment record
    $stmt = $conn->prepare("INSERT INTO payments (user_id, plan_id, amount, payment_method, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
    $amount = $plan['price'];
    $pm = 'revolut';
    $stmt->bind_param('iid s', $user_id, $plan_id, $amount, $pm);
    // bind_param above had issues with types; use this safe variant:
    $stmt->close();
    $q = $conn->prepare("INSERT INTO payments (user_id, plan_id, amount, payment_method, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
    $q->bind_param('iids', $user_id, $plan_id, $plan['price'], $method);
    $q->execute();
    $pid = $q->insert_id;
    echo json_encode(['success'=>true,'payment_id'=>$pid,'method'=>'revolut','message'=>'Payment initiated. Complete the Revolut payment externally.']);
    exit;
}

if ($method === 'card') {
    if (!isset($_FILES['screenshot'])) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Screenshot required']); exit; }
    $file = $_FILES['screenshot'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = 'pay_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = UPLOADS_DIR . 'payments/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) { http_response_code(500); echo json_encode(['success'=>false,'error'=>'Upload failed']); exit; }
    $path = '/uploads/payments/' . $name;
    $q = $conn->prepare("INSERT INTO payments (user_id, plan_id, amount, payment_method, status, screenshot_path, created_at) VALUES (?, ?, ?, ?, 'pending', ?, NOW())");
    $q->bind_param('i d s s', $user_id, $plan_id, $plan['price'], $method, $path); // types adjusted below
    $q->close();
    $stmt = $conn->prepare("INSERT INTO payments (user_id, plan_id, amount, payment_method, status, screenshot_path, created_at) VALUES (?, ?, ?, ?, 'pending', ?, NOW())");
    $stmt->bind_param('iidss', $user_id, $plan_id, $plan['price'], $method, $path);
    $stmt->execute();
    $pid = $stmt->insert_id;
    echo json_encode(['success'=>true,'payment_id'=>$pid,'method'=>'card','message'=>'Payment submitted for review']);
    exit;
}

http_response_code(400);
echo json_encode(['success'=>false,'error'=>'Unsupported method']);
exit;
?>
