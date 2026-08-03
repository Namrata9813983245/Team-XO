
<div class="dash-shell">
  <?php require __DIR__ . '/../includes/user_sidebar.php'; ?>
  <div class="dash-main">
    <div class="dash-head">
      <div>
        <h1>Recommend a Crop</h1>
        <p style="margin:4px 0 0;color:var(--ink-soft);">Enter your field conditions, or load them straight from your live sensors.</p>
      </div>
    </div>

    <div class="recommend-layout">
      <div>
        <form method="POST" class="card">
          <!-- Live data loader (autofills the fields below) -->
          <div class="live-load-bar">
            <div class="llb-text">📡 Have live sensor data?<small>One click fills in temperature, humidity, moisture &amp; soil type.</small></div>
            <button type="button" class="btn btn-sm btn-wheat" onclick="loadLiveIntoForm('<?= base_url('api/live_data.php') ?>?refresh=1', this)">Load Live Sensor Data</button>
          </div>

          <?php foreach ($fields as $f): ?>
            <div class="field">
              <label for="f_<?= e($f['field_key']) ?>"><?= e($f['label']) ?><?= $f['unit'] ? ' (' . e($f['unit']) . ')' : '' ?></label>
              <?php if ($f['field_type'] === 'select'): ?>
                <select id="f_<?= e($f['field_key']) ?>" name="<?= e($f['field_key']) ?>" required>
                  <option value="">Select <?= e(strtolower($f['label'])) ?>…</option>
                  <?php foreach (explode(',', $f['options'] ?? '') as $opt):
                    $opt = trim($opt); if ($opt === '') continue;
                    $sel = (($submittedInput[$f['field_key']] ?? '') === $opt) ? 'selected' : ''; ?>
                    <option value="<?= e($opt) ?>" <?= $sel ?>><?= e($opt) ?></option>
                  <?php endforeach; ?>
                </select>
              <?php else: ?>
                <input type="number" step="0.1" id="f_<?= e($f['field_key']) ?>" name="<?= e($f['field_key']) ?>"
                       min="<?= e($f['min_value']) ?>" max="<?= e($f['max_value']) ?>" required
                       value="<?= e($submittedInput[$f['field_key']] ?? '') ?>"
                       placeholder="e.g. <?= e(($f['min_value']+$f['max_value'])/2) ?>">
                <?php if ($f['min_value'] !== null): ?>
                  <div class="field-hint">Typical range: <?= e($f['min_value']) ?>–<?= e($f['max_value']) ?> <?= e($f['unit']) ?></div>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>

          <button type="submit" class="btn btn-block">🔍 Get Recommendation</button>
        </form>
      </div>

      <div>
        <?php if ($results): ?>
          <?php $top = $results[0]; ?>
          <div class="best-pick-banner">
            <img src="<?= e($top['crop']['image']) ?>" alt="<?= e($top['crop']['name']) ?>">
            <div>
              <span class="crop-tag" style="background:rgba(255,255,255,.5);">Best match · <?= $top['percent'] ?>%</span>
              <h2 style="margin:4px 0 0;font-size:1.6rem;"><?= e($top['crop']['name']) ?></h2>
              <p style="margin:4px 0 0;color:var(--forest-dark);"><?= e($top['crop']['description']) ?></p>
            </div>
          </div>

          <h3>All ranked matches</h3>
          <?php foreach ($results as $i => $r): $c = $r['crop']; $circumf = 2*M_PI*26; ?>
            <div class="match-card <?= $i===0?'top':'' ?>">
              <img src="<?= e($c['image']) ?>" alt="<?= e($c['name']) ?>">
              <div style="flex:1;">
                <h3 style="margin:0;"><?= e($c['name']) ?> <span style="font-size:.8rem;font-weight:400;color:var(--ink-soft);">· <?= e($c['soil_type']) ?> soil · <?= e($c['season']) ?> season</span></h3>
                <ul class="match-reasons">
                  <?php foreach (array_slice($r['reasons'],0,3) as $reason): ?><li><?= e($reason) ?></li><?php endforeach; ?>
                </ul>
              </div>
              <div class="match-ring">
                <svg viewBox="0 0 60 60">
                  <circle class="bg" cx="30" cy="30" r="26"></circle>
                  <circle class="fg" cx="30" cy="30" r="26" data-percent="<?= $r['percent'] ?>"></circle>
                </svg>
                <div class="pct"><?= $r['percent'] ?>%</div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="card empty-state">
            <div class="icon">🌾</div>
            <h3>No recommendation yet</h3>
            <p>Fill in the form (or load live sensor data) and submit to see your ranked crop matches with photos and reasoning.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
