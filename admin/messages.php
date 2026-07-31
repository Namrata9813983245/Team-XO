<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$__u = current_user();
$db = getDB();

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM contact_messages WHERE id=?")->execute([$_GET['delete']]);
    header('Location: messages.php'); exit;
}

$messages = $db->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll();
$pageTitle = 'Contact Messages';
require __DIR__ . '/../includes/header.php';
?>
<div class="dash-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head"><div><h1>Contact Messages</h1><p style="margin:4px 0 0;color:var(--ink-soft);">Submissions from the footer "Contact Us" form.</p></div></div>
 <?php if (!$messages): ?>
      <div class="card empty-state"><div class="icon">✉️</div><h3>No messages yet</h3></div>
    <?php else: foreach ($messages as $m): ?>
      <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:start;">
          <div>
            <strong><?= e($m['name']) ?></strong> · <span style="color:var(--ink-soft);"><?= e($m['email']) ?></span>
            <p style="margin:8px 0 0;"><?= nl2br(e($m['message'])) ?></p>
            <p style="margin:8px 0 0;font-size:.78rem;color:var(--ink-soft);"><?= e(date('M j, Y g:i a', strtotime($m['created_at']))) ?></p>
          </div>
          <a href="?delete=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this message?');">Delete</a>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
