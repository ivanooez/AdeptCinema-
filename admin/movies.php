<?php
// admin/movies.php - list and quick actions
require_once __DIR__ . '/../common/config.php';
require_once __DIR__ . '/common/header.php';
$page_title = 'Movies Management';

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

// Handle delete via POST (safer)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_movie') {
    $mid = (int)($_POST['movie_id'] ?? 0);
    if ($mid > 0) {
        $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->bind_param('i', $mid);
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: /admin/movies.php?success=Movie deleted');
            exit;
        } else {
            $error = 'Error deleting';
            $stmt->close();
        }
    }
}

// Fetch movies
$movies = $conn->query("SELECT m.id, m.title, m.release_year, m.rating, c.name as category FROM movies m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<div class="mb-6 flex items-center justify-between">
  <h1 class="text-2xl font-bold">Movies</h1>
  <a href="/admin/manage_movie.php" class="bg-cyan-500 px-4 py-2 rounded">Add Movie</a>
</div>

<?php if ($success): ?><div class="bg-green-800 p-3 rounded mb-4 text-green-200"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="bg-red-900 p-3 rounded mb-4 text-red-200"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="bg-gray-800 rounded p-4">
  <table class="w-full text-sm">
    <thead><tr class="border-b border-gray-700"><th class="text-left p-2">ID</th><th class="text-left p-2">Title</th><th class="p-2">Year</th><th class="p-2">Rating</th><th class="p-2">Category</th><th class="p-2">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($movies as $m): ?>
        <tr class="border-b border-gray-700 hover:bg-gray-700/30">
          <td class="p-2"><?php echo (int)$m['id']; ?></td>
          <td class="p-2"><?php echo htmlspecialchars($m['title']); ?></td>
          <td class="p-2"><?php echo htmlspecialchars($m['release_year']); ?></td>
          <td class="p-2"><?php echo $m['rating'] ? htmlspecialchars($m['rating']) : '-'; ?></td>
          <td class="p-2"><?php echo htmlspecialchars($m['category'] ?? '-'); ?></td>
          <td class="p-2">
            <a href="/admin/manage_movie.php?id=<?php echo (int)$m['id']; ?>" class="text-cyan-400 mr-3">Edit</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete movie?')">
              <input type="hidden" name="movie_id" value="<?php echo (int)$m['id']; ?>">
              <input type="hidden" name="action" value="delete_movie">
              <button class="text-red-400">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/common/bottom.php'; ?>
