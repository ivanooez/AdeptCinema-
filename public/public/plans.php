<?php
require_once __DIR__ . '/../common/config.php';
session_start();
$plans = $conn->query("SELECT * FROM plans WHERE is_active = TRUE")->fetch_all(MYSQLI_ASSOC);
$page_title = 'Plans';
require_once __DIR__ . '/../common/header.php';
?>
<section class="py-8">
  <h1 class="text-2xl font-bold mb-4">Choose a Plan</h1>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <?php foreach ($plans as $p): ?>
      <div class="bg-gray-800 p-4 rounded">
        <h3 class="font-bold"><?php echo htmlspecialchars($p['name']); ?></h3>
        <p class="text-gray-400"><?php echo htmlspecialchars($p['description']); ?></p>
        <div class="mt-4">
          <span class="text-2xl font-bold text-cyan-400">€<?php echo number_format($p['price'],2); ?></span>
        </div>
        <div class="mt-4">
          <a href="/checkout.php?plan_id=<?php echo (int)$p['id']; ?>" class="bg-cyan-500 px-3 py-2 rounded">Select</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once __DIR__ . '/../common/bottom.php'; ?>
