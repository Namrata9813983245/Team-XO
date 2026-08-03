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