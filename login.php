
<div class="auth-wrap">
  <div class="auth-visual">
    <div>
      <div class="brand" style="color:#fff;"><span class="brand-mark">🌾</span> <span class="brand-name"><?= e(get_setting('site_name')) ?></span></div>
    </div>
    <div>
      <h1>Welcome back to the field.</h1>
      <p>Log in to check live sensor readings, run new crop recommendations, and review your recommendation history.</p>
      <div class="auth-stats">
        <div><strong><?= (int)getDB()->query("SELECT COUNT(*) c FROM crops")->fetch()['c'] ?></strong><span>Crops profiled</span></div>
        <div><strong><?= (int)getDB()->query("SELECT COUNT(*) c FROM users")->fetch()['c'] ?></strong><span>Growers onboard</span></div>
        <div><strong><?= (int)getDB()->query("SELECT COUNT(*) c FROM recommendation_history")->fetch()['c'] ?></strong><span>Recommendations run</span></div>
      </div>
    </div>
  </div>
  <div class="auth-form-wrap">
    <div class="auth-card">
      <h2>Log in</h2>
      <p style="margin-top:-8px;margin-bottom:24px;">Enter your details to access your dashboard.</p>
      <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
      <form method="POST">
        <div class="field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@example.com">
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required placeholder="••••••••">
        </div>
        <button class="btn btn-block" type="submit">Log in</button>
      </form>
      <p class="muted-link">Don't have an account? <a href="register.php">Register here</a></p>
      
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
