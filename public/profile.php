<?php
require_once __DIR__ . '/../common/config.php';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location:/login.php'); exit; }

$stmt = $conn->prepare("SELECT id, username, email, full_name, subscription_end FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$page_title = 'Profile';
require_once __DIR__ . '/../common/header.php';
?>
<section class="py-8 max-w-2xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Profile</h1>
  <div class="bg-gray-800 p-4 rounded">
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Full name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
    <p><strong>Subscription end:</strong> <?php echo $user['subscription_end'] ? htmlspecialchars($user['subscription_end']) : 'None'; ?></p>
  </div>
</section>
<?php require_once __DIR__ . '/../common/bottom.php'; ?>
