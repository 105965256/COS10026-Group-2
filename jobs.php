<?php
$page_title = "Jobs | ShopSphere";
$page_heading = "Careers at ShopSphere";
$page_description = "Explore current opportunities to improve your customer-facing website, product listings, and digital workflows with ShopSphere.";

include 'header.inc';
include 'nav.inc';

$search_query = '';
$search_results = false;
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $search_results = true;
}
$jobs = [
    [
        'job_id' => 1,
        'job_reference' => 'FWD25',
        'job_title' => 'Front-End Web Developer',
        'job_category' => 'Digital Development',
        'job_summary' => 'We are seeking a Front-End Web Developer to support the improvement of our customer-facing retail website. This role focuses on building accessible, responsive, and visually consistent web pages.',
        'salary_min' => 78000,
        'salary_max' => 92000,
        'reports_to' => 'Digital Development Manager',
        'responsibilities' => "Develop and maintain HTML and CSS for website pages.\nImprove page layout, navigation, and responsive behaviour.\nSupport product listing templates and promotional landing pages.\nWork with designers to implement consistent brand styling.\nCheck pages for accessibility, usability, and cross-browser compatibility.",
        'essential_requirements' => "Strong knowledge of HTML5 and CSS3.\nUnderstanding of responsive web design principles.\nAbility to create clear and semantic page structures.\nGood attention to detail and problem-solving skills.\nAbility to work effectively in a team environment.",
        'preferable_requirements' => "Experience with e-commerce or retail websites.\nFamiliarity with accessibility standards and inclusive design.\nBasic understanding of GitHub or version control workflows.\nExperience working with designers or content teams."
    ],
    [
        'job_id' => 2,
        'job_reference' => 'UXC25',
        'job_title' => 'UX Content & Product Listing Coordinator',
        'job_category' => 'User Experience & Content',
        'job_summary' => 'We are looking for a UX Content & Product Listing Coordinator to improve the clarity and usability of product information across our online retail platform. This role supports the delivery of accessible and customer-friendly digital content.',
        'salary_min' => 65000,
        'salary_max' => 76000,
        'reports_to' => 'User Experience Lead',
        'responsibilities' => "Prepare and update product descriptions and listing content.\nReview website text for clarity, consistency, and tone.\nSupport improvements to application and enquiry forms.\nWork with the UX team to make content easy to scan and understand.\nHelp ensure online information is accurate and customer-friendly.",
        'essential_requirements' => "Strong written communication skills.\nAbility to organise information clearly and accurately.\nUnderstanding of user-focused digital content.\nGood teamwork and time management skills.\nConfidence using web-based systems and online platforms.",
        'preferable_requirements' => "Experience in e-commerce, retail, or digital marketing.\nAwareness of accessibility and inclusive communication.\nExperience editing website or catalogue content.\nInterest in customer experience and online shopping behaviour."
    ]
];

// Filter jobs if search is performed
$filtered_jobs = $jobs;
if ($search_results && !empty($search_query)) {
    $filtered_jobs = [];
    $query_lower = strtolower($search_query);
    foreach ($jobs as $job) {
        if (strpos(strtolower($job['job_title']), $query_lower) !== false || 
            strpos(strtolower($job['job_reference']), $query_lower) !== false ||
            strpos(strtolower($job['job_category']), $query_lower) !== false) {
            $filtered_jobs[] = $job;
        }
    }
}
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
            
            <?php if ($search_results && count($filtered_jobs) === 0): ?>
                <div class="content-card">
                    <p>No jobs found matching your search: <strong><?php echo htmlspecialchars($search_query); ?></strong></p>
                    <p><a href="jobs.php">View all jobs</a></p>
                </div>
            <?php elseif (count($filtered_jobs) > 0): ?>
                <?php foreach ($filtered_jobs as $job): ?>
                    <article class="content-card">
                        <header class="card-header">
                            <div>
                                <p class="card-tag"><?php echo htmlspecialchars($job['job_category']); ?></p>
                                <h2><?php echo htmlspecialchars($job['job_title']); ?></h2>
                                <p class="card-summary">
                                    <?php echo htmlspecialchars($job['job_summary']); ?>
                                </p>
                            </div>

                            <div class="job-meta-box">
                                <p><strong>Ref:</strong> <?php echo htmlspecialchars($job['job_reference']); ?></p>
                                <p><strong>Salary:</strong> AUD <?php echo number_format($job['salary_min']); ?> - AUD <?php echo number_format($job['salary_max']); ?> per year</p>
                                <p><strong>Reports to:</strong> <?php echo htmlspecialchars($job['reports_to']); ?></p>
                            </div>
                        </header>

                        <div class="info-grid">
                            <section class="info-panel">
                                <h3>Key Responsibilities</h3>
                                <ol>
                                    <?php 
                                    $responsibilities = explode("\n", $job['responsibilities']);
                                    foreach ($responsibilities as $resp): 
                                        if (!empty(trim($resp))):
                                    ?>
                                        <li><?php echo htmlspecialchars(trim($resp)); ?></li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ol>
                            </section>

                            <section class="info-panel">
                                <h3>Essential Requirements</h3>
                                <ul>
                                    <?php 
                                    $requirements = explode("\n", $job['essential_requirements']);
                                    foreach ($requirements as $req): 
                                        if (!empty(trim($req))):
                                    ?>
                                        <li><?php echo htmlspecialchars(trim($req)); ?></li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ul>
                            </section>

                            <section class="info-panel">
                                <h3>Preferable Requirements</h3>
                                <ul>
                                    <?php 
                                    $preferable = explode("\n", $job['preferable_requirements']);
                                    foreach ($preferable as $pref): 
                                        if (!empty(trim($pref))):
                                    ?>
                                        <li><?php echo htmlspecialchars(trim($pref)); ?></li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ul>
                            </section>
                        </div>
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
                    <p>No job listings available at the moment.</p>
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