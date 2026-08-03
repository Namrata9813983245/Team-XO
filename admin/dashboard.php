<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$__u = current_user();
$db = getDB();

$cropCount = (int)$db->query("SELECT COUNT(*) c FROM crops")->fetch()['c'];
$userCount = (int)$db->query("SELECT COUNT(*) c FROM users WHERE role='user'")->fetch()['c'];
$historyCount = (int)$db->query("SELECT COUNT(*) c FROM recommendation_history")->fetch()['c'];
$msgCount = (int)$db->query("SELECT COUNT(*) c FROM contact_messages")->fetch()['c'];

$recentHistory = $db->query("
  SELECT h.*, u.name AS user_name, c.name AS crop_name, c.image AS crop_image
  FROM recommendation_history h
  JOIN users u ON u.id = h.user_id
  LEFT JOIN crops c ON c.id = h.recommended_crop_id
  ORDER BY h.id DESC LIMIT 6
")->fetchAll();

$topCrops = $db->query("
  SELECT c.name, c.image, COUNT(h.id) AS n
  FROM crops c LEFT JOIN recommendation_history h ON h.recommended_crop_id = c.id
  GROUP BY c.id ORDER BY n DESC LIMIT 5
")->fetchAll();

$pageTitle = 'Admin dash';
require __DIR__ . '/../includes/header.php';
?>
<div class="dash-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head">
      <div><h1>Admin Dash</h1><p style="margin:4px 0 0;color:var(--ink-soft);">An overview of AgroSense activity.</p></div>
      <a href="<?= base_url('admin/crops.php') ?>" class="btn">+ Add Crop</a>
    </div>

    <div class="stat-grid">
      <div class="stat-card"><div class="stat-label">Crops in catalog</div><div class="stat-value"><?= $cropCount ?></div><div class="stat-sub">Editable anytime</div></div>
      <div class="stat-card"><div class="stat-label">Registered growers</div><div class="stat-value"><?= $userCount ?></div><div class="stat-sub">Standard users</div></div>
      <div class="stat-card"><div class="stat-label">Recommendations run</div><div class="stat-value"><?= $historyCount ?></div><div class="stat-sub">All time</div></div>
      <div class="stat-card"><div class="stat-label">Contact messages</div><div class="stat-value"><?= $msgCount ?></div><div class="stat-sub"><a href="<?= base_url('admin/messages.php') ?>" style="color:var(--forest);">View inbox →</a></div></div>
    </div>

    <div class="field-row" style="align-items:start;">
      <div class="card" style="margin:0;">
        <h3>Recent recommendations</h3>
        <?php if (!$recentHistory): ?>
          <p style="color:var(--ink-soft);">No recommendations run yet.</p>
        <?php else: foreach ($recentHistory as $h): ?>
          <div class="history-card">
            <img src="<?= e($h['crop_image'] ?: 'https://loremflickr.com/100/100/plant?lock='.$h['id']) ?>" alt="">
            <div style="flex:1;">
              <strong><?= e($h['user_name']) ?></strong> matched with <strong><?= e($h['crop_name'] ?? '—') ?></strong>
              <div style="font-size:.78rem;color:var(--ink-soft);"><?= e(date('M j, g:i a', strtotime($h['created_at']))) ?></div>
            </div>
            <?php if ($h['match_score'] !== null): ?><span class="badge badge-active"><?= (int)$h['match_score'] ?>%</span><?php endif; ?>
          </div>
        <?php endforeach; endif; ?>
      </div>

      <div class="card" style="margin:0;">
        <h3>Most recommended crops</h3>
        <?php if (!$topCrops): ?>
          <p style="color:var(--ink-soft);">No data yet.</p>
        <?php else: foreach ($topCrops as $t): ?>
          <div class="history-card">
            <img src="<?= e($t['image']) ?>" alt="">
            <div style="flex:1;"><strong><?= e($t['name']) ?></strong></div>
            <span class="badge badge-user"><?= (int)$t['n'] ?> times</span>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
