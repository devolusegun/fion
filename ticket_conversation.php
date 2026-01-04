<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($ticket_id <= 0) {
    exit("Invalid ticket.");
}

/* ===============================
   VERIFY TICKET OWNERSHIP
================================ */
$chk = $conn->prepare(
    "SELECT id, message, attachment, created_at 
     FROM support_tickets 
     WHERE id=? AND user_id=?"
);
$chk->bind_param("ii", $ticket_id, $user_id);
$chk->execute();
$tkt = $chk->get_result()->fetch_assoc();

if (!$tkt) {
    exit("Ticket not found.");
}

/* ===============================
   RENDER CHAT THREAD
================================ */
echo '<div class="chat-thread">';

/* USER ORIGINAL MESSAGE */
echo '
<div class="chat-bubble user">
    <div class="chat-msg">' . nl2br(htmlspecialchars($tkt['message'])) . '</div>
    <div class="chat-meta">
        You • ' . htmlspecialchars($tkt['created_at']) . '
    </div>';

if (!empty($tkt['attachment'])) {
    echo '<a class="chat-attach" href="' . htmlspecialchars($tkt['attachment']) . '" target="_blank">📎 View attachment</a>';
}
echo '</div>';

/* REPLIES */
$r = $conn->prepare(
    "SELECT sender, message, attachment, created_at
     FROM support_replies
     WHERE ticket_id=?
     ORDER BY created_at ASC"
);
$r->bind_param("i", $ticket_id);
$r->execute();
$replies = $r->get_result();

while ($rep = $replies->fetch_assoc()) {

    $side = ($rep['sender'] === 'admin') ? 'admin' : 'user';
    $label = ($rep['sender'] === 'admin') ? 'Fion Support' : 'You';

    echo '
    <div class="chat-bubble ' . $side . '">
        <div class="chat-msg">' . nl2br(htmlspecialchars($rep['message'])) . '</div>
        <div class="chat-meta">
            ' . $label . ' • ' . htmlspecialchars($rep['created_at']) . '
        </div>';

    if (!empty($rep['attachment'])) {
        echo '<a class="chat-attach" href="' . htmlspecialchars($rep['attachment']) . '" target="_blank">📎 View attachment</a>';
    }

    echo '</div>';
}

echo '</div>';
