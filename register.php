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
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $location = trim($_POST['location'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill in all required fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = getDB()->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'An account with that email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = getDB()->prepare("INSERT INTO users (name,email,password,role,location) VALUES (?,?,?,'user',?)");
            $stmt->execute([$name, $email, $hash, $location]);
            $_SESSION['user_id'] = getDB()->lastInsertId();
            header('Location: user/home.php');
            exit;
        }
    }
}

$pageTitle = 'Register';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-visual">
    <div class="brand" style="color:#fff;"><span class="brand-mark">🌾</span> <span class="brand-name"><?= e(get_setting('site_name')) ?></span></div>
    <div>
      <h1>Join growers using data, not guesswork.</h1>
      <p>Register in under a minute to unlock live sensor tracking and instant, rule-based crop recommendations for your field.</p>
    </div>
  </div>
  <div class="auth-form-wrap">
    <div class="auth-card">
      <h2>Create your account</h2>
      <p style="margin-top:-8px;margin-bottom:24px;">It's free — new accounts are set up as standard users.</p>
      <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
      <form method="POST">
        <div class="field">
          <label for="name">Full name</label>
          <input type="text" id="name" name="name" required value="<?= e($_POST['name'] ?? '') ?>" placeholder="Jane Farmer">
        </div>
        <div class="field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@example.com">
        </div>
        <div class="field">
          <label for="location">Farm location <span style="font-weight:400;color:var(--ink-soft);">(optional)</span></label>
          <input type="text" id="location" name="location" value="<?= e($_POST['location'] ?? '') ?>" placeholder="Kathmandu, Nepal">
        </div>
        <div class="field-row">
          <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="At least 6 characters">
          </div>
          <div class="field">
            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repeat password">
          </div>
        </div>
        <button class="btn btn-block" type="submit">Create account</button>
      </form>
      <p class="muted-link">Already have an account? <a href="login.php">Log in</a></p>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
