<?php
session_start();
require 'db.php';

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$pass = $_POST['password'] ?? '';

if (!$email || !$pass) {
    $_SESSION['flash'] = 'Please provide a valid email and password.';
    header('Location: index.html'); exit;
}

$stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($pass, $user['password'])) {
    $_SESSION['flash'] = 'Invalid credentials.';
    header('Location: index.html'); exit;
}

// success
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
header('Location: Dashboard.php'); exit;
