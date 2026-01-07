<?php
require "admin_guard.php";

$users = $conn->query("
    SELECT 
        u.id,
        u.email,
        u.role,
        u.email_verified,
        u.created_at,
        r.firstname,
        r.lastname
    FROM users u
    LEFT JOIN registered r ON r.id = u.id
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin — Users</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body class="admin-app">

<?php include "admin_nav.php"; ?>

<div class="admin-layout">
<?php include "admin_sidebar.php"; ?>

<main class="admin-content">

    <h2>Registered Users</h2>
    <p class="muted">Read-only overview of platform users</p>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Email Verified</th>
                <th>Date Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars(trim(($u['firstname'] ?? '') . ' ' . ($u['lastname'] ?? ''))) ?: '—' ?>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="badge <?= $u['role'] ?>">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td>
                        <?= $u['email_verified'] ? '✅ Verified' : '❌ Not verified' ?>
                    </td>
                    <td><?= date("M j, Y", strtotime($u['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</main>
</div>

</body>
</html>
