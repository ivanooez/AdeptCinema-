<?php
// admin/manage_movie.php - add/edit movie
require_once __DIR__ . '/../common/config.php';
require_once __DIR__ . '/common/header.php';
$page_title = 'Manage Movie';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $movie = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    $movie = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $rating = floatval($_POST['rating'] ?? 0);
    $year = (int)($_POST['release_year'] ?? 0);
    $watch_link = trim($_POST['watch_link'] ?? '');
    $subtitle_url = trim($_POST['subtitle_url'] ?? '');

    // Poster upload handling
    $poster_path = $movie['poster_url'] ?? '';
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['poster_file']['name'], PATHINFO_EXTENSION);
        $new = 'post_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = UPLOADS_DIR . 'posters/' . $new;
        if (move_uploaded_file($_FILES['poster_file']['tmp_name'], $dest)) {
            $poster_path = '/uploads/posters/' . $new;
        } else {
            $error = 'Poster upload failed';
        }
    }

    // subtitle file
    if (isset($_FILES['subtitle_file']) && $_FILES['subtitle_file']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['subtitle_file']['name'], PATHINFO_EXTENSION);
        $new = 'sub_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = UPLOADS_DIR . 'subtitles/' . $new;
        if (move_uploaded_file($_FILES['subtitle_file']['tmp_name'], $dest)) {
            $subtitle_url = '/uploads/subtitles/' . $new;
        } else {
            $error = 'Subtitle upload failed';
        }
    }

    if (!$title || !$desc) $error = 'Please fill title and description';

    if (!$error) {
        if ($id) {
            $stmt = $conn->prepare("UPDATE movies SET title=?, description=?, poster_url=?, category_id=?, rating=?, release_year=?, watch_link=?, subtitle_url=? WHERE id=?");
            $stmt->bind_param('sssiddssi', $title, $desc, $poster_path, $category_id, $rating, $year, $watch_link, $subtitle_url, $id);
            if ($stmt->execute()) { $success = 'Updated'; }
            else { $error = 'Update failed'; }
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO movies (title, description, poster_url, category_id, rating, release_year, watch_link, subtitle_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param('sssiddss', $title, $desc, $poster_path, $category_id, $rating, $year, $watch_link, $subtitle_url);
            if ($stmt->execute()) { $success = 'Created'; $id = $stmt->insert_id; }
            else { $error = 'Insert failed'; }
            $stmt->close();
        }
        // reload movie
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ? LIMIT 1");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $movie = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
    }
}

// fetch categories
$cats = $conn->query("SELECT id,name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>
<div class="mb-4 flex items-center justify-between">
  <h1 class="text-2xl font-bold"><?php echo $movie ? 'Edit Movie' : 'Add Movie'; ?></h1>
  <a href="/admin/movies.php" class="bg-gray-700 px-3 py-2 rounded">Back</a>
</div>

<?php if ($error): ?><div class="bg-red-900 p-3 mb-3 text-red-200"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if ($success): ?><div class="bg-green-900 p-3 mb-3 text-green-200"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="bg-gray-800 p-4 rounded">
  <label class="block mb-2">Title</label>
  <input name="title" value="<?php echo htmlspecialchars($movie['title'] ?? ''); ?>" class="w-full mb-3 p-2 bg-gray-700 rounded">

  <label class="block mb-2">Description</label>
  <textarea name="description" class="w-full mb-3 p-2 bg-gray-700 rounded"><?php echo htmlspecialchars($movie['description'] ?? ''); ?></textarea>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
    <div>
      <label class="block mb-2">Category</label>
      <select name="category_id" class="w-full p-2 bg-gray-700 rounded">
        <option value="0">None</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?php echo (int)$c['id']; ?>" <?php echo (isset($movie['category_id']) && $movie['category_id']==$c['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="block mb-2">Rating</label>
      <input name="rating" value="<?php echo htmlspecialchars($movie['rating'] ?? ''); ?>" class="w-full p-2 bg-gray-700 rounded">
    </div>

    <div>
      <label class="block mb-2">Release Year</label>
      <input name="release_year" value="<?php echo htmlspecialchars($movie['release_year'] ?? ''); ?>" class="w-full p-2 bg-gray-700 rounded">
    </div>
  </div>

  <label class="block mb-2 mt-3">Poster file (optional)</label>
  <?php if (!empty($movie['poster_url'])): ?><div class="mb-2"><img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="poster" class="h-28"></div><?php endif; ?>
  <input type="file" name="poster_file" class="mb-3">

  <label class="block mb-2">Watch link (URL/embed)</label>
  <input name="watch_link" value="<?php echo htmlspecialchars($movie['watch_link'] ?? ''); ?>" class="w-full mb-3 p-2 bg-gray-700 rounded">

  <label class="block mb-2">Subtitles file (.srt/.vtt) or URL</label>
  <?php if (!empty($movie['subtitle_url'])): ?><div class="mb-2 text-sm text-green-300">Current: <?php echo htmlspecialchars(basename($movie['subtitle_url'])); ?></div><?php endif; ?>
  <input type="file" name="subtitle_file" class="mb-2">
  <input type="text" name="subtitle_url" placeholder="or enter external subtitle URL" class="w-full mb-3 p-2 bg-gray-700 rounded" value="<?php echo htmlspecialchars($movie['subtitle_url'] ?? ''); ?>">

  <div class="mt-4">
    <button class="bg-cyan-500 px-4 py-2 rounded"><?php echo $movie ? 'Save' : 'Create'; ?></button>
  </div>
</form>

<?php require_once __DIR__ . '/common/bottom.php'; ?>
