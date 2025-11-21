<?php
require_once __DIR__ . '/../common/config.php';
session_start();

$page_title = 'Home';
require_once __DIR__ . '/../common/header.php';

// Fetch featured banners and latest movies
$banners = $conn->query("SELECT * FROM banners ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$latest = $conn->query("SELECT id, title, poster_url FROM movies ORDER BY created_at DESC LIMIT 12")->fetch_all(MYSQLI_ASSOC);
?>
<section class="py-8">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2">
      <?php if (!empty($banners)): ?>
        <div class="mb-6">
          <img src="<?php echo htmlspecialchars('/' . ($banners[0]['image_url'] ?? '')); ?>" alt="Banner" class="w-full h-64 object-cover rounded">
        </div>
      <?php endif; ?>

      <h2 class="text-xl font-bold mb-4">Latest Releases</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($latest as $m): ?>
          <a href="/movie_details.php?id=<?php echo (int)$m['id']; ?>" class="group">
            <div class="aspect-[2/3] bg-gray-800 rounded overflow-hidden">
              <img src="<?php echo htmlspecialchars($m['poster_url'] ?: '/placehold.jpg'); ?>" alt="" class="w-full h-full object-cover">
            </div>
            <p class="mt-2 text-sm"><?php echo htmlspecialchars($m['title']); ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <aside>
      <div class="bg-gray-800 p-4 rounded mb-4">
        <h3 class="font-bold mb-2">Search</h3>
        <form action="/search.php" method="GET">
          <input type="text" name="q" placeholder="Search movies..." class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600" />
        </form>
      </div>

      <div class="bg-gray-800 p-4 rounded">
        <h3 class="font-bold mb-2">Categories</h3>
        <ul class="space-y-2 text-sm">
          <?php
            $cats = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
            foreach ($cats as $c):
          ?>
            <li><a href="/category_movies.php?id=<?php echo (int)$c['id']; ?>" class="text-cyan-300 hover:underline"><?php echo htmlspecialchars($c['name']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>
  </div>
</section>

<?php require_once __DIR__ . '/../common/bottom.php'; ?>
