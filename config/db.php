<?php
/**
 * Database connection
 * ---------------------------------------------------------
 * Uses SQLite (file-based) so the site runs immediately with
 * zero server setup. To use MySQL instead, just replace the
 * DSN below with:
 *   new PDO("mysql:host=localhost;dbname=cropsys;charset=utf8mb4", $user, $pass)
 * All the rest of the code (prepared statements) stays the same.
 */

define('DB_FILE', __DIR__ . '/../data/cropsys.sqlite');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $isNew = !file_exists(DB_FILE);
        $pdo = new PDO('sqlite:' . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON;');
        if ($isNew) {
            require __DIR__ . '/schema.php';
            install_schema($pdo);
        }
    }
    return $pdo;
}
