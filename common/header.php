<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="bg">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo isset($page_title) ? htmlspecialchars($page_title).' - '.APP_NAME : APP_NAME; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>body{background:#0b1220;color:#fff}</style>
</head>
<body>
<header class="bg-gray-900 p-4 border-b border-gray-800">
  <div class="max-w-6xl mx-auto flex justify-between items-center">
    <a href="/" class="text-2xl font-bold text-cyan-400"><i class="fas fa-film mr-2"></i><?php echo APP_NAME; ?></a>
    <nav class="flex items-center gap-4 text-gray-300">
      <a href="/search.php" class="hover:text-white"><i class="fas fa-search"></i></a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/profile.php" class="hover:text-white"><i class="fas fa-user"></i></a>
        <a href="/logout.php" class="text-red-400 hover:text-red-300">Logout</a>
      <?php else: ?>
        <a href="/login.php" class="hover:text-white">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="max-w-6xl mx-auto p-4">
