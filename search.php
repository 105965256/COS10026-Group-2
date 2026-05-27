<?php
require_once __DIR__ . "/settings.php";

function render_search_text_list($text, $ordered = false) {
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

$search = clean_input($_GET["search"] ?? $_GET["q"] ?? "");
if ($search === "") {
    header("Location: jobs.php");
    exit;
}

$like = "%" . $search . "%";
$sql = "
    SELECT *
    FROM jobs
    WHERE job_reference LIKE ?
       OR category LIKE ?
       OR title LIKE ?
       OR summary LIKE ?
       OR responsibilities LIKE ?
       OR essential_requirements LIKE ?
       OR preferable_requirements LIKE ?
    ORDER BY job_reference
";
$result = prepared_result($conn, $sql, "sssssss", array($like, $like, $like, $like, $like, $like, $like));

$jobs = array();
while ($row = mysqli_fetch_assoc($result)) {
    $jobs[] = $row;
}

$page_title = "Search Results | ShopSphere";
$page_heading = "Job search results";
$page_description = "Database-backed job search results for ShopSphere opportunities.";

include 'header.inc';
include 'nav.inc';
?>

<main id="main-content" class="container page-main">
    <section class="page-intro">
        <p class="section-tag">Search Results</p>
        <h2>Results for "<?php echo h($search); ?>"</h2>
        <p>
            Search results are retrieved from the jobs table using PHP and MySQL.
        </p>
    </section>

    <section class="content-card">
        <header class="card-header">
            <div>
                <p class="card-tag">Search Jobs</p>
                <h2>Search again</h2>
            </div>
        </header>
        <form class="job-search-form" action="search.php" method="get" novalidate>
            <label class="search-label-dark" for="search-again">Search available roles</label>
            <div class="search-row search-row-light">
                <input type="search" id="search-again" name="search" value="<?php echo h($search); ?>" placeholder="Example: FWD25, content, accessibility">
                <button type="submit">Search</button>
            </div>
        </form>
    </section>

    <?php if (count($jobs) === 0): ?>
        <section class="content-card">
            <p>No jobs matched your search.</p>
            <p><a href="jobs.php">View all jobs</a></p>
        </section>
    <?php else: ?>
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
                        <?php render_search_text_list($job["responsibilities"], true); ?>
                    </section>

                    <section class="info-panel">
                        <h3>Essential Requirements</h3>
                        <?php render_search_text_list($job["essential_requirements"]); ?>
                    </section>

                    <section class="info-panel">
                        <h3>Preferable Requirements</h3>
                        <?php render_search_text_list($job["preferable_requirements"]); ?>
                    </section>
                </div>

                <p class="job-apply-link">
                    <a href="apply.php?job=<?php echo urlencode($job["job_reference"]); ?>">Apply for <?php echo h($job["job_reference"]); ?></a>
                </p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php include 'footer.inc'; ?>
