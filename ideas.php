<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require "db.php";

$user_id = $_SESSION['user_id'];

$categories = [
    'dredging_river_niger' => 'Dredging of River Niger & Upper Benue (Inland Ports)',
    'steel_industry' => 'Steel Industry & Heavy-Duty Equipment Factories',
    'retail_system_reform' => 'Retail System Reform & National Co-operative Capital Base',
    'land_geopin_mapping' => 'Land Mapping via Geopin & Ownership System',
    'water_dam_city_planning' => 'Water Dam, Pipeborne Water & City/Town Planning'
];

$category = $_GET['category'] ?? '';

if (!array_key_exists($category, $categories)) {
    die("Invalid category");
}

$categoryBanners = [
    'dredging_river_niger' => [
        'title' => 'Dredging of River Niger & Upper Benue',
        'subtitle' => 'Unlocking inland ports, trade routes, and economic growth',
        'class' => 'banner-dredging'
    ],
    'steel_industry' => [
        'title' => 'Steel Industry & Heavy-Duty Equipment',
        'subtitle' => 'Building Nigeria’s industrial backbone',
        'class' => 'banner-steel'
    ],
    'retail_system_reform' => [
        'title' => 'Retail System Reform',
        'subtitle' => 'A national cooperative capital framework',
        'class' => 'banner-retail'
    ],
    'land_geopin_mapping' => [
        'title' => 'Land Mapping via Geopin',
        'subtitle' => 'Transparent land ownership and spatial intelligence',
        'class' => 'banner-land'
    ],
    'water_dam_city_planning' => [
        'title' => 'Water Dams & City Planning',
        'subtitle' => 'Sustainable water systems and urban development',
        'class' => 'banner-water'
    ]
];

$banner = $categoryBanners[$category];

/* Fetch approved ideas */
$stmt = $conn->prepare("
    SELECT p.id, p.title, p.summary, p.created_at,
           r.firstname, r.lastname,
           (SELECT COUNT(*) FROM idea_reactions ir WHERE ir.idea_id = p.id AND ir.reaction='like') AS likes,
           (SELECT COUNT(*) FROM idea_reactions ir WHERE ir.idea_id = p.id AND ir.reaction='upvote') AS upvotes,
           (SELECT COUNT(*) FROM idea_reactions ir WHERE ir.idea_id = p.id AND ir.reaction='downvote') AS downvotes
    FROM project_ideas p
    JOIN registered r ON r.id = p.user_id
    WHERE p.category = ? AND p.status = 'approved'
    ORDER BY p.created_at DESC
");
$stmt->bind_param("s", $category);
$stmt->execute();
$ideas = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($categories[$category]) ?></title>
    <link rel="stylesheet" href="css/ideas.css">
</head>

<body>

    <div class="page-layout">

        <!-- LEFT AD COLUMN -->
        <aside class="side-ad left-ad">
            <div class="ad-placeholder">
                <span class="ad-label">Sponsored</span>
                <p>Your Ad Here</p>
                <small>Advertising space</small>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="content-area">

            <!-- CATEGORY BANNER -->
            <section class="category-banner <?= $banner['class'] ?>">
                <div class="banner-overlay">

                    <h1 class="category-name">
                        <?= htmlspecialchars($categories[$category]) ?>
                    </h1>

                    <?php if (!empty($banner['subtitle'])): ?>
                        <p class="category-subtitle">
                            <?= htmlspecialchars($banner['subtitle']) ?>
                        </p>
                    <?php endif; ?>

                </div>
            </section>

            <!-- EXISTING CONTENT (UNCHANGED LOGIC) -->
            <div class="ideas-container">

                <?php if ($ideas->num_rows === 0): ?>
                    <p class="empty">No approved ideas yet.</p>
                <?php endif; ?>

                <?php while ($idea = $ideas->fetch_assoc()): ?>
                    <a href="idea.php?id=<?= $idea['id'] ?>" class="idea-card">

                        <h3><?= htmlspecialchars($idea['title']) ?></h3>

                        <p class="summary">
                            <?= nl2br(htmlspecialchars(substr($idea['summary'], 0, 220))) ?>…
                        </p>

                        <div class="meta">
                            <span>By <?= htmlspecialchars($idea['firstname']) ?></span>
                            <span><?= date("M j, Y", strtotime($idea['created_at'])) ?></span>
                        </div>

                        <div class="reactions">
                            ❤️ <?= $idea['likes'] ?>
                            👍 <?= $idea['upvotes'] ?>
                            👎 <?= $idea['downvotes'] ?>
                        </div>

                    </a>
                <?php endwhile; ?>

            </div>
        </main>

        <!-- RIGHT AD COLUMN -->
        <aside class="side-ad right-ad">
            <div class="ad-placeholder">
                <span class="ad-label">Sponsored</span>
                <p>Your Ad Here</p>
                <small>Advertising space</small>
            </div>
        </aside>

    </div>

    <!-- ===== FOOTER ===== -->
    <footer class="dash-footer improved-footer">
        <div class="footer-wrapper">

            <!-- Left: Logo & Brand -->
            <div class="footer-section footer-brand">
                <h2 class="footer-logo">Fion<span>CES</span></h2>
                <p class="footer-desc">A modern civic ideation platform for smart governance.</p>

                <div class="footer-social">
                    <a href="#" aria-label="Twitter">
                        <svg viewBox="0 0 24 24" width="22">
                            <path fill="currentColor"
                                d="M22.46 6c-.77.35-1.6.58-2.46.69a4.27 4.27 0 0 0 1.88-2.37 8.59 8.59 0 0 1-2.72 1.06A4.24 4.24 0 0 0 16.11 4c-2.36 0-4.28 2-4.28 4.46 0 .35.03.7.1 1.03A12.11 12.11 0 0 1 3.14 5.1a4.58 4.58 0 0 0-.58 2.25 4.52 4.52 0 0 0 1.9 3.7 4.14 4.14 0 0 1-1.94-.56v.06c0 2.2 1.49 4.03 3.47 4.45a4.18 4.18 0 0 1-1.93.07 4.29 4.29 0 0 0 4 3 8.52 8.52 0 0 1-5.41 1.94c-.35 0-.7-.02-1.04-.06A12.08 12.08 0 0 0 8.1 21c7.73 0 11.96-6.64 11.96-12.4v-.57A8.93 8.93 0 0 0 22.46 6z" />
                        </svg>
                    </a>

                    <a href="#" aria-label="LinkedIn">
                        <svg viewBox="0 0 24 24" width="22">
                            <path fill="currentColor"
                                d="M19 3A2.94 2.94 0 0 1 22 6v12a2.94 2.94 0 0 1-3 3H5a2.94 2.94 0 0 1-3-3V6a2.94 2.94 0 0 1 3-3h14M8.5 17v-7H6v7h2.5M7.2 8.3A1.45 1.45 0 1 0 7.2 5.4a1.45 1.45 0 0 0 0 2.9M18 17v-4.1c0-2-1.1-3-2.7-3a2.36 2.36 0 0 0-2.1 1.2h-.1V10H10v7h2.5v-3.5c0-.9.4-1.5 1.2-1.5s1.3.6 1.3 1.5V17H18z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Middle: Quick Links -->
            <div class="footer-section footer-links">
                <h4></h4>
                <a href="#">Partnership</a>
                <a href="#">Submit Idea</a>
                <a href="#">Support</a>
                <a href="#">Guidelines</a>
            </div>

            <!-- Right: Legal -->
            <div class="footer-section footer-legal">
                <h4>Explore</h4>
                <a href="#">About us</a>
                <a href="#">Our vision</a>
                <a href="#">Terms</a>
                <a href="#">Privacy policy</a>
            </div>

        </div>

        <div class="footer-bottom">
            <p>© <?php echo date("Y"); ?> FionCES — Empowering civic innovation.</p>
        </div>
    </footer>

</body>

</html>