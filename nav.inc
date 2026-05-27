<?php
$current_page = basename($_SERVER['PHP_SELF']);
$nav_items = array(
    array('href' => 'index.php', 'label' => 'Home', 'pages' => array('index.php')),
    array('href' => 'jobs.php', 'label' => 'Jobs', 'pages' => array('jobs.php', 'search.php')),
    array('href' => 'apply.php', 'label' => 'Apply', 'pages' => array('apply.php', 'process_eoi.php')),
    array('href' => 'about.php', 'label' => 'About', 'pages' => array('about.php')),
    array('href' => 'login.php', 'label' => 'Manager Login', 'pages' => array('login.php', 'manage.php'))
);
?>

<nav class="site-nav" aria-label="Main navigation">
    <div class="container">
        <ul>
            <?php foreach ($nav_items as $item): ?>
                <?php $is_current = in_array($current_page, $item['pages'], true); ?>
                <li>
                    <a href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $is_current ? 'class="current-page" aria-current="page"' : ''; ?>>
                        <?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
