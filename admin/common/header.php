<?php
// admin/common/header.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}
require_once __DIR__ . '/../../common/config.php';
?>
<!doctype html>
<html lang="bg">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Admin' : 'Admin'; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>body{background:#0b1220;color:#fff}</style>
</head>
<body>
<header class="bg-gray-900 p-4 border-b border-gray-800">
  <div class="max-w-6xl mx-auto flex justify-between items-center">
    <a href="/admin/index.php" class="text-2xl font-bold text-cyan-400"><i class="fas fa-cog mr-2"></i>Admin</a>
    <nav class="flex items-center gap-4 text-gray-300">
      <a href="/admin/movies.php" class="hover:text-white">Movies</a>
      <a href="/admin/categories.php" class="hover:text-white">Categories</a>
      <a href="/admin/banners.php" class="hover:text-white">Banners</a>
      <a href="/admin/payments.php" class="hover:text-white">Payments</a>
      <a href="/admin/logout.php" class="text-red-400 hover:text-red-300">Logout</a>
    </nav>
  </div>
</header>
<main class="max-w-6xl mx-auto p-4">
