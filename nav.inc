<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="site-nav" aria-label="Main navigation">
    <div class="container">
        <ul>
            <li><a href="index.php" <?php echo $current_page === 'index.php' ? 'class="current-page" aria-current="page"' : ''; ?>>Home</a></li>
            <li><a href="jobs.php" <?php echo $current_page === 'jobs.php' ? 'class="current-page" aria-current="page"' : ''; ?>>Jobs</a></li>
            <li><a href="apply.php" <?php echo $current_page === 'apply.php' ? 'class="current-page" aria-current="page"' : ''; ?>>Apply</a></li>
            <li><a href="about.php" <?php echo $current_page === 'about.php' ? 'class="current-page" aria-current="page"' : ''; ?>>About</a></li>
            <li><a href="login.php" <?php echo $current_page === 'login.php' ? 'class="current-page" aria-current="page"' : ''; ?>>Manager Login</a></li>
        </ul>
    </div>
</nav>