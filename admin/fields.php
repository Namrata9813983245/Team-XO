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
