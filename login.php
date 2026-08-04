<?php
session_start();
define('APP_DEPTH', 0);
require_once __DIR__ . '/includes/functions.php';

if (current_user()) {
    $u = current_user();
    header('Location: ' . ($u['role'] === 'admin' ? 'admin/dashboard.php' : 'user/home.php'));
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = getDB()->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        // Role-based redirect: admins go to the admin dashboard, everyone else to the user dashboard.
        header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/home.php'));
        exit;
    } else {
        $error = 'Incorrect email or password. Please try again.';
    }
}

$pageTitle = 'Log in';
require __DIR__ . '/includes/header.php';
?>
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
