<?php
$page_title = "About | CodeCrafters";
$page_heading = "Meet the team behind the project";
$page_description = "This page introduces our group members, project contributions, shared class details, and the teamwork behind our G05 recruitment website.";

include 'header.inc';
include 'nav.inc';

$members = [
    [
        'member_id' => 1,
        'member_name' => 'Zikun Pang',
        'student_id' => '105965214',
        'project_1_contribution' => 'Responsible for the apply.html and part of the about.html pages, including the home page company presentation, search feature, merged-cell recruitment table, and form styling.',
        'project_2_contribution' => 'Database design for EOI table, form processing and validation, application confirmation system.',
        'quote_original' => '细节决定成败，坚持让每一步都更扎实。',
        'quote_english' => 'Details determine success, so every step should be built on a solid foundation.',
        'dream_job' => 'Software engineer building practical digital products',
        'coding_snack' => 'Iced black tea',
        'hometown' => 'Jining, Shandong',
        'display_order' => 1
    ],
    [
        'member_id' => 2,
        'member_name' => 'Yunchen Xue',
        'student_id' => '105965256',
        'project_1_contribution' => 'Responsible for the jobs.html page, including the job description structure, role summaries, responsibility lists, requirement sections, and supporting sidebar content.',
        'project_2_contribution' => 'Jobs database implementation, dynamic job listing rendering, search functionality.',
        'quote_original' => '积跬步千里，才能看见更远的风景。',
        'quote_english' => 'Only by taking steady steps can we reach farther horizons.',
        'dream_job' => 'Product designer for an international tech platform',
        'coding_snack' => 'Sugar-free soda',
        'hometown' => 'Qingdao, Shandong',
        'display_order' => 2
    ],
    [
        'member_id' => 3,
        'member_name' => 'Ricky Jiang',
        'student_id' => '106108939',
        'project_1_contribution' => 'Responsible for the index.html and part of the about.html page refinement and shared visual presentation, including group profile content organisation and team coordination.',
        'project_2_contribution' => 'Manager dashboard development, authentication system, EOI management interface, PHP modularization.',
        'quote_original' => '把简单的事情做好，就是最稳的进步。',
        'quote_english' => 'Doing simple things well is the most reliable way to keep improving.',
        'dream_job' => 'Full-stack engineer working on user-focused web products',
        'coding_snack' => 'Shapes',
        'hometown' => 'Shanghai',
        'display_order' => 3
    ]
];
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
                            <li>Zikun Pang</li>
                            <li>Yunchen Xue</li>
                            <li>Ricky Jiang</li>
                        </ul>
                    </li>
                </ul>
            </div>

            <figure class="about-photo">
                <img src="group-photo.jpg" alt="Group photo of the three CodeCrafters team members." class="group-photo">
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
                    Our contribution summary displays each member's role and completed work in both projects.
                </p>
            </div>
        </header>

        <dl class="member-contributions">
            <?php foreach ($members as $member): ?>
                <dt>
                    <?php echo htmlspecialchars($member['member_name']); ?>
                    <span class="student-id"><abbr title="Student ID"><?php echo htmlspecialchars($member['student_id']); ?></abbr></span>
                </dt>
                <dd>
                    <strong>Project 1:</strong> <?php echo htmlspecialchars($member['project_1_contribution']); ?>
                </dd>
                <dd>
                    <strong>Project 2:</strong> <?php echo htmlspecialchars($member['project_2_contribution']); ?>
                </dd>
                <dd>
                    <span lang="zh-CN"><?php echo htmlspecialchars($member['quote_original']); ?></span>
                    <br>
                    <em>English translation:</em> <?php echo htmlspecialchars($member['quote_english']); ?>
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
                        <th scope="row"><?php echo htmlspecialchars($member['member_name']); ?></th>
                        <td><?php echo htmlspecialchars($member['dream_job']); ?></td>
                        <td><?php echo htmlspecialchars($member['coding_snack']); ?></td>
                        <td><?php echo htmlspecialchars($member['hometown']); ?></td>
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