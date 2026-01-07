<?php
require "admin_guard.php";
require "../db.php";

/* TEMP: assume admin already authenticated */

$stmt = $conn->prepare("
    SELECT p.id, p.title, p.category, p.status, p.created_at,
           r.firstname, r.lastname
    FROM project_ideas p
    JOIN registered r ON r.id = p.user_id
    ORDER BY p.created_at DESC
");
$stmt->execute();
$ideas = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Ideas Moderation — Admin</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body>

    <?php include "admin_nav.php"; ?>

    <div class="admin-layout">

        <?php include "admin_sidebar.php"; ?>

        <main class="admin-content">

            <div class="admin-header">
                <h2>Ideas Moderation</h2>
                <p>Review and manage submitted project ideas</p>
            </div>

            <div class="card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($idea = $ideas->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($idea['title']) ?></td>
                                <td><?= htmlspecialchars($idea['category']) ?></td>
                                <td><?= htmlspecialchars($idea['firstname']) ?></td>
                                <td>
                                    <span class="badge <?= $idea['status'] ?>">
                                        <?= ucfirst($idea['status']) ?>
                                    </span>
                                </td>
                                <td><?= date("M j, Y", strtotime($idea['created_at'])) ?></td>
                                <td>
                                    <a href="idea_review.php?id=<?= $idea['id'] ?>" class="btn-small">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

</body>

</html>