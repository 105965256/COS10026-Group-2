<?php
require_once __DIR__ . "/settings.php";

function render_text_list($text, $ordered = false) {
    $items = preg_split("/\r\n|\r|\n/", (string)$text);
    $tag = $ordered ? "ol" : "ul";
    echo "<" . $tag . ">";
    foreach ($items as $item) {
        $item = trim($item);
        if ($item !== "") {
            echo "<li>" . h($item) . "</li>";
        }
    }
    echo "</" . $tag . ">";
}

$conn = db_connect();
ensure_jobs_table($conn);

$result = mysqli_query($conn, "SELECT * FROM jobs ORDER BY job_reference");
if (!$result) {
    db_fail(mysqli_error($conn));
}

$jobs = array();
while ($row = mysqli_fetch_assoc($result)) {
    $jobs[] = $row;
}

$page_title = "Jobs | ShopSphere";
$page_heading = "Careers at ShopSphere";
$page_description = "Explore current opportunities to improve your customer-facing website, product listings, and digital workflows with ShopSphere.";

include 'header.inc';
include 'nav.inc';
?>

<main id="main-content" class="container page-main">
    <section class="page-intro">
        <p class="section-tag">Current Opportunities</p>
        <h2>Current Opportunities</h2>
        <p>
            At ShopSphere, we build customer-focused online retail experiences that make shopping simpler, faster, and more accessible. As our platform continues to grow, we are looking for talented individuals to join our team.
        </p>
    </section>

    <div class="page-with-sidebar">
        <section class="page-content" aria-label="Job listings">
            <section class="content-card">
                <header class="card-header">
                    <div>
                        <p class="card-tag">Search Jobs</p>
                        <h2>Find a role</h2>
                        <p class="card-summary">Search by job reference, title, category, summary, or requirement keywords.</p>
                    </div>
                </header>
                <form class="job-search-form" action="search.php" method="get" novalidate>
                    <label class="search-label-dark" for="job-search-page">Search available roles</label>
                    <div class="search-row search-row-light">
                        <input type="search" id="job-search-page" name="search" placeholder="Example: FWD25, content, accessibility">
                        <button type="submit">Search</button>
                    </div>
                </form>
            </section>

            <?php if (count($jobs) > 0): ?>
                <?php foreach ($jobs as $job): ?>
                    <article class="content-card">
                        <header class="card-header">
                            <div>
                                <p class="card-tag"><?php echo h($job["category"]); ?></p>
                                <h2><?php echo h($job["title"]); ?></h2>
                                <p class="card-summary">
                                    <?php echo h($job["summary"]); ?>
                                </p>
                            </div>

                            <div class="job-meta-box">
                                <p><strong>Ref:</strong> <?php echo h($job["job_reference"]); ?></p>
                                <p><strong>Salary:</strong> <?php echo h($job["salary_range"]); ?></p>
                                <p><strong>Reports to:</strong> <?php echo h($job["reports_to"]); ?></p>
                            </div>
                        </header>

                        <div class="info-grid">
                            <section class="info-panel">
                                <h3>Key Responsibilities</h3>
                                <?php render_text_list($job["responsibilities"], true); ?>
                            </section>

                            <section class="info-panel">
                                <h3>Essential Requirements</h3>
                                <?php render_text_list($job["essential_requirements"]); ?>
                            </section>

                            <section class="info-panel">
                                <h3>Preferable Requirements</h3>
                                <?php render_text_list($job["preferable_requirements"]); ?>
                            </section>
                        </div>

                        <p class="job-apply-link">
                            <a href="apply.php?job=<?php echo urlencode($job["job_reference"]); ?>">Apply for <?php echo h($job["job_reference"]); ?></a>
                        </p>
                    </article>
                <?php endforeach; ?>

                <section class="content-card" aria-labelledby="how-to-apply">
                    <header class="card-header">
                        <div>
                            <p class="card-tag">Application Process</p>
                            <h2 id="how-to-apply">How to Apply</h2>
                            <p class="card-summary">
                                Interested applicants should visit our <a href="apply.php">Apply page</a> to submit their application. Please include the correct job reference number when applying.
                            </p>
                        </div>
                    </header>
                </section>
            <?php else: ?>
                <div class="content-card">
                    <p>No job listings are available at the moment.</p>
                </div>
            <?php endif; ?>
        </section>

        <aside class="page-sidebar">
            <div class="sidebar-card">
                <p class="section-tag">Why Join</p>
                <h2>Why Join ShopSphere?</h2>
                <ul>
                    <li>Work on real customer-facing digital retail projects.</li>
                    <li>Collaborate with design, content, and technical teams.</li>
                    <li>Flexible and supportive team environment.</li>
                    <li>Opportunities to contribute to accessible and inclusive web design.</li>
                </ul>
            </div>

            <div class="sidebar-card acknowledgement-box">
                <h3>Inclusive Employment Statement</h3>
                <p>
                    ShopSphere welcomes applications from people of all backgrounds, including Aboriginal and Torres Strait Islander peoples.
                </p>
                <p>
                    We value inclusive hiring and respectful collaboration in our workplace.
                </p>
            </div>
        </aside>
    </div>
</main>

<?php include 'footer.inc'; ?>
