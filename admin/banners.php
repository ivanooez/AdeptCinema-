<?php
// admin/banners.php - upload / list banners
require_once __DIR__ . '/../common/config.php';
require_once __DIR__ . '/common/header.php';
$page_title = 'Banners';

$success = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        if (!isset($_FILES['banner_image']) || $_FILES['banner_image']['error'] !== UPLOAD_ERR_OK) $error = 'Choose image';
        else {
            $file = $_FILES['banner_image'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $name = 'banner_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest = UPLOADS_DIR . 'banners/' . $name;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $image_url = '/uploads/banners/' . $name;
                $title = trim($_POST['title'] ?? '');
                $movie_id = (int)($_POST['movie_id'] ?? 0);
                $stmt = $conn->prepare("INSERT INTO banners (image_url, title, movie_id, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param('ssi', $image_url, $title, $movie_id);
                if ($stmt->execute()) $success = 'Added banner';
                else $error = 'DB insert failed';
                $stmt->close();
            } else $error = 'Upload failed';
        }
    } elseif ($_POST['action'] === 'delete') {
        $bid = (int)($_POST['banner_id'] ?? 0);
        if ($bid) {
            $row = $conn->query("SELECT image_url FROM banners WHERE id = $bid")->fetch_assoc();
            if ($row && !empty($row['image_url'])) {
                $file = __DIR__ . '/..' . $row['image_url'];
                if (file_exists($file)) @unlink($file);
            }
            $conn->query("DELETE FROM banners WHERE id = $bid");
            $success = 'Deleted';
        }
    }
}

$banners = $conn->query("SELECT b.*, m.title as movie_title FROM banners b LEFT JOIN movies m ON b.movie_id = m.id ORDER BY b.created_at DESC")->fetch_all(MYSQLI_ASSOC);
$movies = $conn->query("SELECT id, title FROM movies ORDER BY title")->fetch_all(MYSQLI_ASSOC);
?>
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-bold">Banners</h1>
</div>

<?php if ($error): ?><div class="bg-red-900 p-3 mb-3 text-red-200"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if ($success): ?><div class="bg-green-900 p-3 mb-3 text-green-200"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

<div class="bg-gray-800 p-4 rounded mb-6">
  <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-3">
    <input type="hidden" name="action" value="add">
    <input type="file" name="banner_image" required class="col-span-1 md:col-span-3">
    <input type="text" name="title" placeholder="Title (optional)" class="p-2 bg-gray-700 rounded">
    <select name="movie_id" class="p-2 bg-gray-700 rounded">
      <option value="">No movie</option>
      <?php foreach ($movies as $m): ?><option value="<?php echo (int)$m['id']; ?>"><?php echo htmlspecialchars($m['title']); ?></option><?php endforeach; ?>
    </select>
    <button class="bg-cyan-500 px-3 py-2 rounded">Upload</button>
  </form>
</div>

<div class="bg-gray-800 p-4 rounded">
  <table class="w-full text-sm">
    <thead><tr class="border-b border-gray-700"><th class="p-2">Image</th><th class="p-2">Title</th><th class="p-2">Movie</th><th class="p-2">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($banners as $b): ?>
        <tr class="border-b border-gray-700 hover:bg-gray-700/30">
          <td class="p-2"><img src="<?php echo htmlspecialchars($b['image_url']); ?>" alt="" class="h-12"></td>
          <td class="p-2"><?php echo htmlspecialchars($b['title'] ?: '-'); ?></td>
          <td class="p-2"><?php echo htmlspecialchars($b['movie_title'] ?: '-'); ?></td>
          <td class="p-2">
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete banner?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="banner_id" value="<?php echo (int)$b['id']; ?>">
              <button class="text-red-400">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/common/bottom.php'; ?>
