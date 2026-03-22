<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: login.html');
	exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
	$error = urlencode('Please enter username and password.');
	header('Location: login.html?error=' . $error);
	exit();
}

$stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
	$error = urlencode('Invalid credentials.');
	header('Location: login.html?error=' . $error);
	exit();
}

$_SESSION['user_id'] = (int)$user['id'];

$redirect = $_GET['redirect'] ?? '';
if ($redirect) {
	header('Location: ' . $redirect);
} else {
	header('Location: index.html');
}
exit();
?>



