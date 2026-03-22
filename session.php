<?php
require_once __DIR__ . '/config.php';

function startSecureSession() {
    $session_name = 'moonmoss_session';
    $secure = false;
    $httponly = true;

    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? '',
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => 'Strict'
    ]);

    session_name($session_name);
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    session_regenerate_id(true);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserData() {
    if (!isLoggedIn()) return null;
    global $pdo;
    $stmt = $pdo->prepare('SELECT id, username, email, fairy_type, gender, coins FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.html?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? '/'));
        exit();
    }
}

function logout() {
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
?>