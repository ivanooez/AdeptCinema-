<?php
require_once __DIR__ . '/../common/config.php';
session_start();
$page_title = 'Categories';
require_once __DIR__ . '/../common/header.php';

$cats = $conn->query("SELECT c.*, COUNT(m.id) as movie_count FROM categories c LEFT JOIN movies m ON m.category_id = c.id GROUP BY c.id ORDER BY c.name")->fetch_all(MYSQLI_ASSOC);
?>
<section class="py-8">
  <h1 class="text-2xl font-bold mb-4">Categories</h1>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <?php foreach ($cats as $c): ?>
      <a href="/category_movies.php?id=<?php echo (int)$c['id']; ?>" class="block bg-gray-800 p-4 rounded">
        <h3 class="font-bold"><?php echo htmlspecialchars($c['name']); ?></h3>
        <p class="text-gray-400 text-sm"><?php echo (int)$c['movie_count']; ?> movies</p>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once __DIR__ . '/../common/bottom.php'; ?>
