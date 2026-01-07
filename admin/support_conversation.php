<?php
require "admin_guard.php";

$ticket_id = (int)($_GET['id'] ?? 0);
if (!$ticket_id) {
    exit("Invalid ticket.");
}

/* Ticket */
$t = $conn->prepare("
  SELECT subject, message, user_email
  FROM support_tickets
  WHERE id=?
");
$t->bind_param("i", $ticket_id);
$t->execute();
$ticket = $t->get_result()->fetch_assoc();

if (!$ticket) {
    exit("Ticket not found.");
}

/* Replies */
$r = $conn->prepare("
  SELECT message, created_at
  FROM support_replies
  WHERE ticket_id=?
  ORDER BY created_at ASC
");
$r->bind_param("i", $ticket_id);
$r->execute();
$replies = $r->get_result();
?>

<div class="ticket-thread">

  <div class="bubble user">
    <strong>User (<?= htmlspecialchars($ticket['user_email']) ?>)</strong>
    <p><?= nl2br(htmlspecialchars($ticket['message'])) ?></p>
  </div>

  <?php while ($rep = $replies->fetch_assoc()): ?>
    <div class="bubble admin">
      <strong>Admin</strong>
      <p><?= nl2br(htmlspecialchars($rep['message'])) ?></p>
      <small><?= date("M j, Y H:i", strtotime($rep['created_at'])) ?></small>
    </div>
  <?php endwhile; ?>

</div>
