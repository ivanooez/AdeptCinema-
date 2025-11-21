<?php
require_once __DIR__ . '/../common/config.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($u === '' || $p === '') $error = 'Fill all fields';
    else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $u);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($user && password_verify($p, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: /');
            exit;
        } else $error = 'Invalid credentials';
    }
}
$page_title = 'Login';
require_once __DIR__ . '/../common/header.php';
?>
<div class="py-8 max-w-md mx-auto">
  <form method="POST" class="bg-gray-800 p-6 rounded">
    <?php if ($error): ?><div class="text-red-400 mb-2"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <label class="block mb-2">Username</label>
    <input name="username" class="w-full mb-3 px-3 py-2 rounded bg-gray-700" />
    <label class="block mb-2">Password</label>
    <input name="password" type="password" class="w-full mb-4 px-3 py-2 rounded bg-gray-700" />
    <button class="bg-cyan-500 px-4 py-2 rounded">Login</button>
  </form>
</div>
<?php require_once __DIR__ . '/../common/bottom.php'; ?>
