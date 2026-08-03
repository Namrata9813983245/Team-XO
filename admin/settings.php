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

    <div class="card">
      <h3>Site settings</h3>
      <p style="margin-top:-8px;">Changes here update the public site — including the About Us page and footer.</p>
      <form method="POST">
        <input type="hidden" name="action" value="site">
        <div class="field-row">
          <div class="field"><label for="site_name">Site name</label><input type="text" id="site_name" name="site_name" value="<?= e(get_setting('site_name')) ?>"></div>
          <div class="field"><label for="tagline">Tagline</label><input type="text" id="tagline" name="tagline" value="<?= e(get_setting('tagline')) ?>"></div>
        </div>
        <div class="field"><label for="about_us">About Us content</label><textarea id="about_us" name="about_us" rows="4"><?= e(get_setting('about_us')) ?></textarea></div>
        <div class="field-row">
          <div class="field"><label for="contact_email">Contact email</label><input type="email" id="contact_email" name="contact_email" value="<?= e(get_setting('contact_email')) ?>"></div>
          <div class="field"><label for="contact_phone">Contact phone</label><input type="text" id="contact_phone" name="contact_phone" value="<?= e(get_setting('contact_phone')) ?>"></div>
        </div>
        <div class="field"><label for="contact_address">Contact address</label><input type="text" id="contact_address" name="contact_address" value="<?= e(get_setting('contact_address')) ?>"></div>
        <button class="btn" type="submit">Save site settings</button>
      </form>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>