<?php
require_once __DIR__ . "/settings.php";

$conn = db_connect();
ensure_about_table($conn);

$result = mysqli_query($conn, "SELECT * FROM about ORDER BY display_order, member_name");
if (!$result) {
    db_fail(mysqli_error($conn));
}

$members = array();
while ($row = mysqli_fetch_assoc($result)) {
    $members[] = $row;
}

$page_title = "About | CodeCrafters";
$page_heading = "Meet the team behind the project";
$page_description = "This page introduces our group members, project contributions, shared class details, and the teamwork behind our G05 recruitment website.";

include 'header.inc';
include 'nav.inc';
?>

<main id="main-content" class="container page-main" role="main">

    <section class="page-intro">
        <p class="section-tag">Group Information</p>
        <h2>CodeCrafters</h2>
        <p>
            We are a three-member team developing a dynamic recruitment website for the G05 E-Commerce and Digital Retail Platform scenario.
        </p>
    </section>

    <section class="content-card">
        <header class="card-header">
            <div>
                <p class="card-tag">Class Details</p>
                <h2>Group profile</h2>
                <p class="card-summary">
                    This section summarises our group name, tutorial schedule, and current team structure.
                </p>
            </div>
        </header>

        <div class="about-overview">
            <div class="info-panel">
                <h3>Group and class information</h3>
                <ul class="nested-details">
                    <li>
                        <strong>Group name:</strong> CodeCrafters
                        <ul>
                            <li><strong>Tutorial time:</strong> Thursday, 2:30pm to 4:30pm</li>
                            <li><strong>Project theme:</strong> G05 - E-Commerce &amp; Digital Retail Platform</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Group members:</strong>
                        <ul>
                            <?php foreach ($members as $member): ?>
                                <li><?php echo h($member["member_name"]); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </div>

            <figure class="about-photo">
                <img src="9a2e405ba7c57796ac91e9fdd0197e90.jpg" alt="Group photo of the three CodeCrafters team members." class="group-photo">
                <figcaption>
                    The CodeCrafters team group photo.
                </figcaption>
            </figure>
        </div>
    </section>

    <section class="content-card">
        <header class="card-header">
            <div>
                <p class="card-tag">Contributions</p>
                <h2>Member contributions and quotes</h2>
                <p class="card-summary">
                    Member contribution data is loaded from the database and can be refreshed from the about table.
                </p>
            </div>
        </header>

        <dl class="member-contributions">
            <?php foreach ($members as $member): ?>
                <dt>
                    <?php echo h($member["member_name"]); ?>
                    <span class="student-id"><abbr title="Student ID"><?php echo h($member["student_id"]); ?></abbr></span>
                </dt>
                <dd>
                    <strong>Project 1:</strong> <?php echo h($member["project_1_contribution"]); ?>
                </dd>
                <dd>
                    <strong>Project 2:</strong> <?php echo h($member["project_2_contribution"]); ?>
                </dd>
                <dd>
                    <span lang="zh-CN"><?php echo h($member["quote_original"]); ?></span>
                    <br>
                    <em>English translation:</em> <?php echo h($member["quote_english"]); ?>
                </dd>
            <?php endforeach; ?>
        </dl>

    </section>

    <section class="content-card">
        <header class="card-header">
            <div>
                <p class="card-tag">Fun Facts</p>
                <h2>Get to know our team</h2>
                <p class="card-summary">
                    This table highlights personal details about each team member.
                </p>
            </div>
        </header>

        <div class="table-wrap">
            <table class="fun-facts-table">
                <caption>Fun facts about the CodeCrafters team members</caption>
                <thead>
                <tr>
                    <th scope="col">Member</th>
                    <th scope="col">Dream job</th>
                    <th scope="col">Coding snack</th>
                    <th scope="col">Hometown</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <th scope="row"><?php echo h($member["member_name"]); ?></th>
                        <td><?php echo h($member["dream_job"]); ?></td>
                        <td><?php echo h($member["coding_snack"]); ?></td>
                        <td><?php echo h($member["hometown"]); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </section>

    <section class="content-card acknowledgement-box">
        <header class="card-header">
            <div>
                <p class="card-tag">Acknowledgement</p>
                <h2>Respectful recognition</h2>
                <p class="card-summary">
                    As a student team, we want our project to reflect respect, inclusion, and thoughtful digital communication.
                </p>
            </div>
        </header>
        <p>
            CodeCrafters acknowledges the Traditional Custodians of the lands and waterways across Australia and pays respect to Elders past and present.
        </p>
        <p>
            Our team recognises the importance of creating digital experiences that are respectful, inclusive, and welcoming to Aboriginal and Torres Strait Islander peoples.
        </p>
    </section>

</main>

<?php include 'footer.inc'; ?>
