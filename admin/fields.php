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