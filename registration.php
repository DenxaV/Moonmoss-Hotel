<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: signin.html');
	exit();
}

// Collect inputs
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$gender = $_POST['gender'] ?? '';

// Origin could be array of checkboxes
$originField = $_POST['origin'] ?? '';
if (is_array($originField)) {
	$origin = implode(', ', array_map('trim', $originField));
} else {
	$origin = trim((string)$originField);
}

$fairy_type = $_POST['fairy_type'] ?? '';
$email = trim($_POST['email'] ?? ''); // Optional for now

// Basic validation
if ($username === '' || $password === '' || $gender === '' || $fairy_type === '') {
	$error = urlencode('Please fill in all required fields.');
	header('Location: signin.html?error=' . $error);
	exit();
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$error = urlencode('Invalid email format.');
	header('Location: signin.html?error=' . $error);
	exit();
}

if (strlen($password) < 6) {
	$error = urlencode('Password must be at least 6 characters.');
	header('Location: signin.html?error=' . $error);
	exit();
}

// Check uniqueness on username
$check = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$check->execute([$username]);
if ($check->fetch()) {
	$error = urlencode('Username already exists.');
	header('Location: signin.html?error=' . $error);
	exit();
}

$hashed = password_hash($password, PASSWORD_ALGO, PASSWORD_OPTIONS);

try {
	$insert = $pdo->prepare(
		'INSERT INTO users (username, email, password_hash, gender, origin, fairy_type) VALUES (?, ?, ?, ?, ?, ?)'
	);
	$insert->execute([$username, $email, $hashed, $gender, $origin, $fairy_type]);

	// Redirect to login with success message
	header('Location: login.html?registered=1');
	exit();
} catch (PDOException $e) {
	$error = urlencode('Registration failed.');
	header('Location: signin.html?error=' . $error);
	exit();
}
?>