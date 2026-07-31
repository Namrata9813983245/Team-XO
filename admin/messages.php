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
