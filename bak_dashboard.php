<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: index.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard</title><link rel="stylesheet" href="styles.css"></head><body>
  <div class="card">
    <h1>Welcome</h1>
    <p class="lead">You are signed in.</p>
    <form method="post" action="logout.php"><button class="btn" type="submit">Sign out</button></form>
  </div>
</body></html>
