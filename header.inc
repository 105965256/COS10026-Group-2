<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'ShopSphere'; ?></title>
</head>
<body>

<header class="site-header <?php echo isset($header_class) ? $header_class : ''; ?>">
    <div class="container">
        <?php if (basename($_SERVER['PHP_SELF']) === 'index.php'): ?>
            <section class="home-hero">
                <div class="hero-copy">
                    <p class="eyebrow">G05 - E-Commerce &amp; Digital Retail Platform</p>
                    <div class="brand-row">
                        <div class="brand-logo" aria-hidden="true">S</div>
                        <div>
                            <h1>ShopSphere</h1>
                            <p class="hero-slogan">Build better online shopping journeys.</p>
                        </div>
                    </div>
                    <p class="hero-summary" style="max-width: 54rem;">
                        ShopSphere is an online retail platform growing its digital team to improve customer-facing websites, product listings, and seamless application workflows for modern retail.
                    </p>

                    <form class="home-search" action="jobs.php" method="get" aria-label="Search for jobs">
                        <label class="search-label" for="job-search">Search available roles</label>
                        <div class="search-row">
                            <input type="search" id="job-search" name="search" placeholder="Search by role, skill, or job reference">
                            <button type="submit">Search</button>
                        </div>
                    </form>
                </div>

                <figure class="hero-illustration">
                    <svg class="hero-graphic" viewBox="0 0 520 360" role="img" aria-labelledby="home-graphic-title home-graphic-desc">
                        <title id="home-graphic-title">Illustration of a digital retail dashboard</title>
                        <desc id="home-graphic-desc">A stylised e-commerce dashboard showing product cards, analytics, and a mobile shopping interface.</desc>
                        <rect x="35" y="38" width="450" height="280" rx="28" fill="#ffffff" opacity="0.96" />
                        <rect x="70" y="74" width="185" height="26" rx="13" fill="#dbeafe" />
                        <rect x="70" y="122" width="165" height="128" rx="18" fill="#eff6ff" />
                        <rect x="90" y="142" width="125" height="58" rx="14" fill="#bfdbfe" />
                        <rect x="278" y="122" width="172" height="56" rx="18" fill="#e0f2fe" />
                        <rect x="278" y="194" width="172" height="56" rx="18" fill="#dcfce7" />
                        <circle cx="398" cy="86" r="28" fill="#fef3c7" />
                        <path d="M390 80h16l-3 17h-11z" fill="#1d4ed8" />
                        <circle cx="394" cy="102" r="4" fill="#1d4ed8" />
                        <circle cx="403" cy="102" r="4" fill="#1d4ed8" />
                        <rect x="356" y="210" width="112" height="120" rx="22" fill="#123d7a" />
                        <rect x="371" y="226" width="82" height="16" rx="8" fill="#dbeafe" />
                        <rect x="371" y="254" width="82" height="40" rx="10" fill="#60a5fa" />
                        <rect x="371" y="302" width="55" height="10" rx="5" fill="#cfe4ff" />
                    </svg>
                    <figcaption>ShopSphere's digital retail team supports customer experiences across desktop and mobile shopping journeys.</figcaption>
                </figure>
            </section>
        <?php else: ?>
            <div class="hero-text">
                <p class="eyebrow">G05 - E-Commerce &amp; Digital Retail Platform</p>
                <h1><?php echo isset($page_heading) ? htmlspecialchars($page_heading) : 'ShopSphere'; ?></h1>
                <p class="hero-summary" style="max-width: 52rem;">
                    <?php echo isset($page_description) ? htmlspecialchars($page_description) : ''; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</header>