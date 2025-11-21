<?php
require_once __DIR__ . '/../common/config.php';
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: /categories_page.php'); exit; }

$stmt = $conn->prepare("SELECT id, name, description FROM categories WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$cat = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$cat) { header('Location: /categories_page.php'); exit; }

$movies = $conn->query("SELECT id,title,poster_url FROM movies WHERE category_id = $id ORDER BY release_year DESC")->fetch_all(MYSQLI_ASSOC);
$page_title = $cat['name'];
require_once __DIR__ . '/../common/header.php';
?>
<section class="py-8">
  <h1 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($cat['name']); ?></h1>
  <p class="text-gray-400 mb-6"><?php echo htmlspecialchars($cat['description']); ?></p>
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
</section>
<?php require_once __DIR__ . '/../common/bottom.php'; ?>
