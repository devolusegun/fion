<?php
require "../db.php";

$id = (int)($_GET['id'] ?? 0);
if (!$id) exit;

$t = $conn->query("SELECT * FROM support_tickets WHERE id=$id")->fetch_assoc();

echo "<div class='chat-bubble user'>";
echo "<strong>User:</strong><br>" . nl2br(htmlspecialchars($t['message']));
echo "</div>";

$r = $conn->query("
  SELECT * FROM support_replies
  WHERE ticket_id=$id
  ORDER BY created_at ASC
");

while ($rep = $r->fetch_assoc()) {
  $cls = $rep['sender'] === 'admin' ? 'admin' : 'user';
  echo "<div class='chat-bubble $cls'>";
  echo nl2br(htmlspecialchars($rep['message']));
  echo "</div>";
}
