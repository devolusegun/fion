<?php
require "admin_guard.php";

$categoryLabels = [
    'dredging_river_niger' => 'Dredging of River Niger & Upper Benue',
    'steel_industry' => 'Steel Industry & Heavy Equipment',
    'retail_system_reform' => 'Retail System Reform',
    'land_geopin_mapping' => 'Land Mapping & Ownership System',
    'water_dam_city_planning' => 'Water & City Planning'
];

/* USERS */
$users = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];

/* IDEAS */
$totalIdeas = $conn->query("SELECT COUNT(*) c FROM project_ideas")->fetch_assoc()['c'];
$approvedIdeas = $conn->query("SELECT COUNT(*) c FROM project_ideas WHERE status='approved'")->fetch_assoc()['c'];
$pendingIdeas = $conn->query("SELECT COUNT(*) c FROM project_ideas WHERE status='pending'")->fetch_assoc()['c'];

/* ENGAGEMENT */
$reactions = $conn->query("SELECT COUNT(*) c FROM idea_reactions")->fetch_assoc()['c'];

/* TOP CATEGORY */
$topCategoryRow = $conn->query("
    SELECT category, COUNT(*) c 
    FROM project_ideas 
    GROUP BY category 
    ORDER BY c DESC 
    LIMIT 1
")->fetch_assoc();

$topCategoryKey = $topCategoryRow['category'] ?? null;
$topCategoryLabel = $topCategoryKey && isset($categoryLabels[$topCategoryKey])
    ? $categoryLabels[$topCategoryKey]
    : 'No data yet';

/* APPROVAL RATE */
$approvalRate = $totalIdeas > 0
    ? round(($approvedIdeas / $totalIdeas) * 100)
    : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin — Platform Stats</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body class="admin-app">

<?php include "admin_nav.php"; ?>

<div class="admin-layout">
    <?php include "admin_sidebar.php"; ?>

    <main class="admin-content">

        <h2>Platform Statistics</h2>
        <p class="muted">Live system indicators</p>

        <div class="stats-grid">

            <div class="stat-card">
                <h4>Total Users</h4>
                <strong><?= $users ?></strong>
            </div>

            <div class="stat-card">
                <h4>Total Ideas</h4>
                <strong><?= $totalIdeas ?></strong>
            </div>

            <div class="stat-card success">
                <h4>Approved Ideas</h4>
                <strong><?= $approvedIdeas ?></strong>
            </div>

            <div class="stat-card warning">
                <h4>Pending Ideas</h4>
                <strong><?= $pendingIdeas ?></strong>
            </div>

            <div class="stat-card">
                <h4>Approval Rate</h4>
                <strong><?= $approvalRate ?>%</strong>
            </div>

            <div class="stat-card">
                <h4>Total Reactions</h4>
                <strong><?= $reactions ?></strong>
            </div>

            <div class="stat-card highlight">
                <h4>Top Category</h4>
                <strong class="stat-emphasis">
                    <?= htmlspecialchars($topCategoryLabel) ?>
                </strong>
            </div>

        </div>

    </main>
</div>

</body>
</html>
