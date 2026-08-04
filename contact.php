<?php
session_start();
define('APP_DEPTH', 0);
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $email && $message) {
        $stmt = getDB()->prepare("INSERT INTO contact_messages (name,email,message) VALUES (?,?,?)");
        $stmt->execute([$name, $email, $message]);
        flash('contact_success', 'Thanks, ' . $name . '! Your message has been received — we\'ll get back to you soon.');
    } else {
        flash('contact_error', 'Please fill in every field before sending.');
    }
}
$back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: ' . $back . '#contact-us');
exit;