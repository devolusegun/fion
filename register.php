<?php
session_start();
require "db.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $firstname = trim($_POST['firstname']);
  $lastname  = trim($_POST['lastname']);
  $email     = trim($_POST['email']);
  $password  = $_POST['password'];
  $confirm   = $_POST['confirm_password'];

  /* Basic validation */
  if ($password !== $confirm) {
    $errors[] = "Passwords do not match.";
  }

  if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters.";
  }

  /* Check email uniqueness */
  $chk = $conn->prepare("SELECT id FROM users WHERE email=?");
  $chk->bind_param("s", $email);
  $chk->execute();
  $chk->store_result();

  if ($chk->num_rows > 0) {
    $errors[] = "Email already registered.";
  }

  if (!isset($_POST['terms'])) {
    $errors[] = "You must agree to the Terms and Privacy Policy.";
  }

  if (empty($errors)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    /* Insert into users */
    $conn->begin_transaction();

    try {
      $u = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
      $u->bind_param("ss", $email, $hashed);
      $u->execute();

      $user_id = $conn->insert_id;

      /* Insert into registered */
      $r = $conn->prepare("
                  INSERT INTO registered (id, firstname, lastname)
                  VALUES (?, ?, ?)
              ");
      $r->bind_param("iss", $user_id, $firstname, $lastname);
      $r->execute();

      $conn->commit();

      $_SESSION['user_id'] = $user_id;
      header("Location: dashboard.php");
      exit;
    } catch (Exception $e) {
      $conn->rollback();
      $errors[] = "Registration failed. Please try again.";
    }

    $token = bin2hex(random_bytes(32));

    $upd = $conn->prepare("
        UPDATE users 
        SET email_verification_token = ?
        WHERE id = ?
      ");
    $upd->bind_param("si", $token, $user_id);
    $upd->execute();
  }
}

$verifyLink = "https://fion.ng/verify_email?token=" . $token;

$subject = "Verify your Fion account";

$message = "
  <html>
  <head>
    <style>
      body { font-family: Arial, sans-serif; background:#0a1128; color:#fff; padding:20px; }
      .box { max-width:600px; margin:auto; background:#14213d; padding:30px; border-radius:12px; }
      .btn { display:inline-block; padding:12px 24px; background:#7209b7; color:#fff; text-decoration:none; border-radius:8px; }
      .footer { margin-top:30px; font-size:13px; opacity:0.7; }
    </style>
  </head>
  <body>
    <div class='box'>
      <h2>Welcome to FION</h2>
      <p>Thank you for creating an account.</p>
      <p>Please verify your email address to activate your account:</p>
      <p><a href='$verifyLink' class='btn'>Verify Email</a></p>
      <p>If the button does not work, copy and paste this link:</p>
      <p>$verifyLink</p>
      <div class='footer'>
        © " . date("Y") . " Fion. All rights reserved.
      </div>
    </div>
  </body>
  </html>
  ";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: Fion <no-reply@fion.ng>\r\n";

mail($email, $subject, $message, $headers);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create Account</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <div class="auth-wrapper">
    <form class="auth-form" method="POST">

      <h2 class="auth-title">Create Your Account</h2>

      <?php if (!empty($errors)): ?>
        <div class="auth-errors">
          <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="form-group">
        <input type="text" class="input-field" name="firstname" required placeholder="First Name">
      </div>

      <div class="form-group">
        <input type="text" class="input-field" name="lastname" required placeholder="Last Name">
      </div>

      <div class="form-group">
        <input type="email" class="input-field" name="email" required placeholder="Email">
      </div>

      <div class="form-group">
        <input type="password" class="input-field" name="password" id="password"
          required placeholder="Password (min 8 chars)">

        <!-- Password Strength Meter -->
        <div class="password-meter">
          <div class="strength-bar">
            <div id="strengthFill"></div>
          </div>
          <small id="strengthText">Enter a password</small>
        </div>
      </div>

      <div class="form-group">
        <input type="password" class="input-field" name="confirm_password" required placeholder="Confirm Password">
      </div>

      <div class="form-group consent">
        <label class="consent-label">
          <input type="checkbox" id="termsCheckbox" name="terms" required>
          I agree to the
          <a href="terms.php" target="_blank">Terms of Service</a>
          and
          <a href="privacy.php" target="_blank">Privacy Policy</a>
        </label>
      </div>

      <button type="submit" id="registerBtn" class="btn btn-primary" disabled>Create Account</button>

      <p class="auth-link">
        Already have an account? <a href="login.php">Login</a>
      </p>

    </form>
  </div>

  <script>
    const passwordInput = document.getElementById("password");
    const strengthFill = document.getElementById("strengthFill");
    const strengthText = document.getElementById("strengthText");

    passwordInput.addEventListener("input", () => {
      const val = passwordInput.value;
      let score = 0;

      if (val.length >= 8) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[0-9]/.test(val)) score++;
      if (/[^A-Za-z0-9]/.test(val)) score++;

      const percent = (score / 4) * 100;
      strengthFill.style.width = percent + "%";

      strengthFill.className = ""; // reset
      strengthFill.classList.add("level-" + score);

      const labels = [
        "Very weak",
        "Weak",
        "Okay",
        "Strong",
        "Very strong"
      ];

      strengthText.textContent = val.length === 0 ? "Enter a password" : labels[score];
    });
  </script>

  <script>
    const registerBtn = document.getElementById("registerBtn");
    const termsCheckbox = document.getElementById("termsCheckbox");

    let passwordStrong = false;

    function updateRegisterState() {
      registerBtn.disabled = !(passwordStrong && termsCheckbox.checked);
    }

    passwordInput.addEventListener("input", () => {
      const val = passwordInput.value;
      let score = 0;

      if (val.length >= 8) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[0-9]/.test(val)) score++;
      if (/[^A-Za-z0-9]/.test(val)) score++;

      passwordStrong = (score === 4);
      updateRegisterState();
    });

    termsCheckbox.addEventListener("change", updateRegisterState);
  </script>

</body>

</html>