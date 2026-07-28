<?php
session_start();
define('APP_DEPTH', 1);
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');
if (!current_user()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$forceNew = isset($_GET['refresh']);
$reading = get_live_sensor_reading($forceNew);
echo json_encode([
    'temperature' => (float)$reading['temperature'],
    'humidity' => (float)$reading['humidity'],
    'moisture' => (float)$reading['moisture'],
    'soil_type' => $reading['soil_type'],
    'recorded_at' => $reading['recorded_at'],
]);
