<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: support.php");
    exit;
}

/* ===============================
   VALIDATE INPUT
================================ */
$ticket_id = isset($_POST["ticket_id"]) ? (int)$_POST["ticket_id"] : 0;
$reply     = trim($_POST["reply_message"] ?? '');

if ($ticket_id <= 0 || $reply === '') {
    header("Location: support.php");
    exit;
}

/* ===============================
   ENSURE TICKET BELONGS TO USER
================================ */
$chk = $conn->prepare(
    "SELECT id FROM support_tickets WHERE id=? AND user_id=?"
);
$chk->bind_param("ii", $ticket_id, $user_id);
$chk->execute();
$chk->store_result();

if ($chk->num_rows === 0) {
    // user trying to reply to someone else's ticket
    header("Location: support.php");
    exit;
}

/* ===============================
   HANDLE ATTACHMENT (OPTIONAL)
================================ */
$attachmentPath = null;

if (!empty($_FILES["reply_attachment"]["name"])) {
    $allowed = ['pdf','doc','docx','png','jpg','jpeg'];
    $ext = strtolower(pathinfo($_FILES["reply_attachment"]["name"], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {

        if (!is_dir("uploads/support")) {
            mkdir("uploads/support", 0777, true);
        }

        $safeName = uniqid("reply_", true) . "." . $ext;
        $attachmentPath = "uploads/support/" . $safeName;

        move_uploaded_file($_FILES["reply_attachment"]["tmp_name"], $attachmentPath);
    }
}

/* ===============================
   INSERT REPLY
================================ */
$ins = $conn->prepare(
    "INSERT INTO support_replies (ticket_id, sender, message, attachment)
     VALUES (?, 'user', ?, ?)"
);
$ins->bind_param("iss", $ticket_id, $reply, $attachmentPath);
$ins->execute();

/* ===============================
   UPDATE TICKET STATUS
================================ */
$upd = $conn->prepare(
    "UPDATE support_tickets SET status='answered' WHERE id=?"
);
$upd->bind_param("i", $ticket_id);
$upd->execute();

/* ===============================
   REDIRECT BACK
================================ */
header("Location: support.php");
exit;
