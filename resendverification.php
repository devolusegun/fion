<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT email, email_verified 
    FROM users 
    WHERE id=?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user['email_verified']) {
    header("Location: dashboard.php");
    exit;
}

// Generate new token
$token = bin2hex(random_bytes(32));

$upd = $conn->prepare("
    UPDATE users 
    SET email_verification_token=? 
    WHERE id=?
");
$upd->bind_param("si", $token, $user_id);
$upd->execute();

// Send email
$verifyLink = "https://fion.ng/verify_email.php?token=$token";

$subject = "Verify your Fion account (Resent)";
$message = "
<h2>Email Verification</h2>
<p>Please verify your email by clicking the link below:</p>
<p><a href='$verifyLink'>$verifyLink</a></p>
";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: FION <no-reply@fion.ng>\r\n";

mail($user['email'], $subject, $message, $headers);

header("Location: verify_notice.php?resent=1");
exit;