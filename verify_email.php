<?php
require "db.php";

$token = $_GET['token'] ?? '';

$success = false;

if ($token) {
    $stmt = $conn->prepare("
        UPDATE users 
        SET email_verified = 1,
            email_verification_token = NULL
        WHERE email_verification_token = ?
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Email Verification | FION</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at top, #2a2f6f, #0f1229 70%);
    font-family: 'Poppins', sans-serif;
    color: #eceef3;
}

.verify-card {
    background: rgba(34,36,70,0.9);
    border-radius: 16px;
    padding: 40px 30px;
    max-width: 420px;
    width: 90%;
    text-align: center;
    box-shadow: 0 15px 45px rgba(0,0,0,0.35);
}

.logo {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.4rem;
    margin-bottom: 10px;
}

.gradient-text {
    background: linear-gradient(90deg,#47caff,#ad4cfb);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.status-icon {
    font-size: 3rem;
    margin: 18px 0;
}

.verify-card h2 {
    font-size: 1.5rem;
    margin-bottom: 8px;
}

.verify-card p {
    color: #cdd1ff;
    font-size: 1rem;
    margin-bottom: 24px;
}

.btn {
    display: inline-block;
    padding: 12px 26px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    background: linear-gradient(90deg,#47caff,#ad4cfb);
    color: #fff;
    transition: transform .2s ease, box-shadow .2s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(173,76,251,0.35);
}
</style>
</head>

<body>

<div class="verify-card">
    <div class="logo">
        Fion<span class="gradient-text">CES</span>
    </div>

    <?php if ($success): ?>
        <div class="status-icon">✅</div>
        <h2>Email Verified</h2>
        <p>Your email has been successfully verified.<br>You can now access your dashboard.</p>
        <a href="login.php" class="btn">Proceed to Login</a>
    <?php else: ?>
        <div class="status-icon">❌</div>
        <h2>Verification Failed</h2>
        <p>This verification link is invalid or has already been used.</p>
        <a href="login.php" class="btn">Go to Login</a>
    <?php endif; ?>
</div>

</body>
</html>
