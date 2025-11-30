<?php session_start(); ?>
<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Forgot password</title>
<link rel="stylesheet" href="styles.css">
</head><body>
  <div class="card">
    <h1>Reset password</h1>
    <p class="lead">Enter your account email. We'll send a reset link.</p>

    <?php if(!empty($_SESSION['flash'])): ?>
      <div style="margin-bottom:12px;color:#065f46;"><?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
    <?php endif; ?>

    <form method="post" action="send_reset.php" novalidate>
      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>
      </div>
      <button class="btn" type="submit">Send reset link</button>
      <div style="margin-top:10px"><a href="index.html" class="small-link">Back to login</a></div>
    </form>
  </div>
</body></html>
