<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';
require_login();
$__u = current_user();
if ($__u['role'] === 'admin') { header('Location: ../admin/dashboard.php'); exit; }

$error = null; $success = null;


<div class="dash-shell">
  <?php require __DIR__ . '/../includes/user_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head"><div><h1>Settings</h1><p style="margin:4px 0 0;color:var(--ink-soft);">Manage your profile and account security.</p></div></div>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
      <h3>Profile</h3>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="profile">
        <div class="settings-avatar-row">
          <img src="<?= $__u['profile_picture'] ? base_url('assets/uploads/profiles/'.e($__u['profile_picture'])) : 'https://loremflickr.com/100/100/portrait?lock='.$__u['id'] ?>" alt="">
          <div class="field" style="margin:0;flex:1;">
            <label for="profile_picture">Profile picture</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
          </div>
        </div>
        <div class="field-row">
          <div class="field"><label for="name">Full name</label><input type="text" id="name" name="name" value="<?= e($__u['name']) ?>" required></div>
          <div class="field"><label for="phone">Phone</label><input type="text" id="phone" name="phone" value="<?= e($__u['phone']) ?>"></div>
        </div>
        <div class="field"><label for="location">Farm location</label><input type="text" id="location" name="location" value="<?= e($__u['location']) ?>"></div>
        <div class="field"><label>Email address</label><input type="email" value="<?= e($__u['email']) ?>" disabled></div>
        <button class="btn" type="submit">Save profile</button>
      </form>
    </div>

    <div class="card">
      <h3>Change password</h3>
      <form method="POST">
        <input type="hidden" name="action" value="password">
        <div class="field"><label for="current_password">Current password</label><input type="password" id="current_password" name="current_password" required></div>
        <div class="field-row">
          <div class="field"><label for="new_password">New password</label><input type="password" id="new_password" name="new_password" required></div>
          <div class="field"><label for="confirm_password">Confirm new password</label><input type="password" id="confirm_password" name="confirm_password" required></div>
        </div>
        <button class="btn btn-outline" type="submit">Update password</button>
      </form>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
