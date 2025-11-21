<?php
require_once __DIR__ . '/../common/config.php';
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($u === '' || $p === '') $error = 'Fill fields';
    else {
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $u);
        $stmt->execute();
        $a = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($a && password_verify($p, $a['password'])) {
            $_SESSION['admin_id'] = $a['id'];
            $_SESSION['admin_username'] = $u;
            header('Location: /admin/index.php');
            exit;
        } else $error = 'Invalid';
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin Login</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-900 text-white p-6">
  <div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-4">Admin Login</h1>
    <?php if ($error): ?><div class="text-red-400 mb-2"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="POST" class="bg-gray-800 p-4 rounded">
      <label class="block mb-2">Username</label>
      <input name="username" class="w-full mb-3 p-2 bg-gray-700 rounded" />
      <label class="block mb-2">Password</label>
      <input name="password" type="password" class="w-full mb-3 p-2 bg-gray-700 rounded" />
      <button class="bg-cyan-500 px-3 py-2 rounded">Login</button>
    </form>
  </div>
</body></html>
