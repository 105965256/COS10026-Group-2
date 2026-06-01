<?php
// AI declaration: AI assistance was used to help debug this file.
require_once __DIR__ . "/settings.php";
start_project_session();

if (empty($_SESSION["manager_logged_in"])) {
    header("Location: login.php");
    exit;
}

$conn = db_connect();
ensure_all_tables($conn);

$notice = $_SESSION["manage_notice"] ?? "";
$error = $_SESSION["manage_error"] ?? "";
unset($_SESSION["manage_notice"], $_SESSION["manage_error"]);

$allowed_sort = array(
    "created_at" => "Date Applied",
    "job_reference" => "Job Reference",
    "first_name" => "First Name",
    "last_name" => "Last Name",
    "status" => "Status",
    "EOInumber" => "EOI Number"
);
$allowed_order = array(
    "DESC" => "Descending",
    "ASC" => "Ascending"
);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_valid($_POST["csrf_token"] ?? "")) {
        $_SESSION["manage_error"] = "Session token expired. Please try again.";
        header("Location: manage.php");
        exit;
    }

    $action = $_POST["manager_action"] ?? "";

    if ($action === "delete_by_job") {
        $job_ref = strtoupper(clean_input($_POST["delete_job_reference"] ?? ""));
        if (!preg_match("/^[A-Za-z0-9]{5}$/", $job_ref)) {
            $_SESSION["manage_error"] = "Enter a valid five-character job reference before deleting EOIs.";
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM eoi WHERE job_reference = ?");
            if (!$stmt) {
                db_fail(mysqli_error($conn));
            }
            bind_params($stmt, "s", array($job_ref));
            if (!mysqli_stmt_execute($stmt)) {
                db_fail(mysqli_stmt_error($stmt));
            }
            $_SESSION["manage_notice"] = mysqli_stmt_affected_rows($stmt) . " EOI record(s) deleted for job reference " . $job_ref . ".";
        }
    } elseif ($action === "update_status") {
        $eoi_number = (int)($_POST["EOInumber"] ?? 0);
        $status = clean_input($_POST["status"] ?? "");
        if ($eoi_number <= 0 || !in_array($status, status_options(), true)) {
            $_SESSION["manage_error"] = "Select a valid EOI number and status.";
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE eoi SET status = ? WHERE EOInumber = ?");
            if (!$stmt) {
                db_fail(mysqli_error($conn));
            }
            bind_params($stmt, "si", array($status, $eoi_number));
            if (!mysqli_stmt_execute($stmt)) {
                db_fail(mysqli_stmt_error($stmt));
            }
            $_SESSION["manage_notice"] = "EOI #" . $eoi_number . " status updated to " . $status . ".";
        }
    }

    header("Location: manage.php?view=all");
    exit;
}

$view = clean_input($_GET["view"] ?? "all");
$sort = clean_input($_GET["sort"] ?? "created_at");
$order = strtoupper(clean_input($_GET["order"] ?? "DESC"));
$job_ref = strtoupper(clean_input($_GET["job_reference"] ?? ""));
$first_name = clean_input($_GET["first_name"] ?? "");
$last_name = clean_input($_GET["last_name"] ?? "");

if (!array_key_exists($sort, $allowed_sort)) {
    $sort = "created_at";
}
if (!array_key_exists($order, $allowed_order)) {
    $order = "DESC";
}
if (!in_array($view, array("all", "job", "name"), true)) {
    $view = "all";
}

$conditions = array();
$params = array();
$types = "";
$result_title = "All EOIs";
$result_summary = "Showing all submitted Expressions of Interest.";

if ($view === "job") {
    if ($job_ref !== "" && preg_match("/^[A-Za-z0-9]{5}$/", $job_ref)) {
        $conditions[] = "job_reference = ?";
        $params[] = $job_ref;
        $types .= "s";
        $result_title = "EOIs for " . $job_ref;
        $result_summary = "Showing applications for job reference " . $job_ref . ".";
    } else {
        $error = $error !== "" ? $error : "Enter a valid five-character job reference.";
        $conditions[] = "1 = 0";
        $result_title = "Job Reference Search";
        $result_summary = "No valid job reference was supplied.";
    }
} elseif ($view === "name") {
    if ($first_name !== "") {
        $conditions[] = "first_name LIKE ?";
        $params[] = "%" . $first_name . "%";
        $types .= "s";
    }
    if ($last_name !== "") {
        $conditions[] = "last_name LIKE ?";
        $params[] = "%" . $last_name . "%";
        $types .= "s";
    }

    if (count($conditions) === 0) {
        $error = $error !== "" ? $error : "Enter a first name, last name, or both.";
        $conditions[] = "1 = 0";
    }

    $result_title = "Applicant Name Search";
    $result_summary = "Showing EOIs that match the supplied applicant name details.";
}

$sql = "SELECT * FROM eoi";
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY `" . $sort . "` " . $order;

if ($types !== "") {
    $result = prepared_result($conn, $sql, $types, $params);
} else {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        db_fail(mysqli_error($conn));
    }
}

$records = array();
while ($row = mysqli_fetch_assoc($result)) {
    $records[] = $row;
}

$page_title = "Manager Dashboard | ShopSphere";
$page_heading = "HR Manager Dashboard";
$page_description = "Manage Expressions of Interest submitted by applicants.";

include 'header.inc';
include 'nav.inc';
?>

<main id="main-content" class="container page-main">
    <section class="page-intro">
        <p class="section-tag">Dashboard</p>
        <h2>HR Management Dashboard</h2>
        <p>
            Welcome, <strong><?php echo h($_SESSION["manager_username"] ?? "manager"); ?></strong>.
            Use the options below to manage EOI applications.
            <a class="logout-link" href="logout.php">Log Out</a>
        </p>
    </section>

    <?php if ($notice !== ""): ?>
        <section class="message-box success-box"><?php echo h($notice); ?></section>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <section class="message-box error-box"><?php echo h($error); ?></section>
    <?php endif; ?>

    <section class="content-card">
        <header class="card-header">
            <div>
                <p class="card-tag">Query Options</p>
                <h2>Select an action</h2>
            </div>
        </header>

        <div class="manager-actions">
            <form class="apply-form action-panel" action="manage.php" method="get" novalidate>
                <input type="hidden" name="view" value="all">
                <h3>1. List All EOIs</h3>
                <div class="form-grid">
                    <div class="field-group">
                        <label for="sort-all">Sort Field</label>
                        <select id="sort-all" name="sort">
                            <?php foreach ($allowed_sort as $value => $label): ?>
                                <option value="<?php echo h($value); ?>"<?php echo $sort === $value ? " selected" : ""; ?>><?php echo h($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="order-all">Sort Order</label>
                        <select id="order-all" name="order">
                            <?php foreach ($allowed_order as $value => $label): ?>
                                <option value="<?php echo h($value); ?>"<?php echo $order === $value ? " selected" : ""; ?>><?php echo h($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="button-row">
                    <input type="submit" value="View All Applications">
                </div>
            </form>

            <form class="apply-form action-panel" action="manage.php" method="get" novalidate>
                <input type="hidden" name="view" value="job">
                <input type="hidden" name="sort" value="<?php echo h($sort); ?>">
                <input type="hidden" name="order" value="<?php echo h($order); ?>">
                <h3>2. List by Job Reference</h3>
                <div class="field-group">
                    <label for="job-reference-filter">Job Reference</label>
                    <input type="text" id="job-reference-filter" name="job_reference" value="<?php echo h($job_ref); ?>" placeholder="Example: FWD25">
                </div>
                <div class="button-row">
                    <input type="submit" value="Search by Job">
                </div>
            </form>

            <form class="apply-form action-panel" action="manage.php" method="get" novalidate>
                <input type="hidden" name="view" value="name">
                <input type="hidden" name="sort" value="<?php echo h($sort); ?>">
                <input type="hidden" name="order" value="<?php echo h($order); ?>">
                <h3>3. List by Applicant Name</h3>
                <div class="form-grid">
                    <div class="field-group">
                        <label for="first-name-filter">First Name</label>
                        <input type="text" id="first-name-filter" name="first_name" value="<?php echo h($first_name); ?>">
                    </div>
                    <div class="field-group">
                        <label for="last-name-filter">Last Name</label>
                        <input type="text" id="last-name-filter" name="last_name" value="<?php echo h($last_name); ?>">
                    </div>
                </div>
                <div class="button-row">
                    <input type="submit" value="Search by Name">
                </div>
            </form>

            <form class="apply-form action-panel danger-panel" action="manage.php" method="post" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo h(csrf_token()); ?>">
                <input type="hidden" name="manager_action" value="delete_by_job">
                <h3>4. Delete All EOIs by Job Reference</h3>
                <p class="field-note">This removes every EOI submitted for the selected job reference.</p>
                <div class="field-group">
                    <label for="delete-job-reference">Job Reference</label>
                    <input type="text" id="delete-job-reference" name="delete_job_reference" placeholder="Example: FWD25">
                </div>
                <div class="button-row">
                    <input class="danger-submit" type="submit" value="Delete All for Job">
                </div>
            </form>
        </div>
    </section>

    <section class="content-card">
        <header class="card-header">
            <div>
                <p class="card-tag">Results</p>
                <h2><?php echo h($result_title); ?></h2>
                <p class="card-summary"><?php echo h($result_summary); ?></p>
            </div>
        </header>

        <?php if (count($records) === 0): ?>
            <p>No EOI records matched this query.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="manage-table">
                    <thead>
                    <tr>
                        <th scope="col">EOI Number</th>
                        <th scope="col">Job Ref</th>
                        <th scope="col">Applicant</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Location</th>
                        <th scope="col">Skills</th>
                        <th scope="col">Status</th>
                        <th scope="col">Submitted</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo h($record["EOInumber"]); ?></td>
                            <td><?php echo h($record["job_reference"]); ?></td>
                            <td><?php echo h($record["first_name"] . " " . $record["last_name"]); ?></td>
                            <td>
                                <span><?php echo h($record["email"]); ?></span><br>
                                <span><?php echo h($record["phone_number"]); ?></span>
                            </td>
                            <td><?php echo h($record["state"] . " " . $record["postcode"]); ?></td>
                            <td>
                                <?php echo h($record["skills"]); ?>
                                <?php if (trim((string)$record["other_skills"]) !== ""): ?>
                                    <br><span class="field-note"><?php echo h($record["other_skills"]); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form class="inline-status-form" action="manage.php" method="post" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?php echo h(csrf_token()); ?>">
                                    <input type="hidden" name="manager_action" value="update_status">
                                    <input type="hidden" name="EOInumber" value="<?php echo h($record["EOInumber"]); ?>">
                                    <label class="visually-hidden" for="status-<?php echo h($record["EOInumber"]); ?>">Status for EOI <?php echo h($record["EOInumber"]); ?></label>
                                    <select id="status-<?php echo h($record["EOInumber"]); ?>" name="status">
                                        <?php foreach (status_options() as $status): ?>
                                            <option value="<?php echo h($status); ?>"<?php echo $record["status"] === $status ? " selected" : ""; ?>><?php echo h($status); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td><?php echo h($record["created_at"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="results-count"><strong>Total Results: <?php echo count($records); ?></strong></p>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.inc'; ?>
