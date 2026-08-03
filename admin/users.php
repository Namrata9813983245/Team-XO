<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$__u = current_user();
$db = getDB();

if (isset($_GET['toggle_role']) && (int)$_GET['toggle_role'] !== (int)$__u['id']) {
    $stmt = $db->prepare("SELECT role FROM users WHERE id=?");
    $stmt->execute([$_GET['toggle_role']]);
    if ($row = $stmt->fetch()) {
        $newRole = $row['role'] === 'admin' ? 'user' : 'admin';
        $db->prepare("UPDATE users SET role=? WHERE id=?")->execute([$newRole, $_GET['toggle_role']]);
        flash('success', 'Role updated.');
    }
    header('Location: users.php'); exit;
}
if (isset($_GET['delete']) && (int)$_GET['delete'] !== (int)$__u['id']) {
    $db->prepare("DELETE FROM users WHERE id=?")->execute([$_GET['delete']]);
    flash('success', 'User removed.');
    header('Location: users.php'); exit;
}

$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$pageTitle = 'Manage Users';
require __DIR__ . '/../includes/header.php';
?>
<div class="dash-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head"><div><h1>Users</h1><p style="margin:4px 0 0;color:var(--ink-soft);">Everyone with an AgroSense account.</p></div></div>
    <?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>

    <div class="card">
      <table class="data-table">
        <thead><tr><th>User</th><th>Email</th><th>Location</th><th>Role</th><th>Joined</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td style="display:flex;align-items:center;gap:10px;">
              <img src="<?= $u['profile_picture'] ? base_url('assets/uploads/profiles/'.e($u['profile_picture'])) : 'https://loremflickr.com/60/60/portrait?lock='.$u['id'] ?>" class="table-thumb" style="border-radius:50%;" alt="">
              <?= e($u['name']) ?>
            </td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['location'] ?: '—') ?></td>
            <td><span class="badge <?= $u['role']==='admin'?'badge-admin':'badge-user' ?>"><?= e(ucfirst($u['role'])) ?></span></td>
            <td><?= e(date('M j, Y', strtotime($u['created_at']))) ?></td>
            <td style="white-space:nowrap;">
              <?php if ((int)$u['id'] !== (int)$__u['id']): ?>
                <a href="?toggle_role=<?= $u['id'] ?>" class="btn btn-sm btn-outline">Make <?= $u['role']==='admin'?'User':'Admin' ?></a>
                <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove <?= e(addslashes($u['name'])) ?>?');">Delete</a>
              <?php else: ?>
                <span style="font-size:.78rem;color:var(--ink-soft);">This is you</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
