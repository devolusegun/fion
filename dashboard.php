<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "db.php"; // contains $conn

$user_id = $_SESSION['user_id'];

// Retrieve personal info from registered table
$stmt = $conn->prepare("
    SELECT users.email, registered.firstname, registered.lastname
    FROM users
    JOIN registered ON users.id = registered.id
    WHERE users.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$notif = $conn->prepare("SELECT message FROM notifications WHERE user_id=? AND is_read=0 ORDER BY created_at DESC LIMIT 3");
$notif->bind_param("i", $user_id);
$notif->execute();
$notifications = $notif->get_result();

if (!$user) {
    die("User details not found.");
}

$p = $conn->prepare("SELECT phone, address, dob, gender, profile_completed FROM registered WHERE id=?");
$p->bind_param("i", $user_id);
$p->execute();
$profile = $p->get_result()->fetch_assoc();

$completed = 0;
$total = 4;

if (!empty($profile['phone']))
    $completed++;
if (!empty($profile['address']))
    $completed++;
if (!empty($profile['dob']))
    $completed++;
if (!empty($profile['gender']))
    $completed++;

$percent = intval(($completed / $total) * 100);

if ($percent === 100 && !$profile['profile_completed']) {
    $conn->query("UPDATE registered SET profile_completed=1 WHERE id=$user_id");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css" />
    <script defer src="js/dashboard.js"></script>
</head>

<body class="light">

    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="logo">
            FION
            <!-- later replace text with <img src="logo.png" alt="FION"> -->
        </div>

        <div class="nav-right">
            <a href="donate.php" class="donate-btn">Donate</a>

            <button id="modeToggle" class="mode-btn" aria-label="Toggle dark mode">
                <svg class="mode-icon" viewBox="0 0 24 24" width="20" aria-hidden="true">
                    <path fill="currentColor" d="M21.64 13a1 1 0 0 0-1.05-.14
                 8 8 0 0 1-3.37.73
                 8.15 8.15 0 0 1-8.14-8.14
                 8.59 8.59 0 0 1 .25-2
                 1 1 0 0 0-1.33-1.15
                 A10 10 0 1 0 22 14.05
                 a1 1 0 0 0-.36-1.05Z" />
                </svg>
            </button>

            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </nav>

    <!-- Main Layout -->
    <div class="container">

        <!-- LEFT COLUMN: ICON GRID MENU -->
        <section class="left-column">
            <!-- Carousel Container -->
            <div class="carousel-card">
                <div class="carousel">
                    <div class="slides">

                        <div class="slide">
                            <img src="https://picsum.photos/id/183/2316/1544.jpg?text=Slide+1" alt="Slide 1">
                        </div>

                        <div class="slide">
                            <img src="https://picsum.photos/id/242/2316/1544?text=Slide+2" alt="Slide 2">
                        </div>

                    </div>

                    <!-- Navigation Dots -->
                    <div class="carousel-dots">
                        <span class="dot active" data-index="0"></span>
                        <span class="dot" data-index="1"></span>
                    </div>
                </div>
            </div>

            <!-- <h2>Menu</h2> -->

            <div class="icon-grid">

                <a href="ideas.php?category=dredging_river_niger" class="icon-card">
                    <div>
                        <div class="icon-svg">
                            <!-- Document Icon -->
                            <svg viewBox="0 0 24 24">
                                <path d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" />
                            </svg>
                        </div>
                        <p>Dredging of River</p>
                    </div>
                </a>

                <a href="ideas.php?category=steel_industry" class="icon-card">
                    <div>
                        <div class="icon-svg">
                            <!-- Mail Icon -->
                            <svg viewBox="0 0 24 24">
                                <path d="M4 4h16v16H4V4zm8 8l8-5H4l8 5zm0 2l-8-5v9h16v-9l-8 5z" />
                            </svg>
                        </div>
                        <p>Steel Industry</p>
                    </div>
                </a>

                <a href="ideas.php?category=retail_system_reform" class="icon-card">
                    <div>
                        <div class="icon-svg">
                            <!-- Bell Icon -->
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2zm6-6V11a6 6 0 1 0-12 0v5l-2 2v1h16v-1l-2-2z" />
                            </svg>
                        </div>
                        <p>Retail System Reform</p>
                    </div>
                </a>

                <a href="ideas.php?category=land_geopin_mapping" class="icon-card">
                    <div>
                        <div class="icon-svg">
                            <!-- Pie Chart Icon -->
                            <svg viewBox="0 0 24 24">
                                <path d="M11 2v20a10 10 0 0 0 0-20zm2 0v8h8a8 8 0 0 0-8-8zm0 10v8a8 8 0 0 0 8-8h-8z" />
                            </svg>
                        </div>
                        <p>Land Mapping</p>
                    </div>
                </a>


                <!--<a href="profile.php" class="icon-card">
                    <div class="icon-svg">-->
                <!-- User Icon-->
                <!--<svg viewBox="0 0 24 24">
                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-5 0-9 2.5-9 6v2h18v-2c0-3.5-4-6-9-6z" />
                        </svg>
                    </div>
                    <p>Profile</p>
                </a>-->
                <a href="profile.php" class="icon-card profile-card">

                    <?php if ($percent < 100): ?>
                        <span class="status-badge incomplete">
                            <?= $percent ?>%
                        </span>
                    <?php else: ?>
                        <span class="status-badge complete">✔</span>
                    <?php endif; ?>

                    <div class="icon-svg">
                        <!-- User Icon -->
                        <svg viewBox="0 0 24 24">
                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-5 0-9 2.5-9 6v2h18v-2c0-3.5-4-6-9-6z" />
                        </svg>
                    </div>

                    <p>Profile</p>

                    <?php if ($percent < 100): ?>
                        <div class="mini-progress">
                            <div class="mini-progress-fill" style="width: <?= $percent ?>%"></div>
                        </div>
                    <?php endif; ?>

                </a>


                <a href="ideas.php?category=water_dam_city_planning" class="icon-card">
                    <div>
                        <div class="icon-svg">
                            <!-- Gear Icon -->
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M12 8a4 4 0 1 1-4 4 4 4 0 0 1 4-4zm9.4 4a7.4 7.4 0 0 0-.2-1.8l2.1-1.6-2-3.4-2.5 1a7.4 7.4 0 0 0-1.6-.9L16.3 2h-4.6l-.9 2.3a7.4 7.4 0 0 0-1.6.9l-2.5-1-2 3.4 2.1 1.6a7.4 7.4 0 0 0 0 3.5L2.7 14l2 3.4 2.5-1a7.4 7.4 0 0 0 1.6.9l.9 2.3h4.6l.9-2.3a7.4 7.4 0 0 0 1.6-.9l2.5 1 2-3.4-2.1-1.6a7.4 7.4 0 0 0 .2-1.8z" />
                            </svg>
                        </div>
                        <p>Water Dam, Pipeborne Water & Planning</p>
                    </div>
                </a>

            </div>

        </section>

        <!-- RIGHT COLUMN -->
        <section class="right-column">

            <!-- Personal Info -->
            <div class="card profile-card">
                <h3>Welcome,
                    <?php echo htmlspecialchars($user['firstname']); ?>
                </h3>
                <p>
                    <?php echo htmlspecialchars($user['email']); ?>
                </p>
            </div>

            <!-- Quick Links -->
            <!--<div class="grid">
                <a href="submit_idea.php" class="card action-card">Submit Project Idea</a>
                <a href="support.php" class="card action-card">Contact Support</a>
            </div>-->
            <div class="grid">

                <?php if ($percent >= 75): ?>
                    <a href="submit_idea.php" class="card action-card">
                        Submit Project Idea
                    </a>
                <?php else: ?>
                    <div class="card action-card disabled-card">
                        Submit Project Idea
                        <small>Complete profile (
                            <?= $percent ?>%)
                        </small>
                    </div>
                <?php endif; ?>

                <a href="support.php" class="card action-card">
                    Contact Support
                </a>

            </div>

            <!-- Latest News MOVED HERE -->
            <h2>Latest News</h2>
            <div class="card news-card">News Item 1</div>
            <div class="card news-card">News Item 2</div>
            <div class="card news-card">News Item 3</div>

            <!-- User Notifications -->
            <div class="card notify-card">
                <h4>Your Notifications</h4>
                <?php while ($row = $notifications->fetch_assoc()): ?>
                    <p>•
                        <?= $row['message'] ?>
                    </p>
                <?php endwhile; ?>
            </div>

            <!-- Admin Notifications -->
            <div class="card notify-card">
                <h4>Pending Reviews</h4>
                <p>3 project ideas awaiting admin review.</p>
            </div>

            <!-- Progress -->
            <div class="card progress-card">
                <h4>Your Contribution Progress</h4>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 60%;"></div>
                </div>
                <p>60% – Keep going!</p>
            </div>

        </section>
    </div>

    <script>
        // Simple 2-slide carousel
        let currentSlide = 0;
        const slides = document.querySelector(".slides");
        const dots = document.querySelectorAll(".dot");

        function goToSlide(index) {
            currentSlide = index;
            slides.style.transform = `translateX(-${index * 100}%)`;

            dots.forEach(dot => dot.classList.remove("active"));
            dots[index].classList.add("active");
        }

        // Auto slide every 4 seconds
        setInterval(() => {
            currentSlide = (currentSlide + 1) % 2;
            goToSlide(currentSlide);
        }, 4000);

        // Dot click navigation
        dots.forEach(dot => {
            dot.addEventListener("click", () => {
                goToSlide(dot.dataset.index);
            });
        });
    </script>

    <!-- ===== FOOTER ===== -->
    <footer class="dash-footer improved-footer">
        <div class="footer-wrapper">

            <!-- Left: Logo & Brand -->
            <div class="footer-section footer-brand">
                <h2 class="footer-logo">Fion<span>CES</span></h2>
                <p class="footer-desc">A modern civic ideation platform for smart governance.</p>

                <div class="footer-social">
                    <a href="https://x.com/FION__NG" aria-label="X (formerly Twitter)">
                        <svg viewBox="0 0 24 24" width="22" aria-hidden="true">
                            <path fill="currentColor"
                                d="M18.245 2H21l-6.39 7.3L22 22h-6.8l-4.64-6.18L4.9 22H2l6.86-7.84L2 2h6.97l4.2 5.6L18.245 2Zm-1.19 18h1.87L8.04 4H6.06l10.995 16Z" />
                        </svg>
                    </a>

                    <a href="https://www.instagram.com/foundation.industrialization?igsh=OTZkOGp5NGRjcjNt"
                        aria-label="Instagram">
                        <svg viewBox="0 0 24 24" width="22" aria-hidden="true">
                            <path fill="currentColor"
                                d="M7 2C4.24 2 2 4.24 2 7v10c0 2.76 2.24 5 5 5h10c2.76 0 5-2.24 5-5V7c0-2.76-2.24-5-5-5H7Zm10 2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h10Zm-5 3.5A5.5 5.5 0 1 0 17.5 13 5.51 5.51 0 0 0 12 7.5Zm0 9A3.5 3.5 0 1 1 15.5 13 3.5 3.5 0 0 1 12 16.5Zm5.75-9.75a1.25 1.25 0 1 0 1.25 1.25 1.25 1.25 0 0 0-1.25-1.25Z" />
                        </svg>
                    </a>

                    <a href="https://www.facebook.com/share/1BrSgZvhbn/" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" width="22" aria-hidden="true">
                            <path fill="currentColor"
                                d="M13 3h4a1 1 0 0 1 1 1v3h-3c-.55 0-1 .45-1 1v3h4l-.5 4H14v9h-4v-9H7v-4h3V8c0-2.76 2.24-5 5-5Z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Middle: Quick Links -->
            <div class="footer-section footer-links">
                <h4></h4>
                <a href="#">Partnership</a>
                <a href="submit_idea.php">Submit Idea</a>
                <a href="#">Support</a>
                <a href="#">Guidelines</a>
            </div>

            <!-- Right: Legal -->
            <div class="footer-section footer-legal">
                <h4>Explore</h4>
                <a href="about.html">About us</a>
                <a href="vision.html">Our vision</a>
                <a href="terms.php">Terms</a>
                <a href="privacy.php">Privacy policy</a>
            </div>

        </div>

        <div class="footer-bottom">
            <p>©
                <?php echo date("Y"); ?> FionCES — Empowering civic innovation.
            </p>
        </div>
    </footer>

</body>

</html>