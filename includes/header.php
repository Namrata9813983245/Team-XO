<?php
// Expects $pageTitle to be set. Uses base_url() for portable links.
$__u = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? get_setting('site_name','AgroSense')) ?> · <?= e(get_setting('site_name','AgroSense')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<header class="site-header">
  <div class="header-inner">
    <a class="brand" href="<?= base_url($__u ? ($__u['role']==='admin' ? 'admin/dashboard.php' : 'user/home.php') : 'index.php') ?>">
      <span class="brand-mark">🌾</span> <span class="brand-name"><?= e(get_setting('site_name','AgroSense')) ?></span>
    </a>
    <input type="checkbox" id="nav-toggle" class="nav-toggle">
    <label for="nav-toggle" class="nav-burger"><span></span><span></span><span></span></label>
    <nav class="main-nav">
      <?php if ($__u): ?>
        <?php if ($__u['role'] === 'admin'): ?>
          <a href="<?= base_url('admin/dashboard.php') ?>">Dash</a>
          <a href="<?= base_url('admin/crops.php') ?>">Crops</a>
          <a href="<?= base_url('admin/fields.php') ?>">Fields</a>
          <a href="<?= base_url('admin/users.php') ?>">Users</a>
          <a href="<?= base_url('admin/history.php') ?>">History</a>
        <?php else: ?>
          <a href="<?= base_url('user/home.php') ?>">Home</a>
          <a href="<?= base_url('user/recommend.php') ?>">Recommend</a>
          <a href="<?= base_url('user/history.php') ?>">History</a>
        <?php endif; ?>
        <a href="<?= base_url('about.php') ?>">About Us</a>
        <a href="<?= base_url($__u['role']==='admin' ? 'admin/settings.php' : 'user/settings.php') ?>" class="nav-profile">
          <img src="<?= $__u['profile_picture'] ? base_url('assets/uploads/profiles/'.e($__u['profile_picture'])) : 'https://loremflickr.com/60/60/portrait?lock='.$__u['id'] ?>" alt="" class="nav-avatar">
          <span><?= e($__u['name']) ?></span>
        </a>
        <a href="<?= base_url('logout.php') ?>" class="btn-nav-logout">Log out</a>
      <?php else: ?>
        <a href="<?= base_url('index.php') ?>">Home</a>
        <a href="<?= base_url('about.php') ?>">About Us</a>
        <a href="<?= base_url('login.php') ?>">Log in</a>
        <a href="<?= base_url('register.php') ?>" class="btn-nav-cta">Get Started</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main>
<?php
$__flashSuccess = flash('contact_success') ?? flash('success');
$__flashError = flash('contact_error') ?? flash('error');
if ($__flashSuccess || $__flashError):
?>
<div class="container" style="padding-top:22px;">
  <?php if ($__flashSuccess): ?><div class="alert alert-success"><?= e($__flashSuccess) ?></div><?php endif; ?>
  <?php if ($__flashError): ?><div class="alert alert-error"><?= e($__flashError) ?></div><?php endif; ?>
</div>
<?php endif; ?>

