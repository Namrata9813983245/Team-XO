// Animate any circular gauge/ring on the page based on its data-percent attribute.
function animateRings() {
  document.querySelectorAll('[data-percent]').forEach(function (circle) {
    const pct = Math.max(0, Math.min(100, parseFloat(circle.dataset.percent)));
    const r = circle.r.baseVal.value;
    const c = 2 * Math.PI * r;
    circle.style.strokeDasharray = c;
    circle.style.strokeDashoffset = c;
    requestAnimationFrame(() => {
      circle.style.strokeDashoffset = c - (pct / 100) * c;
    });
  });
}
document.addEventListener('DOMContentLoaded', animateRings);

// Fetch a simulated / live IoT reading from the server and update any
// elements on the page tagged with data-live="temperature|humidity|moisture|soil_type".
async function fetchLiveReading(apiUrl) {
  const res = await fetch(apiUrl, { cache: 'no-store' });
  if (!res.ok) throw new Error('Live data request failed');
  return res.json();
}

function updateLiveDisplays(data) {
  document.querySelectorAll('[data-live]').forEach(function (el) {
    const key = el.dataset.live;
    if (data[key] === undefined) return;
    el.textContent = (typeof data[key] === 'number') ? data[key].toFixed(1) : data[key];
  });
  document.querySelectorAll('[data-live-gauge]').forEach(function (el) {
    const key = el.dataset.liveGauge;
    const max = parseFloat(el.dataset.max || '100');
    if (data[key] === undefined) return;
    const pct = Math.max(0, Math.min(100, (data[key] / max) * 100));
    el.dataset.percent = pct;
  });
  animateRings();
  const ts = document.getElementById('live-timestamp');
  if (ts) ts.textContent = 'Last synced ' + new Date().toLocaleTimeString();
}

function initLivePanel(apiUrl, intervalMs) {
  const load = () => fetchLiveReading(apiUrl).then(updateLiveDisplays).catch(() => {});
  load();
  if (intervalMs) setInterval(load, intervalMs);
}

// Used on the recommendation form: "Load Live Sensor Data" button fills the form.
function loadLiveIntoForm(apiUrl, btn) {
  const originalText = btn.textContent;
  btn.textContent = 'Loading…';
  btn.disabled = true;
  fetchLiveReading(apiUrl).then(function (data) {
    const tempEl = document.querySelector('[name="temperature"]');
    const humEl = document.querySelector('[name="humidity"]');
    const moistEl = document.querySelector('[name="moisture"]');
    const soilEl = document.querySelector('[name="soil_type"]');
    if (tempEl) tempEl.value = data.temperature;
    if (humEl) humEl.value = data.humidity;
    if (moistEl) moistEl.value = data.moisture;
    if (soilEl) soilEl.value = data.soil_type;
    [tempEl, humEl, moistEl, soilEl].forEach(function (el) {
      if (!el) return;
      el.classList.add('field-flash');
      setTimeout(() => el.classList.remove('field-flash'), 900);
    });
    btn.textContent = '✓ Live data loaded';
    setTimeout(() => { btn.textContent = originalText; btn.disabled = false; }, 1500);
  }).catch(function () {
    btn.textContent = 'Failed — try again';
    setTimeout(() => { btn.textContent = originalText; btn.disabled = false; }, 1500);
  });
}
