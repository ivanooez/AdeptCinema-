<?php
// simple auth & subscription middleware
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if (!isset($conn)) {
    require_once __DIR__ . '/config.php';
}

$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role, subscription_end FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $userId);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    session_destroy();
    header('Location: /login.php');
    exit;
}

// Admins always allowed
if ($data['role'] === 'admin') {
    $GLOBALS['user_role'] = 'admin';
    $GLOBALS['user_subscription_active'] = true;
    return;
}

$subActive = false;
if (!empty($data['subscription_end']) && strtotime($data['subscription_end']) > time()) {
    $subActive = true;
}

$GLOBALS['user_role'] = $data['role'];
$GLOBALS['user_subscription_active'] = $subActive;

if (!$subActive) {
    header('Location: /subscription.php');
    exit;
}
?>
