<?php
require_once __DIR__ . '/../common/config.php';
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: /'); exit; }

$stmt = $conn->prepare("SELECT m.*, c.name as category_name FROM movies m LEFT JOIN categories c ON m.category_id = c.id WHERE m.id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$movie) { header('Location: /'); exit; }

$page_title = $movie['title'];
require_once __DIR__ . '/../common/header.php';
?>
<section class="py-8">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2">
      <img src="<?php echo htmlspecialchars($movie['poster_url'] ?: '/placehold.jpg'); ?>" alt="" class="w-full h-96 object-cover rounded mb-4">
      <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($movie['title']); ?></h1>
      <p class="text-gray-400 mt-2"><?php echo htmlspecialchars($movie['category_name']); ?> • <?php echo htmlspecialchars($movie['release_year']); ?></p>
      <p class="mt-4"><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>

      <?php if ($movie['watch_link']): ?>
        <div class="mt-6">
          <a href="/watch.php?id=<?php echo (int)$movie['id']; ?>" class="bg-cyan-500 px-4 py-2 rounded">Watch Now</a>
        </div>
      <?php endif; ?>
    </div>

    <aside>
      <div class="bg-gray-800 p-4 rounded">
        <h3 class="font-bold mb-2">Details</h3>
        <p>Rating: <?php echo htmlspecialchars($movie['rating'] ?: 'N/A'); ?></p>
        <p class="mt-2">Category: <?php echo htmlspecialchars($movie['category_name'] ?: 'N/A'); ?></p>
      </div>
    </aside>
  </div>
</section>

<?php require_once __DIR__ . '/../common/bottom.php'; ?>
