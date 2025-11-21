<?php
require_once __DIR__ . '/../common/config.php';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location:/login.php'); exit; }

$plan_id = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 0;
$plan = null;
if ($plan_id) {
    $plan = $conn->query("SELECT * FROM plans WHERE id = $plan_id LIMIT 1")->fetch_assoc();
}
if (!$plan) { header('Location:/plans.php'); exit; }

$page_title = 'Checkout';
require_once __DIR__ . '/../common/header.php';
?>
<section class="py-8 max-w-xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Checkout - <?php echo htmlspecialchars($plan['name']); ?></h1>
  <p class="mb-4">Price: €<?php echo number_format($plan['price'],2); ?></p>

  <form id="payForm" method="POST" action="/api/create-payment.php" enctype="multipart/form-data">
    <input type="hidden" name="plan_id" value="<?php echo (int)$plan['id']; ?>">
    <div class="mb-4">
      <label class="block mb-2">Payment method</label>
      <select name="payment_method" class="w-full bg-gray-700 p-2 rounded">
        <option value="revolut">Revolut Link</option>
        <option value="card">Card / Bank (upload screenshot)</option>
      </select>
    </div>

    <div class="mb-4">
      <label class="block mb-2">Screenshot (if card)</label>
      <input type="file" name="screenshot" class="w-full" />
    </div>

    <button class="bg-cyan-500 px-4 py-2 rounded">Submit Payment</button>
  </form>
</section>
<?php require_once __DIR__ . '/../common/bottom.php'; ?>
