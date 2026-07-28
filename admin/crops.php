
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
