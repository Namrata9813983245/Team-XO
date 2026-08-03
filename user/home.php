<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';
require_login();
$__u = current_user();
if ($__u['role'] === 'admin') { header('Location: ../admin/dashboard.php'); exit; }

$reading = get_live_sensor_reading();
$articles = getDB()->query("SELECT * FROM articles ORDER BY id DESC")->fetchAll();
$pageTitle = 'Home';
require __DIR__ . '/../includes/header.php';
?>
<div class="dash-shell">
  <?php require __DIR__ . '/../includes/user_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head">
      <div>
        <h1>Welcome back, <?= e(explode(' ', $__u['name'])[0]) ?> 👋</h1>
        <p style="margin:4px 0 0;color:var(--ink-soft);">Here's what your field sensors are reporting right now.</p>
      </div>
      <a href="<?= base_url('user/recommend.php') ?>" class="btn">Run a recommendation →</a>
    </div>

    <div class="home-split">
      <!-- LEFT HALF: live IoT sensor data -->
      <div class="live-panel">
        <div class="live-panel-head">
          <h3><span class="live-dot"></span> Live sensor feed</h3>
          <button onclick="fetchLiveReading('<?= base_url('api/live_data.php') ?>?refresh=1').then(updateLiveDisplays)" class="btn btn-sm btn-wheat">Refresh now</button>
        </div>
        <div class="gauge-row">
          <div class="gauge">
            <svg viewBox="0 0 130 130">
              <circle class="gauge-track" cx="65" cy="65" r="52"></circle>
              <circle class="gauge-fill temp" data-live-gauge="temperature" data-max="50" data-percent="<?= min(100, ($reading['temperature']/50)*100) ?>" cx="65" cy="65" r="52" transform="rotate(-90 65 65)"></circle>
              <text x="65" y="60" text-anchor="middle" class="gauge-val" data-live="temperature"><?= number_format($reading['temperature'],1) ?></text>
              <text x="65" y="78" text-anchor="middle" style="fill:rgba(255,255,255,.6);font-size:.6rem;">°C</text>
            </svg>
            <div class="gauge-label">Temperature</div>
          </div>
          <div class="gauge">
            <svg viewBox="0 0 130 130">
              <circle class="gauge-track" cx="65" cy="65" r="52"></circle>
              <circle class="gauge-fill hum" data-live-gauge="humidity" data-max="100" data-percent="<?= $reading['humidity'] ?>" cx="65" cy="65" r="52" transform="rotate(-90 65 65)"></circle>
              <text x="65" y="60" text-anchor="middle" class="gauge-val" data-live="humidity"><?= number_format($reading['humidity'],1) ?></text>
              <text x="65" y="78" text-anchor="middle" style="fill:rgba(255,255,255,.6);font-size:.6rem;">%</text>
            </svg>
            <div class="gauge-label">Humidity</div>
          </div>
          <div class="gauge">
            <svg viewBox="0 0 130 130">
              <circle class="gauge-track" cx="65" cy="65" r="52"></circle>
              <circle class="gauge-fill moist" data-live-gauge="moisture" data-max="100" data-percent="<?= $reading['moisture'] ?>" cx="65" cy="65" r="52" transform="rotate(-90 65 65)"></circle>
              <text x="65" y="60" text-anchor="middle" class="gauge-val" data-live="moisture"><?= number_format($reading['moisture'],1) ?></text>
              <text x="65" y="78" text-anchor="middle" style="fill:rgba(255,255,255,.6);font-size:.6rem;">%</text>
            </svg>
            <div class="gauge-label">Moisture</div>
          </div>
        </div>
        <div style="text-align:center;">
          <span class="live-soil-tag">🌱 Detected soil type: <strong data-live="soil_type"><?= e($reading['soil_type']) ?></strong></span>
        </div>
        <div class="live-meta" id="live-timestamp">Last synced <?= e(date('g:i:s a', strtotime($reading['recorded_at']))) ?></div>
      </div>

      <!-- RIGHT HALF: photographs + real articles -->
      <div>
        <div class="photo-strip">
          <img src="https://loremflickr.com/500/700/farmer,sunrise?lock=11" alt="Farmer at sunrise">
          <img src="https://loremflickr.com/500/340/irrigation,field?lock=12" alt="Irrigation">
          <img src="https://loremflickr.com/500/340/harvest,crop?lock=13" alt="Harvest">
        </div>
        <h3 style="margin-bottom:14px;">Field notes &amp; articles</h3>
        <?php foreach ($articles as $a): ?>
          <div class="article-card">
            <img src="<?= e($a['image']) ?>" alt="<?= e($a['title']) ?>">
            <div>
              <h4><?= e($a['title']) ?></h4>
              <p><?= e(trim_text($a['content'], 120)) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<script>initLivePanel('<?= base_url('api/live_data.php') ?>', 8000);</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
