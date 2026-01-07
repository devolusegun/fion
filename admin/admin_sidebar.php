<aside class="admin-sidebar">

  <div class="sidebar-header">
    <h4>Admin Panel</h4>
  </div>

  <ul class="sidebar-menu">

    <li class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
      <a href="index.php">Overview</a>
    </li>

    <li class="<?= strpos($_SERVER['PHP_SELF'], 'idea') !== false ? 'active' : '' ?>">
      <a href="ideas.php">Ideas Moderation</a>
    </li>

    <li class="<?= strpos($_SERVER['PHP_SELF'], 'support') !== false ? 'active' : '' ?>">
      <a href="support.php">Support Tickets</a>
    </li>

    <li class="<?= strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : '' ?>">
      <a href="users.php">Users</a>
    </li>

    <li class="<?= strpos($_SERVER['PHP_SELF'], 'stats') !== false ? 'active' : '' ?>">
      <a href="stats.php">Platform Stats</a>
    </li>

  </ul>

</aside>
