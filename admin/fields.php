<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$__u = current_user();
$db = getDB();
$error = null; $success = null;

if (isset($_GET['toggle'])) {
    $db->prepare("UPDATE recommendation_fields SET active = 1 - active WHERE id=?")->execute([$_GET['toggle']]);
    header('Location: fields.php'); exit;
}
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("SELECT is_core FROM recommendation_fields WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    $row = $stmt->fetch();
    if ($row && !$row['is_core']) {
        $db->prepare("DELETE FROM recommendation_fields WHERE id=?")->execute([$_GET['delete']]);
        flash('success', 'Field removed.');
    } else {
        flash('error', 'Core fields (used by the rule engine) can be deactivated but not deleted.');
    }
    header('Location: fields.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = trim($_POST['field_key'] ?? '');
    $key = preg_replace('/[^a-z0-9_]/', '', strtolower(str_replace(' ', '_', $key)));
    $label = trim($_POST['label'] ?? '');
    $type = $_POST['field_type'] ?? 'number';
    $unit = trim($_POST['unit'] ?? '');
    $min = $_POST['min_value'] !== '' ? floatval($_POST['min_value']) : null;
    $max = $_POST['max_value'] !== '' ? floatval($_POST['max_value']) : null;
    $options = trim($_POST['options'] ?? '');

    if ($key === '' || $label === '') {
        $error = 'Field key and label are required.';
    } else {
        $stmt = $db->prepare("SELECT id FROM recommendation_fields WHERE field_key=?");
        $stmt->execute([$key]);
        if ($stmt->fetch()) {
            $error = 'A field with that key already exists.';
        } else {
            $order = (int)$db->query("SELECT COALESCE(MAX(sort_order),0)+1 n FROM recommendation_fields")->fetch()['n'];
            $stmt = $db->prepare("INSERT INTO recommendation_fields (field_key,label,field_type,unit,min_value,max_value,options,is_core,active,sort_order) VALUES (?,?,?,?,?,?,?,0,1,?)");
            $stmt->execute([$key, $label, $type, $unit, $min, $max, $options ?: null, $order]);
            $success = 'New field added to the recommendation form.';
        }
    }
}

$fields = $db->query("SELECT * FROM recommendation_fields ORDER BY sort_order ASC, id ASC")->fetchAll();
$pageTitle = 'Recommendation Fields';
require __DIR__ . '/../includes/header.php';
?>
<div class="dash-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head"><div><h1>Recommendation Fields</h1><p style="margin:4px 0 0;color:var(--ink-soft);">Control exactly which inputs appear on the "Recommend a Crop" form.</p></div></div>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <?php if ($m = flash('error')): ?><div class="alert alert-error"><?= e($m) ?></div><?php endif; ?>
    <?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>

    <div class="card">
      <h3>Add a new field</h3>
      <p style="margin-top:-8px;">Core fields (soil type, temperature, humidity, moisture) power the rule engine's scoring. Custom fields you add here appear on the form for context, but only core fields are currently scored automatically.</p>
      <form method="POST">
        <div class="field-row">
          <div class="field"><label for="field_key">Field key</label><input type="text" id="field_key" name="field_key" required placeholder="e.g. rainfall"></div>
          <div class="field"><label for="label">Display label</label><input type="text" id="label" name="label" required placeholder="e.g. Rainfall"></div>
        </div>
        <div class="field-row">
          <div class="field">
            <label for="field_type">Input type</label>
            <select id="field_type" name="field_type">
              <option value="number">Number</option>
              <option value="select">Dropdown</option>
            </select>
          </div>
          <div class="field"><label for="unit">Unit</label><input type="text" id="unit" name="unit" placeholder="e.g. mm"></div>
        </div>
        <div class="field-row">
          <div class="field"><label for="min_value">Min value (number fields)</label><input type="number" step="0.1" id="min_value" name="min_value"></div>
          <div class="field"><label for="max_value">Max value (number fields)</label><input type="number" step="0.1" id="max_value" name="max_value"></div>
        </div>
        <div class="field"><label for="options">Dropdown options (comma-separated, dropdown fields only)</label><input type="text" id="options" name="options" placeholder="e.g. Low,Medium,High"></div>
        <button class="btn" type="submit">+ Add field</button>
      </form>
    </div>
    
    <div class="card">
      <h3>All fields</h3>
      <table class="data-table">
        <thead><tr><th>Key</th><th>Label</th><th>Type</th><th>Range / Options</th><th>Core</th><th>Active</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($fields as $f): ?>
          <tr>
            <td class="mono"><?= e($f['field_key']) ?></td>
            <td><?= e($f['label']) ?></td>
            <td><?= e($f['field_type']) ?></td>
            <td><?= $f['field_type']==='select' ? e($f['options']) : e($f['min_value']).'–'.e($f['max_value']).' '.e($f['unit']) ?></td>
            <td><?= $f['is_core'] ? '<span class="badge badge-admin">Core</span>' : '<span class="badge badge-user">Custom</span>' ?></td>
            <td>
              <label class="switch">
                <input type="checkbox" onchange="window.location='?toggle=<?= $f['id'] ?>'" <?= $f['active'] ? 'checked' : '' ?>>
                <span class="slider-toggle"></span>
              </label>
            </td>
            <td>
              <?php if (!$f['is_core']): ?>
                <a href="?delete=<?= $f['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this field?');">Delete</a>
              <?php else: ?>
                <span style="font-size:.78rem;color:var(--ink-soft);">protected</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
