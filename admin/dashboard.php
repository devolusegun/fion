<?php   
session_start();
require "../db.php";

/*
  NOTE:
  - No logic yet
  - Assume admin access is already validated
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard — FION</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Reuse existing styles -->
    <link rel="stylesheet" href="../css/dashboard.css">

    <!-- Admin-specific -->
    <link rel="stylesheet" href="admin.css">
</head>

<body>

<!-- TOP NAV -->
<nav class="top-nav admin-nav">
    <div class="logo">
        FION<span class="gradient-text">Admin</span>
    </div>

    <div class="nav-right">
        <a href="../dashboard.php" class="nav-link">User Dashboard</a>
        <a href="../logout.php" class="nav-link danger">Logout</a>
    </div>
</nav>

<!-- MAIN WRAPPER -->
<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar">

        <h4 class="sidebar-title">Admin Panel</h4>

        <ul class="sidebar-menu">
            <li class="active">Overview</li>
            <li>Ideas Moderation</li>
            <li>Support Tickets</li>
            <li>Users</li>
            <li>Platform Stats</li>
        </ul>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-content">

        <!-- PAGE HEADER -->
        <div class="admin-header">
            <h2>Overview</h2>
            <p>Administrative overview of platform activity</p>
        </div>

        <!-- STATS CARDS -->
        <div class="admin-cards">

            <div class="card stat-card">
                <h4>Total Ideas</h4>
                <span class="stat-number">—</span>
            </div>

            <div class="card stat-card">
                <h4>Pending Review</h4>
                <span class="stat-number">—</span>
            </div>

            <div class="card stat-card">
                <h4>Support Tickets</h4>
                <span class="stat-number">—</span>
            </div>

            <div class="card stat-card">
                <h4>Registered Users</h4>
                <span class="stat-number">—</span>
            </div>

        </div>

        <!-- PLACEHOLDER TABLE -->
        <div class="card admin-placeholder">
            <h3>Admin Activity</h3>
            <p>This area will dynamically load moderation tables, tickets, or user data.</p>
        </div>

    </main>

</div>

</body>
</html>
