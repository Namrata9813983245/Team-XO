<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$__u = current_user();
$db = getDB();

$history = $db->query("
  SELECT h.*, u.name AS user_name, u.email AS user_email, c.name AS crop_name, c.image AS crop_image
  FROM recommendation_history h
  JOIN users u ON u.id = h.user_id
  LEFT JOIN crops c ON c.id = h.recommended_crop_id
  ORDER BY h.id DESC
")->fetchAll();

$pageTitle = 'All Recommendation History';
require __DIR__ . '/../includes/header.php';
?>
<div class="dash-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head"><div><h1>All History</h1><p style="margin:4px 0 0;color:var(--ink-soft);">Every recommendation run across all users.</p></div></div>

    <?php if (!$history): ?>
      <div class="card empty-state"><div class="icon">🕘</div><h3>No recommendations yet</h3></div>
    <?php else: ?>
      <div class="card">
        <table class="data-table">
          <thead><tr><th>Photo</th><th>User</th><th>Recommended crop</th><th>Match</th><th>Conditions entered</th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach ($history as $h): $inputs = json_decode($h['input_data'], true) ?: []; ?>
            <tr>
              <td><img src="<?= e($h['crop_image'] ?: 'https://loremflickr.com/80/80/plant?lock='.$h['id']) ?>" class="table-thumb" alt=""></td>
              <td><?= e($h['user_name']) ?><br><span style="font-size:.76rem;color:var(--ink-soft);"><?= e($h['user_email']) ?></span></td>
              <td><?= e($h['crop_name'] ?? 'No strong match') ?></td>
              <td><?= $h['match_score']!==null ? '<span class="badge badge-active">'.(int)$h['match_score'].'%</span>' : '—' ?></td>
              <td style="font-size:.82rem;">
                <?php $parts=[]; foreach ($inputs as $k=>$v){ $parts[]=ucfirst(str_replace('_',' ',$k)).': '.e($v);} echo implode(' · ', $parts); ?>
              </td>
              <td style="font-size:.82rem;white-space:nowrap;"><?= e(date('M j, g:i a', strtotime($h['created_at']))) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
