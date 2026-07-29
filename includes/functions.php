<?php
require_once __DIR__ . '/../config/db.php';

function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

// Truncate text without requiring the mbstring extension (not always enabled on shared hosts).
function trim_text($str, $len, $suffix = '…') {
    $str = (string)$str;
    if (function_exists('mb_strimwidth')) return mb_strimwidth($str, 0, $len, $suffix);
    return strlen($str) > $len ? substr($str, 0, $len) . $suffix : $str;
}

function current_user() {
    if (!isset($_SESSION['user_id'])) return null;
    static $u = null;
    if ($u === null) {
        $stmt = getDB()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $u = $stmt->fetch();
    }
    return $u;
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

function require_admin() {
    require_login();
    $u = current_user();
    if (!$u || $u['role'] !== 'admin') {
        header('Location: ' . base_url('user/dashboard.php'));
        exit;
    }
}

function base_url($path = '') {
    // Adjusts relative paths so links work whether we're in /, /user/, or /admin/
    $depth = defined('APP_DEPTH') ? APP_DEPTH : 0;
    return str_repeat('../', $depth) . $path;
}

function get_setting($key, $default = '') {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (getDB()->query("SELECT setting_key, setting_value FROM site_settings") as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache[$key] ?? $default;
}

function flash($key, $msg = null) {
    if ($msg !== null) { $_SESSION['flash'][$key] = $msg; return; }
    if (!empty($_SESSION['flash'][$key])) {
        $m = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $m;
    }
    return null;
}

/* ---------------- IoT simulated live sensor data ---------------- */
function get_live_sensor_reading($forceNew = false) {
    $db = getDB();
    if (!$forceNew) {
        $row = $db->query("SELECT * FROM iot_data ORDER BY id DESC LIMIT 1")->fetch();
        if ($row && (time() - strtotime($row['recorded_at'])) < 15) {
            return $row;
        }
    }
    // Simulate a plausible new sensor reading (in a real deployment this
    // would be inserted by the IoT gateway / MQTT bridge instead).
    $soils = ['Loamy', 'Sandy', 'Clay', 'Silty', 'Black', 'Red'];
    $temp = round(mt_rand(150, 380) / 10, 1);
    $hum  = round(mt_rand(300, 900) / 10, 1);
    $moist = round(mt_rand(200, 850) / 10, 1);
    $soil = $soils[array_rand($soils)];
    $stmt = $db->prepare("INSERT INTO iot_data (temperature,humidity,moisture,soil_type) VALUES (?,?,?,?)");
    $stmt->execute([$temp, $hum, $moist, $soil]);
    $id = $db->lastInsertId();
    $stmt = $db->prepare("SELECT * FROM iot_data WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/* ---------------- Dynamic recommendation fields ---------------- */
function get_active_fields() {
    return getDB()->query("SELECT * FROM recommendation_fields WHERE active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
}

/* ---------------- Rule-based recommendation engine ---------------- */
/**
 * Scores every crop against the submitted conditions using simple,
 * transparent IF/THEN range rules:
 *   - Soil type must match exactly            -> required (hard rule)
 *   - Temperature within [min_temp,max_temp]   -> +1, else partial credit by distance
 *   - Humidity within [min_humidity,max_humidity] -> +1
 *   - Moisture within [min_moisture,max_moisture] -> +1
 * Crops are ranked by total score; soil mismatch heavily penalizes.
 */
function recommend_crops(array $input, $limit = 3) {
    $crops = getDB()->query("SELECT * FROM crops")->fetchAll();
    $temp = isset($input['temperature']) ? floatval($input['temperature']) : null;
    $hum  = isset($input['humidity']) ? floatval($input['humidity']) : null;
    $moist = isset($input['moisture']) ? floatval($input['moisture']) : null;
    $soil = $input['soil_type'] ?? null;

    $results = [];
    foreach ($crops as $crop) {
        $score = 0;
        $max = 0;
        $reasons = [];

        if ($soil !== null) {
            $max += 2; // soil match is weighted heavier
            if (strcasecmp($crop['soil_type'], $soil) === 0) {
                $score += 2;
                $reasons[] = "Soil type matches ({$crop['soil_type']})";
            } else {
                $reasons[] = "Soil type differs (needs {$crop['soil_type']}, you have {$soil})";
            }
        }

        foreach ([
            ['temp', $temp, 'min_temp', 'max_temp', 'Temperature'],
            ['hum', $hum, 'min_humidity', 'max_humidity', 'Humidity'],
            ['moist', $moist, 'min_moisture', 'max_moisture', 'Moisture'],
        ] as [$k, $val, $minKey, $maxKey, $label]) {
            if ($val === null) continue;
            $max += 1;
            $min = $crop[$minKey]; $mx = $crop[$maxKey];
            if ($val >= $min && $val <= $mx) {
                $score += 1;
                $reasons[] = "$label $val is within ideal range ({$min}-{$mx})";
            } else {
                $range = $mx - $min;
                $dist = $val < $min ? $min - $val : $val - $mx;
                $partial = $range > 0 ? max(0, 1 - ($dist / $range)) * 0.5 : 0;
                $score += $partial;
                $reasons[] = "$label $val is outside ideal range ({$min}-{$mx})";
            }
        }

        $pct = $max > 0 ? round(($score / $max) * 100) : 0;
        $results[] = [
            'crop' => $crop,
            'score' => $score,
            'max' => $max,
            'percent' => $pct,
            'reasons' => $reasons,
        ];
    }

    usort($results, fn($a, $b) => $b['percent'] <=> $a['percent']);
    return array_slice($results, 0, $limit);
}

function handle_image_upload($fileField, $destDir, $prefix) {
    if (empty($_FILES[$fileField]['name'])) return null;
    $err = $_FILES[$fileField]['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($err !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($_FILES[$fileField]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) return null;
    $name = $prefix . '_' . uniqid() . '.' . $ext;
    $dest = rtrim($destDir, '/') . '/' . $name;
    if (move_uploaded_file($_FILES[$fileField]['tmp_name'], $dest)) {
        return $name;
    }
    return null;
}
