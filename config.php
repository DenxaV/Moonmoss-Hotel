<?php
// Database configuration (SQLite - file stored locally)
$dbFile = __DIR__ . DIRECTORY_SEPARATOR . 'database.sqlite';

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Initialize schema if not exists
try {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            email TEXT UNIQUE,
            password_hash TEXT NOT NULL,
            gender TEXT,
            origin TEXT,
            fairy_type TEXT,
            coins INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )'
    );
} catch (PDOException $e) {
    // If schema init fails, surface a clear message
    die('Database initialization failed: ' . $e->getMessage());
}

// Password hashing configuration
define('PASSWORD_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_OPTIONS', ['cost' => 12]);

// Session cookie defaults (actual start handled in session.php)
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);
?>