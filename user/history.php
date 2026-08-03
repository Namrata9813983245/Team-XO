
  <?php require __DIR__ . '/../includes/user_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head">
      <div><h1>Your History</h1><p style="margin:4px 0 0;color:var(--ink-soft);">Every recommendation you've run, most recent first.</p></div>
    </div>

    <?php if (!$history): ?>
      <div class="card empty-state">
        <div class="icon">🕘</div>
        <h3>No history yet</h3>
        <p>Once you run a recommendation, it'll show up here with the crop photo and the conditions you entered.</p>
        <a href="<?= base_url('user/recommend.php') ?>" class="btn" style="margin-top:14px;">Run your first recommendation</a>
      </div>
    <?php else: foreach ($history as $h): $inputs = json_decode($h['input_data'], true) ?: []; ?>
      <div class="history-card">
        <img src="<?= e($h['crop_image'] ?: 'https://loremflickr.com/120/120/plant?lock='.$h['id']) ?>" alt="">
        <div style="flex:1;">
          <h3 style="margin:0 0 4px;"><?= e($h['crop_name'] ?? 'No strong match found') ?></h3>
          <p style="margin:0;font-size:.85rem;">
            <?php
              $parts = [];
              foreach ($inputs as $k => $v) { $parts[] = ucfirst(str_replace('_',' ',$k)) . ': ' . e($v); }
              echo implode(' · ', $parts);
            ?>
          </p>
          <p style="margin:4px 0 0;font-size:.78rem;color:var(--ink-soft);"><?= e(date('M j, Y g:i a', strtotime($h['created_at']))) ?></p>
        </div>
        <?php if ($h['match_score'] !== null): ?>
          <span class="badge badge-active"><?= (int)$h['match_score'] ?>% match</span>
        <?php endif; ?>
      </div>
    <?php endforeach; endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
