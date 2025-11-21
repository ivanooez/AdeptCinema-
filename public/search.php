<?php
require_once __DIR__ . '/../common/config.php';
session_start();

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page_title = 'Search';
require_once __DIR__ . '/../common/header.php';

$movies = [];
if ($q !== '') {
    $term = '%' . $conn->real_escape_string($q) . '%';
    $res = $conn->query("SELECT id, title, poster_url FROM movies WHERE title LIKE '$term' LIMIT 50");
    if ($res) $movies = $res->fetch_all(MYSQLI_ASSOC);
}
?>
<section class="py-8">
  <h1 class="text-2xl font-bold mb-4">Search results for "<?php echo htmlspecialchars($q); ?>"</h1>
  <?php if (empty($movies)): ?>
    <p class="text-gray-400">No results</p>
  <?php else: ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <?php foreach ($movies as $m): ?>
        <a href="/movie_details.php?id=<?php echo (int)$m['id']; ?>" class="group">
          <div class="aspect-[2/3] bg-gray-800 rounded overflow-hidden">
            <img src="<?php echo htmlspecialchars($m['poster_url'] ?: '/placehold.jpg'); ?>" class="w-full h-full object-cover">
          </div>
          <p class="mt-2 text-sm"><?php echo htmlspecialchars($m['title']); ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../common/bottom.php'; ?>
