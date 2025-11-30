<?php session_start(); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="card" role="main" aria-labelledby="loginTitle">
    <h1 id="loginTitle">Sign in</h1>
    <p class="lead">Sign in with your email and password.</p>

    <?php if(!empty($_SESSION['flash'])): ?>
      <div style="margin-bottom:12px;color:#b91c1c;"><?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
    <?php endif; ?>

    <form method="post" action="auth.php" autocomplete="on" novalidate>
      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required />
      </div>

      <div class="form-group">
        <label for="password">Password <span style="font-size:12px;color:#6b7280">(min 8 chars)</span></label>
        <input id="password" name="password" type="password" required minlength="8" />
      </div>

      <button class="btn" type="submit">Sign in</button>

      <div class="link-row">
        <a href="forgot.php" class="small-link">Forgot password?</a>
        <span style="color:var(--muted);font-size:13px">No account? <a href="#" class="small-link">Sign up</a></span>
      </div>
    </form>
  </div>
</body>
</html>
