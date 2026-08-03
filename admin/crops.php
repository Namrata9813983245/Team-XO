<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$__u = current_user();
$db = getDB();

$error = null; $success = null;
$editCrop = null;

// Delete
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("SELECT image FROM crops WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    if ($row = $stmt->fetch()) {
        if ($row['image'] && strpos($row['image'], 'assets/uploads') !== false) {
            @unlink(__DIR__ . '/../' . parse_url($row['image'], PHP_URL_PATH));
        }
        $db->prepare("DELETE FROM crops WHERE id=?")->execute([$_GET['delete']]);
        flash('success', 'Crop deleted.');
    }
    header('Location: crops.php'); exit;
}

// Load for edit
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM crops WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editCrop = $stmt->fetch();
}
// Save (create or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $soil = trim($_POST['soil_type'] ?? '');
    $minT = floatval($_POST['min_temp']); $maxT = floatval($_POST['max_temp']);
    $minH = floatval($_POST['min_humidity']); $maxH = floatval($_POST['max_humidity']);
    $minM = floatval($_POST['min_moisture']); $maxM = floatval($_POST['max_moisture']);
    $season = trim($_POST['season'] ?? 'Any');
    $desc = trim($_POST['description'] ?? '');

    if ($name === '' || $soil === '') {
        $error = 'Crop name and soil type are required.';
    } else {
        $uploadedImage = handle_image_upload('image', __DIR__ . '/../assets/uploads/crops', 'crop');
        if ($id) {
            if ($uploadedImage) {
                $stmt = $db->prepare("UPDATE crops SET name=?,image=?,soil_type=?,min_temp=?,max_temp=?,min_humidity=?,max_humidity=?,min_moisture=?,max_moisture=?,season=?,description=? WHERE id=?");
                $stmt->execute([$name, base_url('assets/uploads/crops/'.$uploadedImage), $soil, $minT, $maxT, $minH, $maxH, $minM, $maxM, $season, $desc, $id]);
            } else {
                $stmt = $db->prepare("UPDATE crops SET name=?,soil_type=?,min_temp=?,max_temp=?,min_humidity=?,max_humidity=?,min_moisture=?,max_moisture=?,season=?,description=? WHERE id=?");
                $stmt->execute([$name, $soil, $minT, $maxT, $minH, $maxH, $minM, $maxM, $season, $desc, $id]);
            }
            $success = 'Crop updated successfully.';
        } else {
            $image = $uploadedImage ? base_url('assets/uploads/crops/'.$uploadedImage) : 'https://loremflickr.com/500/350/'.urlencode(strtolower($name)).',crop?lock='.crc32($name);
            $stmt = $db->prepare("INSERT INTO crops (name,image,soil_type,min_temp,max_temp,min_humidity,max_humidity,min_moisture,max_moisture,season,description) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$name, $image, $soil, $minT, $maxT, $minH, $maxH, $minM, $maxM, $season, $desc]);
            $success = 'Crop added successfully.';
        }
        $editCrop = null;
    }
}


$crops = $db->query("SELECT * FROM crops ORDER BY name ASC")->fetchAll();
$soilOptions = ['Loamy','Sandy','Clay','Silty','Peaty','Saline','Black','Red'];

$pageTitle = 'Manage Crops';
require __DIR__ . '/../includes/header.php';
?>
<div class="dash-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head"><div><h1>Manage Crops</h1><p style="margin:4px 0 0;color:var(--ink-soft);">Add, edit, or remove crops used by the recommendation engine.</p></div></div>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
      <h3><?= $editCrop ? 'Edit ' . e($editCrop['name']) : 'Add a new crop' ?></h3>
      <form method="POST" enctype="multipart/form-data">
        <?php if ($editCrop): ?><input type="hidden" name="id" value="<?= (int)$editCrop['id'] ?>"><?php endif; ?>
        <div class="field-row">
          <div class="field"><label for="name">Crop name</label><input type="text" id="name" name="name" required value="<?= e($editCrop['name'] ?? '') ?>"></div>
          <div class="field">
            <label for="soil_type">Ideal soil type</label>
            <select id="soil_type" name="soil_type" required>
              <?php foreach ($soilOptions as $s): ?>
                <option value="<?= e($s) ?>" <?= (($editCrop['soil_type'] ?? '') === $s) ? 'selected' : '' ?>><?= e($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="field-row">
          <div class="field"><label for="min_temp">Min temperature (°C)</label><input type="number" step="0.1" id="min_temp" name="min_temp" required value="<?= e($editCrop['min_temp'] ?? '') ?>"></div>
          <div class="field"><label for="max_temp">Max temperature (°C)</label><input type="number" step="0.1" id="max_temp" name="max_temp" required value="<?= e($editCrop['max_temp'] ?? '') ?>"></div>
        </div>
        <div class="field-row">
          <div class="field"><label for="min_humidity">Min humidity (%)</label><input type="number" step="0.1" id="min_humidity" name="min_humidity" required value="<?= e($editCrop['min_humidity'] ?? '') ?>"></div>
          <div class="field"><label for="max_humidity">Max humidity (%)</label><input type="number" step="0.1" id="max_humidity" name="max_humidity" required value="<?= e($editCrop['max_humidity'] ?? '') ?>"></div>
        </div>
        <div class="field-row">
          <div class="field"><label for="min_moisture">Min soil moisture (%)</label><input type="number" step="0.1" id="min_moisture" name="min_moisture" required value="<?= e($editCrop['min_moisture'] ?? '') ?>"></div>
          <div class="field"><label for="max_moisture">Max soil moisture (%)</label><input type="number" step="0.1" id="max_moisture" name="max_moisture" required value="<?= e($editCrop['max_moisture'] ?? '') ?>"></div>
        </div>
        <div class="field-row">
          <div class="field"><label for="season">Growing season</label><input type="text" id="season" name="season" value="<?= e($editCrop['season'] ?? 'Any') ?>"></div>
          <div class="field"><label for="image">Crop photo <span style="font-weight:400;color:var(--ink-soft);">(optional — a stock photo is used if left blank)</span></label><input type="file" id="image" name="image" accept="image/*"></div>
        </div>
        <div class="field"><label for="description">Description</label><textarea id="description" name="description" rows="2"><?= e($editCrop['description'] ?? '') ?></textarea></div>
        <?php if ($editCrop): ?><img src="<?= e($editCrop['image']) ?>" class="crop-image-upload-preview" style="max-width:220px;" alt=""><?php endif; ?>
        <div style="margin-top:16px;display:flex;gap:10px;">
          <button class="btn" type="submit"><?= $editCrop ? 'Save changes' : 'Add crop' ?></button>
          <?php if ($editCrop): ?><a href="crops.php" class="btn btn-outline">Cancel</a><?php endif; ?>
        </div>
      </form>
    </div>

    <div class="card">
      <h3>All crops (<?= count($crops) ?>)</h3>
      <table class="data-table">
        <thead><tr><th>Photo</th><th>Name</th><th>Soil</th><th>Temp °C</th><th>Humidity %</th><th>Moisture %</th><th>Season</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($crops as $c): ?>
          <tr>
            <td><img src="<?= e($c['image']) ?>" class="table-thumb" alt=""></td>
            <td><strong><?= e($c['name']) ?></strong></td>
            <td><span class="badge badge-user"><?= e($c['soil_type']) ?></span></td>
            <td><?= e($c['min_temp']) ?>–<?= e($c['max_temp']) ?></td>
            <td><?= e($c['min_humidity']) ?>–<?= e($c['max_humidity']) ?></td>
            <td><?= e($c['min_moisture']) ?>–<?= e($c['max_moisture']) ?></td>
            <td><?= e($c['season']) ?></td>
            <td style="white-space:nowrap;">
              <a href="?edit=<?= $c['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
              <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete <?= e(addslashes($c['name'])) ?>?');">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
