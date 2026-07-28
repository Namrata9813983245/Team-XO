<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$__u = current_user();
$db = getDB();
$error = null; $success = null;

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM articles WHERE id=?")->execute([$_GET['delete']]);
    header('Location: articles.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '' || $content === '') {
        $error = 'Title and content are required.';
    } else {
        $uploaded = handle_image_upload('image', __DIR__ . '/../assets/uploads/crops', 'article');
        $image = $uploaded ? base_url('assets/uploads/crops/'.$uploaded) : 'https://loremflickr.com/600/400/farm,agriculture?lock='.crc32($title);
        $stmt = $db->prepare("INSERT INTO articles (title,content,image) VALUES (?,?,?)");
        $stmt->execute([$title, $content, $image]);
        $success = 'Article published to the user home page.';
    }
}

$articles = $db->query("SELECT * FROM articles ORDER BY id DESC")->fetchAll();
$pageTitle = 'Articles';
require __DIR__ . '/../includes/header.php';
?>
<div class="dash-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head"><div><h1>Articles</h1><p style="margin:4px 0 0;color:var(--ink-soft);">These appear on the user home page alongside live sensor data.</p></div></div>
    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
      <h3>Publish a new article</h3>
      <form method="POST" enctype="multipart/form-data">
        <div class="field"><label for="title">Title</label><input type="text" id="title" name="title" required></div>
        <div class="field"><label for="content">Content</label><textarea id="content" name="content" rows="4" required></textarea></div>
        <div class="field"><label for="image">Photo (optional)</label><input type="file" id="image" name="image" accept="image/*"></div>
        <button class="btn" type="submit">Publish article</button>
      </form>
    </div>

    <div class="card">
      <h3>Published articles</h3>
      <?php foreach ($articles as $a): ?>
        <div class="history-card">
          <img src="<?= e($a['image']) ?>" alt="">
          <div style="flex:1;"><strong><?= e($a['title']) ?></strong><p style="margin:2px 0 0;font-size:.85rem;"><?= e(trim_text($a['content'], 140)) ?></p></div>
          <a href="?delete=<?= $a['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this article?');">Delete</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
