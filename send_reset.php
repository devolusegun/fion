<?php
session_start();
require 'db.php';

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email) {
    $_SESSION['flash'] = 'Enter a valid email.';
    header('Location: forgot.php'); exit;
}

// check user exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    // Don't reveal that account doesn't exist — generic message
    $_SESSION['flash'] = 'If that email exists we sent a reset link.';
    header('Location: forgot.php'); exit;
}

// create token
$token = bin2hex(random_bytes(24));
$expires = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

$ins = $pdo->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)');
$ins->execute([$email, $token, $expires]);

$resetLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['REQUEST_URI']) . "/reset.php?token=$token";

// Attempt to email (configure SMTP in php.ini or use PHPMailer)
// mail($email, "Reset your password", "Reset link: $resetLink", "From: no-reply@example.com");

// For local/dev: show link and provide friendly message
$_SESSION['flash'] = "A reset link was generated. (In production this is emailed.)";
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reset sent</title><link rel="stylesheet" href="styles.css"></head><body>
  <div class="card">
    <h1>Reset link generated</h1>
    <p class="small">Link (development):</p>
    <pre style="word-break:break-all;background:#f1f5f9;padding:10px;border-radius:8px"><?php echo htmlspecialchars($resetLink); ?></pre>
    <p class="small">This link expires in 1 hour.</p>
    <a href="index.php" class="small-link">Back to login</a>
  </div>
</body></html>
