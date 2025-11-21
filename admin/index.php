<?php
require_once __DIR__ . '/../common/config.php';
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: /admin/login.php'); exit; }
require_once __DIR__ . '/common/header.php';
$page_title = 'Admin Dashboard';
?>
<section class="p-4">
  <h1 class="text-2xl font-bold">Admin Dashboard</h1>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
    <a href="movies.php" class="bg-gray-800 p-4 rounded">Movies</a>
    <a href="categories.php" class="bg-gray-800 p-4 rounded">Categories</a>
    <a href="payments.php" class="bg-gray-800 p-4 rounded">Payments</a>
  </div>
</section>
<?php require_once __DIR__ . '/common/bottom.php'; ?>
