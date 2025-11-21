<?php
// admin/payments.php - view pending payments and approve/reject via AJAX
require_once __DIR__ . '/../common/config.php';
require_once __DIR__ . '/common/header.php';
$page_title = 'Payments';

$pending = $conn->query("SELECT p.*, u.username, u.email, pl.name as plan_name FROM payments p LEFT JOIN users u ON p.user_id = u.id LEFT JOIN plans pl ON p.plan_id = pl.id WHERE p.status = 'pending' ORDER BY p.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<div class="mb-4 flex items-center justify-between">
  <h1 class="text-2xl font-bold">Pending Payments</h1>
</div>

<div class="bg-gray-800 p-4 rounded">
  <?php if (empty($pending)): ?>
    <p class="text-gray-400">No pending payments</p>
  <?php else: ?>
    <table class="w-full text-sm">
      <thead><tr class="border-b border-gray-700"><th class="p-2">ID</th><th class="p-2">User</th><th class="p-2">Plan</th><th class="p-2">Amount</th><th class="p-2">Method</th><th class="p-2">Proof</th><th class="p-2">Actions</th></tr></thead>
      <tbody>
        <?php foreach ($pending as $p): ?>
          <tr class="border-b border-gray-700 hover:bg-gray-700/30">
            <td class="p-2">#<?php echo (int)$p['id']; ?></td>
            <td class="p-2"><?php echo htmlspecialchars($p['username']); ?><div class="text-xs text-gray-400"><?php echo htmlspecialchars($p['email']); ?></div></td>
            <td class="p-2"><?php echo htmlspecialchars($p['plan_name']); ?></td>
            <td class="p-2">€<?php echo number_format($p['amount'],2); ?></td>
            <td class="p-2"><?php echo htmlspecialchars($p['payment_method']); ?></td>
            <td class="p-2"><?php if ($p['screenshot_path']): ?><a href="<?php echo htmlspecialchars($p['screenshot_path']); ?>" target="_blank" class="text-cyan-300">View</a><?php else: ?>-<?php endif; ?></td>
            <td class="p-2">
              <button onclick="changeStatus(<?php echo (int)$p['id']; ?>,'approved')" class="bg-green-600 px-2 py-1 rounded mr-2">Approve</button>
              <button onclick="rejectPrompt(<?php echo (int)$p['id']; ?>)" class="bg-red-600 px-2 py-1 rounded">Reject</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<script>
function changeStatus(id, status) {
  if (!confirm('Change status to ' + status + '?')) return;
  fetch('/admin/api/approve-payment.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ payment_id: id, action: status })
  })
  .then(r=>r.json()).then(j=>{
    if (j.success) location.reload();
    else alert('Error: ' + (j.error || 'unknown'));
  }).catch(e=>alert('Error: '+e.message));
}

function rejectPrompt(id) {
  const reason = prompt('Rejection reason (optional):');
  if (reason === null) return;
  fetch('/admin/api/reject-payment.php', {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify({ payment_id: id, reason })
  }).then(r=>r.json()).then(j=>{
    if (j.success) location.reload();
    else alert('Error: ' + (j.error || 'unknown'));
  }).catch(e=>alert('Error: '+e.message));
}
</script>

<?php require_once __DIR__ . '/common/bottom.php'; ?>
