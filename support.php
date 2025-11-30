<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require "db.php";

$user_id = $_SESSION['user_id'];

// fetch user email
$q = $conn->prepare("SELECT email FROM users WHERE id=?");
$q->bind_param("i", $user_id);
$q->execute();
$user = $q->get_result()->fetch_assoc();
$user_email = $user["email"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = $_POST["subject"];
    $msg = $_POST["message"];

    // Store ticket in DB
    $ins = $conn->prepare("INSERT INTO support_tickets (user_id, user_email, subject, message) VALUES (?, ?, ?, ?)");
    $ins->bind_param("isss", $user_id, $user_email, $subject, $msg);
    $ins->execute();

    // Email admin
    $adminEmail = "admin@example.com";
    mail($adminEmail, "Support Ticket: $subject", $msg . "\n\nFrom: $user_email");

    echo "Support ticket submitted.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<body>
<h2>Support</h2>

<form method="POST">
    <label>Subject</label><br>
    <input type="text" name="subject" required><br><br>

    <label>Message</label><br>
    <textarea name="message" required></textarea><br><br>

    <button type="submit">Send</button>
</form>
</body>
</html>
