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
        <div class="logo">FION</div>
        <div class="nav-right">
            <button id="modeToggle" class="mode-btn">🌙</button>
            <div class="user-name"><?php echo htmlspecialchars($user['firstname']); ?></div>
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
                            <img src="img/bamboo.png?text=Slide+1" alt="Slide 1">
                        </div>

                        <div class="slide">
                            <img src="img/christmas_ball.png?text=Slide+2" alt="Slide 2">
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

                <div class="icon-card">
                    <div class="icon-svg">
                        <!-- Document Icon -->
                        <svg viewBox="0 0 24 24">
                            <path d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" />
                        </svg>
                    </div>
                    <p>Featured</p>
                </div>

                <div class="icon-card">
                    <div class="icon-svg">
                        <!-- Mail Icon -->
                        <svg viewBox="0 0 24 24">
                            <path d="M4 4h16v16H4V4zm8 8l8-5H4l8 5zm0 2l-8-5v9h16v-9l-8 5z" />
                        </svg>
                    </div>
                    <p>Idea Threads</p>
                </div>

                <div class="icon-card">
                    <div class="icon-svg">
                        <!-- Bell Icon -->
                        <svg viewBox="0 0 24 24">
                            <path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2zm6-6V11a6 6 0 1 0-12 0v5l-2 2v1h16v-1l-2-2z" />
                        </svg>
                    </div>
                    <p>Gallery</p>
                </div>

                <div class="icon-card">
                    <div class="icon-svg">
                        <!-- Pie Chart Icon -->
                        <svg viewBox="0 0 24 24">
                            <path d="M11 2v20a10 10 0 0 0 0-20zm2 0v8h8a8 8 0 0 0-8-8zm0 10v8a8 8 0 0 0 8-8h-8z" />
                        </svg>
                    </div>
                    <p>History</p>
                </div>

                <div class="icon-card">
                    <div class="icon-svg">
                        <!-- User Icon -->
                        <svg viewBox="0 0 24 24">
                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-5 0-9 2.5-9 6v2h18v-2c0-3.5-4-6-9-6z" />
                        </svg>
                    </div>
                    <p>Partnership</p>
                </div>

                <div class="icon-card">
                    <div class="icon-svg">
                        <!-- Gear Icon -->
                        <svg viewBox="0 0 24 24">
                            <path d="M12 8a4 4 0 1 1-4 4 4 4 0 0 1 4-4zm9.4 4a7.4 7.4 0 0 0-.2-1.8l2.1-1.6-2-3.4-2.5 1a7.4 7.4 0 0 0-1.6-.9L16.3 2h-4.6l-.9 2.3a7.4 7.4 0 0 0-1.6.9l-2.5-1-2 3.4 2.1 1.6a7.4 7.4 0 0 0 0 3.5L2.7 14l2 3.4 2.5-1a7.4 7.4 0 0 0 1.6.9l.9 2.3h4.6l.9-2.3a7.4 7.4 0 0 0 1.6-.9l2.5 1 2-3.4-2.1-1.6a7.4 7.4 0 0 0 .2-1.8z" />
                        </svg>
                    </div>
                    <p>Upload</p>
                </div>

            </div>

        </section>

        <!-- RIGHT COLUMN -->
        <section class="right-column">

            <!-- Personal Info -->
            <div class="card profile-card">
                <h3>Welcome, <?php echo htmlspecialchars($user['firstname']); ?></h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <!-- Quick Links -->
            <div class="grid">
                <a href="submit_idea.php" class="card action-card">Submit Project Idea</a>
                <a href="support.php" class="card action-card">Contact Support</a>
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
                    <p>• <?= $row['message'] ?></p>
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