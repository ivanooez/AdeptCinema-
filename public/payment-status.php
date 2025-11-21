<?php
require_once __DIR__ . '/../common/config.php';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location:/login.php'); exit; }
$payment_id = isset($_GET['payment_id']) ? (int)$_GET['payment_id'] : 0;

$payment = null;
if ($payment_id) {
    $payment = $conn->query("SELECT p.*, pl.name as plan_name FROM payments p LEFT JOIN plans pl ON p.plan_id = pl.id WHERE p.id = $payment_id AND p.user_id = " . (int)$_SESSION['user_id'] . " LIMIT 1")->fetch_assoc();
}

$page_title = 'Payment Status';
require_once __DIR__ . '/../common/header.php';
?>
<section class="py-8">
  <h1 class="text-2xl font-bold mb-4">Payment Status</h1>
  <?php if ($payment): ?>
    <div class="bg-gray-800 p-4 rounded">
      <p>Payment ID: #<?php echo (int)$payment['id']; ?></p>
      <p>Plan: <?php echo htmlspecialchars($payment['plan_name']); ?></p>
      <p>Status: <?php echo htmlspecialchars($payment['status']); ?></p>
      <p class="mt-3">Notes: <?php echo htmlspecialchars($payment['admin_notes'] ?: '-'); ?></p>
    </div>
  <?php else: ?>
    <p class="text-gray-400">No payment found.</p>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/../common/bottom.php'; ?>
