<?php
// partials/idea_nav.php
?>

<nav class="idea-nav" id="ideaNav">
    <div class="nav-inner">

        <!-- Logo -->
        <a href="dashboard.php" class="nav-logo">
            <span class="gradient-text">FION</span>
        </a>

        <!-- Links -->
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>

            <div class="nav-dropdown">
                <span>Categories ▾</span>
                <div class="dropdown-menu">
                    <a href="ideas.php?category=dredging_river_niger">Dredging & Ports</a>
                    <a href="ideas.php?category=steel_industry">Steel Industry</a>
                    <a href="ideas.php?category=retail_system_reform">Retail Reform</a>
                    <a href="ideas.php?category=land_geopin_mapping">Land Mapping</a>
                    <a href="ideas.php?category=water_dam_city_planning">Water & City Planning</a>
                </div>
            </div>

            <a href="submit_idea.php" class="nav-cta">Contribute Idea</a>
            <a href="support.php">Support</a>
        </div>

    </div>
</nav>
