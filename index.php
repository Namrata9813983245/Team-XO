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