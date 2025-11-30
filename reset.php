<?php
session_start();
require 'db.php';

$token = $_GET['token'] ?? ($_POST['token'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';

    if (strlen($new) < 8 || $new !== $confirm) {
        $_SESSION['flash'] = 'Passwords must match and be at least 8 characters.';
        header('Location: reset.php?token='.urlencode($token)); exit;
    }

    // verify token
    $stmt = $pdo->prepare('SELECT email, expires_at FROM password_resets WHERE token = ? LIMIT 1');
    $stmt->execute([$token]);
    $row = $stmt->fetch();

    if (!$row || new DateTime() > new DateTime($row['expires_at'])) {
        $_SESSION['flash'] = 'Invalid or expired token.';
        header('Location: index.php'); exit;
    }

    // update password
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $up = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
    $up->execute([$hash, $row['email']]);

    // delete token(s)
    $del = $pdo->prepare('DELETE FROM password_resets WHERE email = ?');
    $del->execute([$row['email']]);

    $_SESSION['flash'] = 'Password updated. You may sign in.';
    header('Location: index.php'); exit;
}

// GET: show form
if (!$token) {
    $_SESSION['flash'] = 'Missing token.';
    header('Location: index.php'); exit;
}

// optional: check token existence/expiry for UX
$stmt = $pdo->prepare('SELECT email, expires_at FROM password_resets WHERE token = ? LIMIT 1');
$stmt->execute([$token]);
$row = $stmt->fetch();
if (!$row || new DateTime() > new DateTime($row['expires_at'])) {
    $_SESSION['flash'] = 'Invalid or expired token.';
    header('Location: index.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Set new password</title><link rel="stylesheet" href="styles.css"></head><body>
  <div class="card">
    <h1>Set a new password</h1>
    <?php if(!empty($_SESSION['flash'])): echo '<div style="color:#b91c1c;margin-bottom:10px">'.htmlspecialchars($_SESSION['flash']).'</div>'; unset($_SESSION['flash']); endif; ?>
    <form method="post" action="reset.php">
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
      <div class="form-group">
        <label for="password">New password</label>
        <input id="password" name="password" type="password" required minlength="8">
      </div>
      <div class="form-group">
        <label for="password_confirm">Confirm new password</label>
        <input id="password_confirm" name="password_confirm" type="password" required minlength="8">
      </div>
      <button class="btn" type="submit">Set password</button>
    </form>
  </div>
</body></html>
