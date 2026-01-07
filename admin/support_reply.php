<?php
require "admin_guard.php";

$ticket_id = (int)($_POST['ticket_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if (!$ticket_id || !$message) exit;

$stmt = $conn->prepare("
  INSERT INTO support_replies (ticket_id, message)
  VALUES (?, ?)
");
$stmt->bind_param("is", $ticket_id, $message);
$stmt->execute();

/* Update ticket status */
$conn->query("UPDATE support_tickets SET status='answered' WHERE id=$ticket_id");
