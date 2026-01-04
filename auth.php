<?php
session_start();
require 'db.php';

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$pass  = $_POST['password'] ?? '';

if (!$email || !$pass) {
    $_SESSION['flash'] = 'Please provide a valid email and password.';
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT id, password, email_verified 
     FROM users 
     WHERE email = ? 
     LIMIT 1'
);
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($pass, $user['password'])) {
    $_SESSION['flash'] = 'Invalid credentials.';
    header('Location: login.php');
    exit;
}

if ((int)$user['email_verified'] !== 1) {
    header("Location: verify_notice.php");
    exit;
}

// ✅ success
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];

header('Location: dashboard.php');
exit;
