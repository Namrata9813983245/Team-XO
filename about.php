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