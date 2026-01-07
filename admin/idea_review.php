<?php
require "admin_guard.php";
require "../db.php";

$id = (int)($_GET['id'] ?? 0);
if (!$id) die("Invalid idea");

$stmt = $conn->prepare("
    SELECT p.*, r.firstname, r.lastname
    FROM project_ideas p
    JOIN registered r ON r.id = p.user_id
    WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$idea = $stmt->get_result()->fetch_assoc();

if (!$idea) die("Idea not found");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Review Idea</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body>

    <?php include "admin_nav.php"; ?>

    <div class="admin-layout">
        <?php include "admin_sidebar.php"; ?>

        <main class="admin-content">

            <div class="admin-review-card">

                <div class="review-header">
                    <h2><?= htmlspecialchars($idea['title']) ?></h2>

                    <div class="review-meta">
                        <span><strong>Author:</strong> <?= htmlspecialchars($idea['firstname']) ?></span>
                        <span><strong>Category:</strong> <?= ucfirst(str_replace('_', ' ', $idea['category'])) ?></span>
                        <span><strong>Date:</strong> <?= date("M j, Y", strtotime($idea['created_at'])) ?></span>
                    </div>
                </div>

                <div class="review-section">
                    <h4>Summary</h4>
                    <div class="review-box">
                        <?= nl2br(htmlspecialchars($idea['summary'])) ?>
                    </div>
                </div>

                <div class="review-section">
                    <h4>Justification</h4>
                    <div class="review-box">
                        <?= nl2br(htmlspecialchars($idea['justification'])) ?>
                    </div>
                </div>

                <?php if (!empty($idea['qualification_doc']) || !empty($idea['proposal_doc'])): ?>
                    <div class="review-section">
                        <h4>Attachments</h4>

                        <div class="attachments">
                            <?php if ($idea['qualification_doc']): ?>
                                <a class="attachment-card"
                                    href="../<?= htmlspecialchars($idea['qualification_doc']) ?>"
                                    target="_blank">
                                    📄 Qualification Document
                                </a>
                            <?php endif; ?>

                            <?php if ($idea['proposal_doc']): ?>
                                <a class="attachment-card"
                                    href="../<?= htmlspecialchars($idea['proposal_doc']) ?>"
                                    target="_blank">
                                    📄 Proposal Document
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="review-actions">
                    <form method="POST" action="idea_action.php" onsubmit="return confirm('Approve this idea?')">
                        <input type="hidden" name="idea_id" value="<?= $idea['id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button class="btn-approve">Approve</button>
                    </form>

                    <form method="POST" action="idea_action.php" onsubmit="return confirm('Reject this idea?')">
                        <input type="hidden" name="idea_id" value="<?= $idea['id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <button class="btn-reject">Reject</button>
                    </form>
                </div>

            </div>

        </main>
    </div>

</body>

</html>