<?php
require_once __DIR__ . '/session.php';
startSecureSession();
logout();
header('Location: index.html');
exit();
?>