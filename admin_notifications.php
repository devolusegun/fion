<?php
require "db.php";

$result = $conn->query("
    SELECT notifications.*, users.email
    FROM notifications
    JOIN users ON notifications.user_id = users.id
    ORDER BY notifications.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<body>
<h2>Notifications</h2>

<?php while ($n = $result->fetch_assoc()): ?>
    <div style="background:#eee;margin:10px;padding:10px;">
        <b>User:</b> <?= $n['email'] ?><br>
        <b>Message:</b> <?= $n['message'] ?><br>
        <b>Time:</b> <?= $n['created_at'] ?><br>
    </div>
<?php endwhile; ?>

</body>
</html>
