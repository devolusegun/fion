<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require "db.php";

$user_id = $_SESSION['user_id'];

/* Fetch user email */
$q = $conn->prepare("SELECT email FROM users WHERE id=?");
$q->bind_param("i", $user_id);
$q->execute();
$user = $q->get_result()->fetch_assoc();
$user_email = $user["email"] ?? '';

/* ===============================
   HANDLE NEW TICKET SUBMISSION
================================ */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["subject"], $_POST["message"])) {

    $subject = trim($_POST["subject"]);
    $msg = trim($_POST["message"]);

    if ($subject !== "" && $msg !== "") {

        $attachmentPath = null;

        /* File upload */
        if (!empty($_FILES["attachment"]["name"])) {
            $allowed = ['pdf','doc','docx','png','jpg','jpeg'];
            $ext = strtolower(pathinfo($_FILES["attachment"]["name"], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                if (!is_dir("uploads/support")) {
                    mkdir("uploads/support", 0777, true);
                }

                $safeName = uniqid("support_", true) . "." . $ext;
                $attachmentPath = "uploads/support/" . $safeName;

                move_uploaded_file($_FILES["attachment"]["tmp_name"], $attachmentPath);
            }
        }

        $ins = $conn->prepare(
            "INSERT INTO support_tickets (user_id, user_email, subject, message, attachment)
             VALUES (?, ?, ?, ?, ?)"
        );
        $ins->bind_param("issss", $user_id, $user_email, $subject, $msg, $attachmentPath);
        $ins->execute();

        /* Redirect back to support page with success flag */
        header("Location: support.php?success=1");
        exit;
    }
}

/* ===============================
   FETCH USER TICKETS
================================ */
$st = $conn->prepare(
    "SELECT id, subject, message, attachment, status, created_at
     FROM support_tickets
     WHERE user_id=?
     ORDER BY created_at DESC"
);
$st->bind_param("i", $user_id);
$st->execute();
$tickets = $st->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support</title>
    <link rel="stylesheet" href="css/support.css">
    <script defer src="js/support.js"></script>
</head>

<body>

<div class="support-container">

    <h2 class="page-title">Contact Support</h2>

    <?php if (isset($_GET["success"])): ?>
        <div class="success-msg">
            Ticket submitted successfully 
            <a href="dashboard.php" class="back-btn">Return to Dashboard</a>
        </div>
    <?php endif; ?>

    <!-- SUPPORT FORM -->
    <form method="POST" enctype="multipart/form-data" id="supportForm" class="support-form">

        <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" required>
        </div>

        <div class="form-group">
            <label>Message</label>
            <textarea name="message" required></textarea>
        </div>

        <div class="form-group">
            <label>Attach File (optional)</label>
            <input type="file" name="attachment" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg">
        </div>

        <button type="submit" class="send-btn">Submit Ticket</button>
    </form>

    <!-- TICKETS TABLE -->
    <h3>Your Support Tickets</h3>

    <table class="ticket-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Status</th>
                <th>Attachment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($t = $tickets->fetch_assoc()): ?>
            <tr>
                <td><?= (int)$t["id"] ?></td>
                <td><?= htmlspecialchars($t["subject"]) ?></td>
                <td><?= htmlspecialchars($t["created_at"]) ?></td>
                <td>
                    <span class="badge <?= htmlspecialchars($t['status']) ?>">
                        <?= ucfirst($t['status']) ?>
                    </span>
                </td>
                <td>
                    <?php if (!empty($t["attachment"])): ?>
                        <a class="download-btn" href="<?= htmlspecialchars($t['attachment']) ?>" target="_blank">View</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td>
                    <button class="read-btn"
                        onclick="openReadModal(<?= (int)$t['id'] ?>)">
                        Read
                    </button>

                    <button class="reply-btn"
                        onclick="openReplyModal(<?= (int)$t['id'] ?>)">
                        Reply
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>

<!-- READ MODAL -->
<div id="readModal" class="modal">
    <div class="modal-content">
        <h3>Ticket Message</h3>
        <p id="readText"></p>
        <button class="close-btn" onclick="closeModal()">Close</button>
    </div>
</div>

<!-- REPLY MODAL -->
<div id="replyModal" class="modal">
    <div class="modal-content">
        <h3>Reply to Ticket</h3>

        <form method="POST" action="support_reply.php" enctype="multipart/form-data">
            <input type="hidden" id="replyTicketID" name="ticket_id">

            <textarea name="reply_message" class="reply-box" placeholder="Type reply..." required></textarea>

            <div class="modal-buttons">
                <button type="button" class="close-btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="submit-btn">Send Reply</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReadModal(msg) {
    document.getElementById("readText").innerText = msg || "No message content.";
    document.getElementById("readModal").style.display = "flex";
}

function openReplyModal(id) {
    document.getElementById("replyTicketID").value = id;
    document.getElementById("replyModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("readModal").style.display = "none";
    document.getElementById("replyModal").style.display = "none";
}
</script>

</body>
</html>