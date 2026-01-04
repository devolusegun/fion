<?php
require "db.php";

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Invalid verification link.");
}

$stmt = $conn->prepare("
    UPDATE users 
    SET email_verified = 1,
        email_verification_token = NULL
    WHERE email_verification_token = ?
");
$stmt->bind_param("s", $token);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "
    <h2>Email Verified ✅</h2>
    <p>Your email has been successfully verified.</p>
    <a href='login.php'>Proceed to Login</a>
    ";
} else {
    echo "
    <h2>Verification Failed ❌</h2>
    <p>This link is invalid or has already been used.</p>
    ";
}
