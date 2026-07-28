<?php

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
