<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "db.php"; // contains $conn

$user_id = $_SESSION['user_id'];

// Retrieve personal info from registered table
$stmt = $conn->prepare("
    SELECT users.email, registered.firstname, registered.lastname
    FROM users
    JOIN registered ON users.id = registered.id
    WHERE users.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$notif = $conn->prepare("SELECT message FROM notifications WHERE user_id=? AND is_read=0 ORDER BY created_at DESC LIMIT 3");
$notif->bind_param("i", $user_id);
$notif->execute();
$notifications = $notif->get_result();

if (!$user) {
    die("User details not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css" />
    <script defer src="js/dashboard.js"></script>
</head>
<body class="light">

<!-- Top Navigation -->
<nav class="top-nav">
    <div class="logo">MyApp</div>
    <div class="nav-right">
        <button id="modeToggle" class="mode-btn">🌙</button>
        <div class="user-name"><?php echo htmlspecialchars($user['firstname']); ?></div>
    </div>
</nav>

<!-- Main Layout -->
<div class="container">

    <!-- Left Column: News -->
    <section class="left-column">
        <h2>Latest News</h2>
        <div class="card news-card">News Item 1</div>
        <div class="card news-card">News Item 2</div>
        <div class="card news-card">News Item 3</div>
    </section>

    <!-- Right Column -->
    <section class="right-column">

        <!-- Personal Info -->
        <div class="card profile-card">
            <h3>Welcome, <?php echo htmlspecialchars($user['firstname']); ?></h3>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <!-- Quick Links -->
        <div class="grid">
            <a href="submit_idea.php" class="card action-card">Submit Project Idea</a>
            <a href="support.php" class="card action-card">Contact Support</a>
        </div>

        <!-- User Notifications (THIS IS THE NEW BLOCK) -->
        <div class="card notify-card">
            <h4>Your Notifications</h4>
            <?php while ($row = $notifications->fetch_assoc()): ?>
                <p>• <?= $row['message'] ?></p>
            <?php endwhile; ?>
        </div>

        <!-- Admin Notifications -->
        <div class="card notify-card">
            <h4>Pending Reviews</h4>
            <p>3 project ideas awaiting admin review.</p>
        </div>

        <!-- Progress / Achievements -->
        <div class="card progress-card">
            <h4>Your Contribution Progress</h4>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 60%;"></div>
            </div>
            <p>60% – Keep going!</p>
        </div>

    </section>
</div>

</body>
</html>
