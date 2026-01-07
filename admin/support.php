<?php
require "admin_guard.php";
require "../db.php";

/* TEMP: assume admin authenticated */

$q = $conn->query("
  SELECT id, subject, user_email, status, created_at
  FROM support_tickets
  ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html>

<head>
  <title>Support Tickets — Admin</title>
  
  <link rel="stylesheet" href="../css/dashboard.css">
  <link rel="stylesheet" href="admin.css">

  <script defer src="admin_support.js"></script>

</head>

<body>

  <?php include "admin_nav.php"; ?>

  <div class="admin-layout">
    <?php include "admin_sidebar.php"; ?>

    <main class="admin-content">

      <div class="admin-header">
        <h2>Support Tickets</h2>
        <p>Manage and respond to user issues</p>
      </div>

      <div class="card">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Subject</th>
              <th>User</th>
              <th>Status</th>
              <th>Date</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php while ($t = $q->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($t['subject']) ?></td>
                <td><?= htmlspecialchars($t['user_email']) ?></td>
                <td>
                  <span class="badge <?= $t['status'] ?>">
                    <?= ucfirst($t['status']) ?>
                  </span>
                </td>
                <td><?= date("M j, Y", strtotime($t['created_at'])) ?></td>
                <td>
                  <button class="btn-small"
                    onclick="openTicket(<?= $t['id'] ?>)">
                    Open
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>

  <!-- MODAL -->
  <div id="ticketModal" class="modal">
    <div class="modal-card large">
      <div id="ticketConversation"></div>

      <form id="replyForm">
        <input type="hidden" name="ticket_id" id="ticketId">
        <textarea name="message" placeholder="Type reply…" required></textarea>
        <button type="submit" class="btn-primary">Send Reply</button>
      </form>

      <button class="close-btn" onclick="closeTicket()">Close</button>
    </div>
  </div>

</body>

</html>