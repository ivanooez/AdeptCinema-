<?php
// admin/categories.php - manage categories
require_once __DIR__ . '/../common/config.php';
require_once __DIR__ . '/common/header.php';
$page_title = 'Categories';

$success = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon_class'] ?? '');
        if ($name === '') $error = 'Name required';
        else {
            $stmt = $conn->prepare("INSERT INTO categories (name, description, icon_class, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param('sss', $name, $desc, $icon);
            if ($stmt->execute()) $success = 'Added';
            else $error = 'DB error';
            $stmt->close();
        }
    } elseif ($action === 'delete') {
        $cid = (int)($_POST['category_id'] ?? 0);
        if ($cid) {
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->bind_param('i', $cid);
            if ($stmt->execute()) $success = 'Deleted';
            else $error = 'Delete failed';
            $stmt->close();
        }
    }
}

$cats = $conn->query("SELECT c.*, COUNT(m.id) as movie_count FROM categories c LEFT JOIN movies m ON m.category_id = c.id GROUP BY c.id ORDER BY c.name")->fetch_all(MYSQLI_ASSOC);
?>
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-bold">Categories</h1>
</div>

<?php if ($success): ?><div class="bg-green-900 p-3 mb-3 text-green-200"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="bg-red-900 p-3 mb-3 text-red-200"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="bg-gray-800 p-4 rounded mb-6">
  <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3">
    <input type="hidden" name="action" value="add">
    <input name="name" placeholder="Category name" class="p-2 bg-gray-700 rounded">
    <input name="icon_class" placeholder="Icon class (optional)" class="p-2 bg-gray-700 rounded">
    <button class="bg-cyan-500 px-3 py-2 rounded">Add</button>
    <textarea name="description" placeholder="Description (optional)" class="col-span-1 md:col-span-3 p-2 bg-gray-700 rounded"></textarea>
  </form>
</div>

<div class="bg-gray-800 p-4 rounded">
  <table class="w-full text-sm">
    <thead><tr class="border-b border-gray-700"><th class="p-2 text-left">Name</th><th class="p-2">Movies</th><th class="p-2">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($cats as $c): ?>
        <tr class="border-b border-gray-700 hover:bg-gray-700/30">
          <td class="p-2"><?php echo htmlspecialchars($c['name']); ?></td>
          <td class="p-2 text-center"><?php echo (int)$c['movie_count']; ?></td>
          <td class="p-2">
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="category_id" value="<?php echo (int)$c['id']; ?>">
              <button class="text-red-400">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/common/bottom.php'; ?>
