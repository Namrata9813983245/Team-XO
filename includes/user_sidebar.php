<?php $__here = basename($_SERVER['SCRIPT_NAME']); ?>
<aside class="dash-side">
  <div class="side-user">
    <img src="<?= $__u['profile_picture'] ? base_url('assets/uploads/profiles/'.e($__u['profile_picture'])) : 'https://loremflickr.com/80/80/portrait?lock='.$__u['id'] ?>" alt="">
    <div>
      <strong><?= e($__u['name']) ?></strong>
      <span>Grower</span>
    </div>
  </div>
  <nav class="dash-nav">
    <a href="<?= base_url('user/home.php') ?>" class="<?= $__here==='home.php'?'active':'' ?>">🏠 Home</a>
    <a href="<?= base_url('user/recommend.php') ?>" class="<?= $__here==='recommend.php'?'active':'' ?>">🌾 Recommend Crop</a>
    <a href="<?= base_url('user/history.php') ?>" class="<?= $__here==='history.php'?'active':'' ?>">🕘 History</a>
    <a href="<?= base_url('user/settings.php') ?>" class="<?= $__here==='settings.php'?'active':'' ?>">⚙️ Settings</a>
    <a href="<?= base_url('about.php') ?>">ℹ️ About Us</a>
    <a href="<?= base_url('logout.php') ?>">🚪 Log out</a>
  </nav>
</aside>
