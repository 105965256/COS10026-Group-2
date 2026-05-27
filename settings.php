<?php
mysqli_report(MYSQLI_REPORT_OFF);

$host = "localhost";
$user = "root";
$pwd = "";
$sql_db = "project2";

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function clean_input($value) {
    return trim(stripslashes((string)$value));
}

function db_fail($message) {
    http_response_code(500);
    echo "<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"utf-8\"><title>Database Error</title>";
    echo "<link rel=\"stylesheet\" href=\"styles.css\"></head><body><main class=\"container page-main\">";
    echo "<section class=\"content-card message-box error-box\"><h1>Database error</h1><p>" . h($message) . "</p></section>";
    echo "</main></body></html>";
    exit;
}

function db_connect() {
    global $host, $user, $pwd, $sql_db;

    $conn = @mysqli_connect($host, $user, $pwd);
    if (!$conn) {
        db_fail("Unable to connect to MySQL. Please start MySQL in XAMPP and try again.");
    }

    mysqli_set_charset($conn, "utf8mb4");
    $safe_db = preg_replace("/[^A-Za-z0-9_]/", "", $sql_db);
    if ($safe_db === "") {
        db_fail("Database name is not valid.");
    }

    if (!mysqli_select_db($conn, $safe_db)) {
        if (!mysqli_query($conn, "CREATE DATABASE `$safe_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
            db_fail(mysqli_error($conn));
        }

        if (!mysqli_select_db($conn, $safe_db)) {
            db_fail(mysqli_error($conn));
        }
    }

    return $conn;
}

function db_execute($conn, $sql) {
    if (!mysqli_query($conn, $sql)) {
        db_fail(mysqli_error($conn));
    }
}

function safe_identifier($identifier, $label = "Identifier") {
    $safe = preg_replace("/[^A-Za-z0-9_]/", "", (string)$identifier);
    if ($safe === "" || $safe !== (string)$identifier) {
        db_fail($label . " is not valid.");
    }
    return $safe;
}

function bind_params($stmt, $types, $params) {
    if ($types === "" || count($params) === 0) {
        return;
    }

    $bind_values = array($types);
    foreach ($params as $key => $value) {
        $bind_values[] = &$params[$key];
    }
    call_user_func_array(array($stmt, "bind_param"), $bind_values);
}

function prepared_result($conn, $sql, $types = "", $params = array()) {
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        db_fail(mysqli_error($conn));
    }

    bind_params($stmt, $types, $params);
    if (!mysqli_stmt_execute($stmt)) {
        db_fail(mysqli_stmt_error($stmt));
    }

    return mysqli_stmt_get_result($stmt);
}

function table_exists($conn, $table) {
    $result = prepared_result(
        $conn,
        "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1",
        "s",
        array($table)
    );
    return mysqli_fetch_assoc($result) !== null;
}

function table_unusable_error($conn) {
    return in_array(mysqli_errno($conn), array(1146, 1812, 1932), true);
}

function drop_table_for_rebuild($conn, $table) {
    $safe_table = safe_identifier($table, "Table name");
    if (!mysqli_query($conn, "DROP TABLE IF EXISTS `$safe_table`")) {
        db_fail("The `$safe_table` table appears damaged and could not be reset automatically. Restart MySQL in XAMPP, drop the project2 database in phpMyAdmin, then reload the page. MySQL said: " . mysqli_error($conn));
    }
}

function table_needs_create($conn, $table) {
    $safe_table = safe_identifier($table, "Table name");

    if (!table_exists($conn, $safe_table)) {
        return true;
    }

    $result = mysqli_query($conn, "SELECT 1 FROM `$safe_table` LIMIT 1");
    if ($result) {
        return false;
    }

    if (table_unusable_error($conn)) {
        drop_table_for_rebuild($conn, $safe_table);
        return true;
    }

    db_fail(mysqli_error($conn));
}

function scalar_count($conn, $table) {
    $safe_table = safe_identifier($table, "Table name");
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$safe_table`");
    if (!$result) {
        db_fail(mysqli_error($conn));
    }
    $row = mysqli_fetch_assoc($result);
    return (int)$row["total"];
}

function ensure_eoi_table($conn) {
    if (!table_needs_create($conn, "eoi")) {
        return;
    }

    $sql = "
        CREATE TABLE IF NOT EXISTS eoi (
            EOInumber INT UNSIGNED NOT NULL AUTO_INCREMENT,
            job_reference VARCHAR(5) NOT NULL,
            first_name VARCHAR(20) NOT NULL,
            last_name VARCHAR(20) NOT NULL,
            date_of_birth DATE NOT NULL,
            gender ENUM('female','male','other','prefer-not-to-say') NOT NULL,
            street_address VARCHAR(40) NOT NULL,
            suburb_town VARCHAR(40) NOT NULL,
            state ENUM('VIC','NSW','QLD','NT','WA','SA','TAS','ACT') NOT NULL,
            postcode CHAR(4) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone_number VARCHAR(12) NOT NULL,
            skills TEXT NOT NULL,
            other_skills TEXT,
            status ENUM('New','Current','Final') NOT NULL DEFAULT 'New',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (EOInumber),
            INDEX idx_eoi_job_reference (job_reference),
            INDEX idx_eoi_applicant_name (first_name, last_name),
            INDEX idx_eoi_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    db_execute($conn, $sql);
}

function ensure_jobs_table($conn) {
    if (table_needs_create($conn, "jobs")) {
    $sql = "
        CREATE TABLE IF NOT EXISTS jobs (
            job_reference VARCHAR(5) NOT NULL,
            category VARCHAR(80) NOT NULL,
            title VARCHAR(120) NOT NULL,
            summary TEXT NOT NULL,
            salary_range VARCHAR(80) NOT NULL,
            reports_to VARCHAR(100) NOT NULL,
            responsibilities TEXT NOT NULL,
            essential_requirements TEXT NOT NULL,
            preferable_requirements TEXT NOT NULL,
            PRIMARY KEY (job_reference)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    db_execute($conn, $sql);
    }

    $jobs = array(
        array(
            "FWD25",
            "Digital Development",
            "Front-End Web Developer",
            "We are seeking a Front-End Web Developer to support the improvement of our customer-facing retail website. This role focuses on building accessible, responsive, and visually consistent web pages.",
            "AUD 78,000 - AUD 92,000 per year",
            "Digital Development Manager",
            "Develop and maintain HTML and CSS for website pages.\nImprove page layout, navigation, and responsive behaviour.\nSupport product listing templates and promotional landing pages.\nWork with designers to implement consistent brand styling.\nCheck pages for accessibility, usability, and cross-browser compatibility.",
            "Strong knowledge of HTML5 and CSS3.\nUnderstanding of responsive web design principles.\nAbility to create clear and semantic page structures.\nGood attention to detail and problem-solving skills.\nAbility to work effectively in a team environment.",
            "Experience with e-commerce or retail websites.\nFamiliarity with accessibility standards and inclusive design.\nBasic understanding of GitHub or version control workflows.\nExperience working with designers or content teams."
        ),
        array(
            "UXC25",
            "User Experience & Content",
            "UX Content & Product Listing Coordinator",
            "We are looking for a UX Content & Product Listing Coordinator to improve the clarity and usability of product information across our online retail platform. This role supports the delivery of accessible and customer-friendly digital content.",
            "AUD 65,000 - AUD 76,000 per year",
            "User Experience Lead",
            "Prepare and update product descriptions and listing content.\nReview website text for clarity, consistency, and tone.\nSupport improvements to application and enquiry forms.\nWork with the UX team to make content easy to scan and understand.\nHelp ensure online information is accurate and customer-friendly.",
            "Strong written communication skills.\nAbility to organise information clearly and accurately.\nUnderstanding of user-focused digital content.\nGood teamwork and time management skills.\nConfidence using web-based systems and online platforms.",
            "Experience in e-commerce, retail, or digital marketing.\nAwareness of accessibility and inclusive communication.\nExperience editing website or catalogue content.\nInterest in customer experience and online shopping behaviour."
        )
    );

    $sql = "
        INSERT INTO jobs (job_reference, category, title, summary, salary_range, reports_to, responsibilities, essential_requirements, preferable_requirements)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            category = VALUES(category),
            title = VALUES(title),
            summary = VALUES(summary),
            salary_range = VALUES(salary_range),
            reports_to = VALUES(reports_to),
            responsibilities = VALUES(responsibilities),
            essential_requirements = VALUES(essential_requirements),
            preferable_requirements = VALUES(preferable_requirements)
    ";
    foreach ($jobs as $job) {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            db_fail(mysqli_error($conn));
        }
        bind_params($stmt, "sssssssss", $job);
        if (!mysqli_stmt_execute($stmt)) {
            db_fail(mysqli_stmt_error($stmt));
        }
    }
}

function ensure_user_table($conn) {
    if (table_needs_create($conn, "user")) {
    $sql = "
        CREATE TABLE IF NOT EXISTS `user` (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            username VARCHAR(50) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_username (username)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    db_execute($conn, $sql);
    }

    $result = prepared_result($conn, "SELECT id, password_hash FROM `user` WHERE username = ?", "s", array("admin"));
    $admin = mysqli_fetch_assoc($result);
    $hash = password_hash("admin", PASSWORD_DEFAULT);

    if (!$admin) {
        $stmt = mysqli_prepare($conn, "INSERT INTO `user` (username, password_hash) VALUES (?, ?)");
        if (!$stmt) {
            db_fail(mysqli_error($conn));
        }
        bind_params($stmt, "ss", array("admin", $hash));
        if (!mysqli_stmt_execute($stmt)) {
            db_fail(mysqli_stmt_error($stmt));
        }
        return;
    }

    if (!password_verify("admin", $admin["password_hash"])) {
        $stmt = mysqli_prepare($conn, "UPDATE `user` SET password_hash = ? WHERE username = ?");
        if (!$stmt) {
            db_fail(mysqli_error($conn));
        }
        bind_params($stmt, "ss", array($hash, "admin"));
        if (!mysqli_stmt_execute($stmt)) {
            db_fail(mysqli_stmt_error($stmt));
        }
    }
}

function ensure_about_table($conn) {
    if (table_needs_create($conn, "about")) {
    $sql = "
        CREATE TABLE IF NOT EXISTS about (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            member_name VARCHAR(80) NOT NULL,
            student_id VARCHAR(20) NOT NULL,
            project_1_contribution TEXT NOT NULL,
            project_2_contribution TEXT NOT NULL,
            quote_original TEXT NOT NULL,
            quote_english TEXT NOT NULL,
            dream_job VARCHAR(160) NOT NULL,
            coding_snack VARCHAR(80) NOT NULL,
            hometown VARCHAR(80) NOT NULL,
            display_order INT UNSIGNED NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            UNIQUE KEY unique_student_id (student_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    db_execute($conn, $sql);
    }

    $members = array(
        array(
            "Zikun Pang",
            "105965214",
            "Responsible for the apply.html and part of the about.html pages, including the home page company presentation, search feature, merged-cell recruitment table, and form styling.",
            "Database design for the EOI table, server-side application processing, validation, and confirmation workflow.",
            "细节决定成败，坚持让每一步都更扎实。",
            "Details determine success, so every step should be built on a solid foundation.",
            "Software engineer building practical digital products",
            "Iced black tea",
            "Jining, Shandong",
            1
        ),
        array(
            "Yunchen Xue",
            "105965256",
            "Responsible for the jobs.html page, including job description structure, role summaries, responsibility lists, requirement sections, and supporting sidebar content.",
            "Jobs database implementation, dynamic job listing rendering, and database-backed search functionality.",
            "积跬步千里，才能看见更远的风景。",
            "Only by taking steady steps can we reach farther horizons.",
            "Product designer for an international tech platform",
            "Sugar-free soda",
            "Qingdao, Shandong",
            2
        ),
        array(
            "Ricky Jiang",
            "106108939",
            "Responsible for the index.html and part of the about.html page refinement and shared visual presentation, including group profile content organisation and team coordination.",
            "Manager dashboard development, authentication, EOI management interface, and PHP modularisation.",
            "把简单的事情做好，就是最稳的进步。",
            "Doing simple things well is the most reliable way to keep improving.",
            "Full-stack engineer working on user-focused web products",
            "Shapes",
            "Shanghai",
            3
        )
    );

    $sql = "
        INSERT INTO about (
            member_name, student_id, project_1_contribution, project_2_contribution,
            quote_original, quote_english, dream_job, coding_snack, hometown, display_order
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            member_name = VALUES(member_name),
            project_1_contribution = VALUES(project_1_contribution),
            project_2_contribution = VALUES(project_2_contribution),
            quote_original = VALUES(quote_original),
            quote_english = VALUES(quote_english),
            dream_job = VALUES(dream_job),
            coding_snack = VALUES(coding_snack),
            hometown = VALUES(hometown),
            display_order = VALUES(display_order)
    ";

    foreach ($members as $member) {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            db_fail(mysqli_error($conn));
        }
        bind_params($stmt, "sssssssssi", $member);
        if (!mysqli_stmt_execute($stmt)) {
            db_fail(mysqli_stmt_error($stmt));
        }
    }
}

function ensure_all_tables($conn) {
    ensure_jobs_table($conn);
    ensure_eoi_table($conn);
    ensure_user_table($conn);
    ensure_about_table($conn);
}

function skill_labels() {
    return array(
        "front-end-development" => "Front-End HTML/CSS",
        "product-listing-management" => "Product Listing Management",
        "cms-content-updates" => "CMS Content Updates",
        "accessibility-testing" => "Accessibility Testing"
    );
}

function gender_labels() {
    return array(
        "female" => "Female",
        "male" => "Male",
        "other" => "Other",
        "prefer-not-to-say" => "Prefer not to say"
    );
}

function status_options() {
    return array("New", "Current", "Final");
}

function start_project_session() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        $session_path = __DIR__ . DIRECTORY_SEPARATOR . "sessions";
        if (!is_dir($session_path)) {
            mkdir($session_path, 0777, true);
        }
        session_save_path($session_path);
        session_start();
    }
}

function csrf_token() {
    start_project_session();
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

function csrf_valid($token) {
    start_project_session();
    return isset($_SESSION["csrf_token"]) && hash_equals($_SESSION["csrf_token"], (string)$token);
}
?>
