<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch profile
$stmt = $conn->prepare("SELECT * FROM registered WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();

$success = false;

// Update profile
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $phone   = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $dob     = $_POST['dob'];
    $gender  = $_POST['gender'];

    $upd = $conn->prepare("
        UPDATE registered 
        SET phone=?, address=?, dob=?, gender=?, profile_completed=1
        WHERE id=?
    ");
    $upd->bind_param("ssssi", $phone, $address, $dob, $gender, $user_id);
    $upd->execute();

    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Complete Profile</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/profile.css">
</head>
<body>

<div class="auth-wrapper">
  <form class="auth-form" method="POST">

    <h2 class="auth-title">Complete Your Profile</h2>

    <?php if ($success): ?>
      <div class="success-msg">
        Profile updated successfully.
        <a href="dashboard.php">Return to Dashboard</a>
      </div>
    <?php endif; ?>

    <div class="form-group">
      <label>First Name</label>
      <input type="text" class="input-field" value="<?= htmlspecialchars($profile['firstname']) ?>" disabled>
    </div>

    <div class="form-group">
      <label>Last Name</label>
      <input type="text" class="input-field" value="<?= htmlspecialchars($profile['lastname']) ?>" disabled>
    </div>

    <div class="form-group">
      <label>Phone Number</label>
      <input type="text" name="phone" class="input-field"
        value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Address</label>
      <input type="text" name="address" class="input-field"
        value="<?= htmlspecialchars($profile['address'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Date of Birth</label>
      <input type="date" name="dob" class="input-field"
        value="<?= htmlspecialchars($profile['dob'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Gender</label>
      <select name="gender" class="input-field">
        <option value="">-- Select --</option>
        <option value="male"   <?= ($profile['gender']=='male')?'selected':'' ?>>Male</option>
        <option value="female" <?= ($profile['gender']=='female')?'selected':'' ?>>Female</option>
        <option value="other"  <?= ($profile['gender']=='other')?'selected':'' ?>>Other</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Save Profile</button>

  </form>
</div>

</body>
</html>
