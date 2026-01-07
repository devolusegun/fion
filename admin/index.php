<?php
require "admin_guard.php";

/* USERS */
$usersTotal = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];

/* IDEAS */
$ideasTotal = $conn->query("SELECT COUNT(*) c FROM project_ideas")->fetch_assoc()['c'];
$ideasPending = $conn->query("SELECT COUNT(*) c FROM project_ideas WHERE status='pending'")->fetch_assoc()['c'];
$ideasApproved = $conn->query("SELECT COUNT(*) c FROM project_ideas WHERE status='approved'")->fetch_assoc()['c'];

/* SUPPORT */
$ticketsOpen = $conn->query("SELECT COUNT(*) c FROM support_tickets WHERE status='open'")->fetch_assoc()['c'];
$ticketsAnswered = $conn->query("SELECT COUNT(*) c FROM support_tickets WHERE status='answered'")->fetch_assoc()['c'];

/* ENGAGEMENT */
$reactionsTotal = $conn->query("SELECT COUNT(*) c FROM idea_reactions")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — FION</title>

    <!-- Reuse existing styles -->
    <link rel="stylesheet" href="../css/dashboard.css">
    
    <link rel="stylesheet" href="admin.css">
</head>

<body>

    <?php include "admin_nav.php"; ?>

    <div class="admin-layout">
        <?php include "admin_sidebar.php"; ?>

        <main class="admin-content">

            <div class="admin-header">
                <h2>Admin Dashboard</h2>
                <p>Platform overview and control</p>
            </div>

            <!-- STATS GRID -->
            <div class="stats-grid">

                <div class="stat-card">
                    <h4>Total Users</h4>
                    <strong>
                        <?= $usersTotal ?>
                    </strong>
                </div>

                <div class="stat-card">
                    <h4>Total Ideas</h4>
                    <strong>
                        <?= $ideasTotal ?>
                    </strong>
                </div>

                <div class="stat-card warning">
                    <h4>Pending Ideas</h4>
                    <strong>
                        <?= $ideasPending ?>
                    </strong>
                </div>

                <div class="stat-card success">
                    <h4>Approved Ideas</h4>
                    <strong>
                        <?= $ideasApproved ?>
                    </strong>
                </div>

                <div class="stat-card">
                    <h4>Open Tickets</h4>
                    <strong>
                        <?= $ticketsOpen ?>
                    </strong>
                </div>

                <div class="stat-card">
                    <h4>Reactions</h4>
                    <strong>
                        <?= $reactionsTotal ?>
                    </strong>
                </div>

            </div>

            <!-- QUICK LINKS -->
            <div class="card quick-actions">
                <a href="ideas.php">Moderate Ideas</a>
                <a href="support.php">Support Tickets</a>
                <a href="users.php">Users Management</a>
            </div>

        </main>
    </div>

</body>

</html>