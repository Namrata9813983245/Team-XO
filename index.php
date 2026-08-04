<?php
session_start();
define('APP_DEPTH', 0);
require_once __DIR__ . '/includes/functions.php';
if (current_user()) {
    $u = current_user();
    header('Location: ' . ($u['role'] === 'admin' ? 'admin/dashboard.php' : 'user/home.php'));
    exit;
}
$pageTitle = 'Smarter crop decisions, grounded in your soil';
$crops = getDB()->query("SELECT * FROM crops ORDER BY id LIMIT 8")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="container hero-inner">
    <div>
      <span class="eyebrow">🌱 Rule-based crop intelligence</span>
      <h1>Know your soil.<br>Grow the right crop.</h1>
      <p class="lead">AgroSense reads live soil moisture, temperature and humidity from your field sensors and matches them against transparent, editable agronomy rules — so you always know <em>why</em> a crop was recommended.</p>
      <div class="hero-cta-row">
        <a href="register.php" class="btn">Create free account</a>
        <a href="#how" class="btn btn-outline">See how it works</a>
      </div>
    </div>
    <div class="hero-art">
      <img src="https://loremflickr.com/700/560/farm,field,green?lock=42" alt="Green farmland">
      <div class="float-card">
        <span class="dot"></span>
        <div>
          <strong style="display:block;font-family:var(--font-mono);font-size:1.1rem;">Live sync active</strong>
          <span style="font-size:.78rem;color:var(--ink-soft);">Sensors reporting every few seconds</span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section" id="how">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">How it works</span>
      <h2>From sensor reading to recommendation in four steps</h2>
      <p>No black-box machine learning — every recommendation traces back to a rule you (or your admin) can inspect and tune.</p>
    </div>
    <div class="step-row">
      <div class="step"><span class="step-num">01</span><h3>Sync sensors</h3><p>Pull live temperature, humidity and soil moisture straight from your field's IoT devices.</p></div>
      <div class="step"><span class="step-num">02</span><h3>Set soil type</h3><p>Choose your soil type — loamy, sandy, clay, and more — the single biggest driver of crop suitability.</p></div>
      <div class="step"><span class="step-num">03</span><h3>Run the rules</h3><p>Our rule engine scores every crop in the catalog against your exact conditions.</p></div>
      <div class="step"><span class="step-num">04</span><h3>Get a ranked match</h3><p>See your best-fit crop plus runners-up, each with a plain-language reason.</p></div>
    </div>
  </div>
</section>

<section class="section" style="background:var(--bg-soft);">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Why AgroSense</span>
      <h2>Built for real fields, not spreadsheets</h2>
    </div>
    <div class="grid-3">
      <div class="feature-card"><div class="icon">📡</div><h3>Live IoT dashboard</h3><p>Watch temperature, humidity and soil moisture update in real time, right on your home screen.</p></div>
      <div class="feature-card"><div class="icon">🧠</div><h3>Transparent rules</h3><p>Every recommendation shows its reasoning — no mystery scores, just clear range comparisons.</p></div>
      <div class="feature-card"><div class="icon">🛠️</div><h3>Configurable fields</h3><p>Admins can add, edit or retire recommendation criteria as agronomy needs change.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Crop catalog</span>
      <h2>A growing library of crop profiles</h2>
      <p>Each crop carries ideal soil, temperature, humidity and moisture ranges — maintained by your admin team.</p>
    </div>
    <div class="grid-4">
      <?php foreach ($crops as $c): ?>
        <div class="crop-card">
          <img src="<?= e($c['image']) ?>" alt="<?= e($c['name']) ?>">
          <div class="crop-body">
            <span class="crop-tag"><?= e($c['soil_type']) ?> soil</span>
            <h3 style="margin:0;font-size:1.05rem;"><?= e($c['name']) ?></h3>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="cta-band">
    <h2>Ready to see what fits your field?</h2>
    <p>Create a free account, connect your sensor readings, and get your first crop recommendation in under a minute.</p>
    <a href="register.php" class="btn btn-wheat">Get started free</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>