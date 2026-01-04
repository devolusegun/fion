<?php
session_start();
/**if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}**/
require "db.php";
$user_id = $_SESSION['user_id'] ?? null;
$isGuest = $user_id === null;

$user_id = $_SESSION['user_id'];
$idea_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idea_id <= 0) {
    die("Invalid idea.");
}

/* Fetch idea */
$stmt = $conn->prepare("
    SELECT p.*, r.firstname, r.lastname
    FROM project_ideas p
    JOIN registered r ON r.id = p.user_id
    WHERE p.id = ? AND p.status = 'approved'
");
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$idea = $stmt->get_result()->fetch_assoc();

if (!$idea) {
    die("Idea not found or not approved.");
}

/* Fetch reactions count */
$counts = [];
$res = $conn->query("
    SELECT reaction, COUNT(*) c 
    FROM idea_reactions 
    WHERE idea_id = $idea_id 
    GROUP BY reaction
");
while ($row = $res->fetch_assoc()) {
    $counts[$row['reaction']] = $row['c'];
}

/* Check if user is owner */
$isOwner = ($idea['user_id'] == $user_id);
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($idea['title']) ?></title>
    <link rel="stylesheet" href="css/ideas.css">
    <script defer src="js/reactions.js"></script>
</head>

<body>

    <div class="idea-detail">

        <h2><?= htmlspecialchars($idea['title']) ?></h2>

        <div class="idea-meta">
            <span>By <?= htmlspecialchars($idea['firstname']) ?></span>
            <span><?= date("M j, Y", strtotime($idea['created_at'])) ?></span>
        </div>

        <div class="idea-body">
            <p><strong>Summary</strong></p>
            <p><?= nl2br(htmlspecialchars($idea['summary'])) ?></p>

            <p><strong>Justification</strong></p>
            <p><?= nl2br(htmlspecialchars($idea['justification'])) ?></p>

            <p><strong>Estimated Budget:</strong> <?= htmlspecialchars($idea['estimated_budget']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($idea['location']) ?></p>
        </div>

        <?php if (!$isGuest): ?>
            <!-- Reactions -->
            <div class="idea-reactions"
                data-idea-id="<?= $idea_id ?>"
                data-owner="<?= $isOwner ? '1' : '0' ?>">

                <button class="react-btn" data-type="like">❤️ <span><?= $counts['like'] ?? 0 ?></span></button>
                <button class="react-btn" data-type="upvote">👍 <span><?= $counts['upvote'] ?? 0 ?></span></button>
                <button class="react-btn" data-type="downvote">👎 <span><?= $counts['downvote'] ?? 0 ?></span></button>

                <div class="share-wrapper">
                    <button class="share-btn" onclick="toggleShare()">🔗 Share</button>

                    <div class="share-menu" id="shareMenu">
                        <a id="shareWhatsapp" target="_blank">WhatsApp</a>
                        <a id="shareX" target="_blank">X</a>
                        <a id="shareFacebook" target="_blank">Facebook</a>
                        <button onclick="copyLink()">Copy Link</button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="guest-note">
                Log in to react or contribute ideas.
            </p>
        <?php endif; ?>

    </div>

    <script>
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent(document.title);

        document.getElementById("shareWhatsapp").href =
            `https://wa.me/?text=${text}%20${url}`;

        document.getElementById("shareX").href =
            `https://x.com/intent/tweet?text=${text}&url=${url}`;

        document.getElementById("shareFacebook").href =
            `https://www.facebook.com/sharer/sharer.php?u=${url}`;

        function toggleShare() {
            document.getElementById("shareMenu").classList.toggle("show");
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href);
            alert("Link copied to clipboard");
        }
    </script>

</body>

</html>