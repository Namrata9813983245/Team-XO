<?php $__here = basename($_SERVER['SCRIPT_NAME']); ?>
<aside class="dash-side">
  <div class="side-user">
    <img src="<?= $__u['profile_picture'] ? base_url('assets/uploads/profiles/'.e($__u['profile_picture'])) : 'https://loremflickr.com/80/80/portrait?lock='.$__u['id'] ?>" alt="">
    <div>
      <strong><?= e($__u['name']) ?></strong>
      <span>Administrator</span>
    </div>
  </div>
  <nav class="dash-nav">
    <a href="<?= base_url('admin/dashboard.php') ?>" class="<?= $__here==='dashboard.php'?'active':'' ?>">📊 Dashboard</a>
    <a href="<?= base_url('admin/crops.php') ?>" class="<?= $__here==='crops.php'?'active':'' ?>">🌾 Manage Crops</a>
    <a href="<?= base_url('admin/fields.php') ?>" class="<?= $__here==='fields.php'?'active':'' ?>">🧩 Recommendation Fields</a>
    <a href="<?= base_url('admin/users.php') ?>" class="<?= $__here==='users.php'?'active':'' ?>">👥 Users</a>
    <a href="<?= base_url('admin/history.php') ?>" class="<?= $__here==='history.php'?'active':'' ?>">🕘 All History</a>
    <a href="<?= base_url('admin/articles.php') ?>" class="<?= $__here==='articles.php'?'active':'' ?>">📰 Articles</a>
    <a href="<?= base_url('admin/messages.php') ?>" class="<?= $__here==='messages.php'?'active':'' ?>">✉️ Contact Messages</a>
    <a href="<?= base_url('admin/settings.php') ?>" class="<?= $__here==='settings.php'?'active':'' ?>">⚙️ Settings</a>
    <a href="<?= base_url('logout.php') ?>">🚪 Log out</a>
  </nav>
</aside>
