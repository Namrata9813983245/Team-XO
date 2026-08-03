<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$__u = current_user();
$db = getDB();
$error = null; $success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'profile') {
        $name = trim($_POST['name'] ?? '');
        $pic = handle_image_upload('profile_picture', __DIR__ . '/../assets/uploads/profiles', 'admin' . $__u['id']);
        if ($pic) {
            $db->prepare("UPDATE users SET name=?, profile_picture=? WHERE id=?")->execute([$name, $pic, $__u['id']]);
        } else {
            $db->prepare("UPDATE users SET name=? WHERE id=?")->execute([$name, $__u['id']]);
        }
        $success = 'Profile updated.';
    } elseif ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!password_verify($current, $__u['password'])) {
            $error = 'Current password is incorrect.';
        } elseif (strlen($new) < 6) {
            $error = 'New password must be at least 6 characters.';
        } elseif ($new !== $confirm) {
            $error = 'New passwords do not match.';
        } else {
            $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($new, PASSWORD_DEFAULT), $__u['id']]);
            $success = 'Password changed.';
        }
         } elseif ($action === 'site') {
        $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?,?)
                               ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value");
        foreach (['site_name','tagline','about_us','contact_email','contact_phone','contact_address'] as $key) {
            $stmt->execute([$key, trim($_POST[$key] ?? '')]);
        }
        $success = 'Site settings updated.';
    }
    $stmt = $db->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$__u['id']]);
    $__u = $stmt->fetch();
}
$pageTitle = 'Settings';
require __DIR__ . '/../includes/header.php';
?>
<div class="dash-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head"><div><h1>Settings</h1><p style="margin:4px 0 0;color:var(--ink-soft);">Manage your admin profile and site-wide content.</p></div></div>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
      <h3>Your profile</h3>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="profile">
        <div class="settings-avatar-row">
          <img src="<?= $__u['profile_picture'] ? base_url('assets/uploads/profiles/'.e($__u['profile_picture'])) : 'https://loremflickr.com/100/100/portrait?lock='.$__u['id'] ?>" alt="">
          <div class="field" style="margin:0;flex:1;"><label for="profile_picture">Profile picture</label><input type="file" id="profile_picture" name="profile_picture" accept="image/*"></div>
        </div>
        <div class="field"><label for="name">Full name</label><input type="text" id="name" name="name" value="<?= e($__u['name']) ?>" required></div>
        <button class="btn" type="submit">Save profile</button>
      </form>
    </div>