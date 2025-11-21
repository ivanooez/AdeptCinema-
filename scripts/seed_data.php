<?php
// scripts/seed_data.php - run from project root: php scripts/seed_data.php
require_once __DIR__ . '/../common/config.php';

echo "Seeding data...\n";

// create admin if not exists
$adminUser = 'admin';
$adminPass = 'admin123';
$hash = password_hash($adminPass, PASSWORD_BCRYPT);
$stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $adminUser);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $stmt2 = $conn->prepare("INSERT INTO admins (username, password, created_at) VALUES (?, ?, NOW())");
    $stmt2->bind_param('ss', $adminUser, $hash);
    $stmt2->execute();
    echo "Admin created: {$adminUser}/{$adminPass}\n";
    $stmt2->close();
} else {
    echo "Admin already exists\n";
}
$stmt->close();

// insert sample plans
$plans = [
    ['Basic Plan','Basic access',9.99,30],
    ['Premium Plan','Full access',19.99,30],
];
foreach ($plans as $p) {
    $stmt = $conn->prepare("INSERT IGNORE INTO plans (name, description, price, features, duration_days, is_active, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
    $features = json_encode(['HD','Download','Ad-free']);
    $stmt->bind_param('ssdsi', $p[0], $p[1], $p[2], $features, $p[3]);
    $stmt->execute();
    $stmt->close();
}
echo "Seeded plans\n";

// sample category and movie
$conn->query("INSERT IGNORE INTO categories (name, description, created_at) VALUES ('Action','Action movies', NOW())");
$cat = $conn->query("SELECT id FROM categories WHERE name='Action' LIMIT 1")->fetch_assoc()['id'] ?? 0;
if ($cat) {
    $title = 'Sample Movie';
    $check = $conn->query("SELECT id FROM movies WHERE title = 'Sample Movie' LIMIT 1");
    if ($check->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO movies (title, description, poster_url, category_id, rating, release_year, watch_link, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $poster = '/uploads/posters/sample.jpg';
        $link = ''; // fill later
        $rating = 7.5; $year = 2021;
        $stmt->bind_param('sssidsis', $title, $title, $poster, $cat, $rating, $year, $link);
        // adjust types properly
        $stmt->close();
        $conn->query("INSERT INTO movies (title, description, poster_url, category_id, rating, release_year, watch_link, created_at) VALUES ('Sample Movie','A sample movie for testing','/uploads/posters/sample.jpg', $cat, 7.5, 2021, '', NOW())");
        echo "Inserted sample movie\n";
    }
}
echo "Done\n";
