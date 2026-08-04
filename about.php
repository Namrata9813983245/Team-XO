<?php
session_start();
define('APP_DEPTH', 0);
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About Us';
require __DIR__ . '/includes/header.php';
?>
<section class="about-hero">
  <div class="container">
    <span class="eyebrow" style="background:rgba(255,255,255,.15);color:#fff;">About Us</span>
    <h1>Agronomy rules you can actually see</h1>
    <p><?= nl2br(e(get_setting('about_us'))) ?></p>
  </div>
</section>

<section class="section">
  <div class="container grid-3">
    <div class="feature-card">
      <div class="icon">🎯</div>
      <h3>Our mission</h3>
      <p>Put transparent, explainable crop guidance in the hands of every grower — not just those with access to expensive agronomists.</p>
    </div>
    <div class="feature-card">
      <div class="icon">🧩</div>
      <h3>How the rules work</h3>
      <p>Each crop has a defined soil type and ideal temperature, humidity and moisture range. We score your live conditions against every crop and rank the closest matches.</p>
    </div>
    <div class="feature-card">
      <div class="icon">🔧</div>
      <h3>Always improving</h3>
      <p>Administrators continually refine crop profiles and recommendation criteria as regional agronomy knowledge grows.</p>
    </div>
  </div>
</section>

<section class="section" style="background:var(--bg-soft);">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Our team's promise</span>
      <h2>No black boxes, ever</h2>
      <p>We believe farmers deserve to know exactly why a crop is being recommended. That's why AgroSense is built on clear, editable IF/THEN rules rather than an opaque model — every result comes with a plain-language reason.</p>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
