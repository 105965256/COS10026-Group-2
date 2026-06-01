<?php
// AI declaration: AI assistance was used to help debug this file.
require_once __DIR__ . "/settings.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["job-reference"])) {
    header("Location: apply.php");
    exit;
}

$conn = db_connect();
ensure_eoi_table($conn);
ensure_jobs_table($conn);

$data = array(
    "job_reference" => strtoupper(clean_input($_POST["job-reference"] ?? "")),
    "first_name" => clean_input($_POST["first-name"] ?? ""),
    "last_name" => clean_input($_POST["last-name"] ?? ""),
    "date_of_birth" => clean_input($_POST["date-of-birth"] ?? ""),
    "gender" => clean_input($_POST["gender"] ?? ""),
    "street_address" => clean_input($_POST["street-address"] ?? ""),
    "suburb_town" => clean_input($_POST["suburb-town"] ?? ""),
    "state" => strtoupper(clean_input($_POST["state"] ?? "")),
    "postcode" => clean_input($_POST["postcode"] ?? ""),
    "email" => clean_input($_POST["email"] ?? ""),
    "phone_number" => clean_input($_POST["phone-number"] ?? ""),
    "other_skills" => clean_input($_POST["other-skills"] ?? "")
);

$selectedSkills = $_POST["skills"] ?? array();
if (!is_array($selectedSkills)) {
    $selectedSkills = array();
}

$errors = array();
$validSkills = skill_labels();
$validGenders = gender_labels();
$validStates = array("VIC", "NSW", "QLD", "NT", "WA", "SA", "TAS", "ACT");

if (!preg_match("/^[A-Za-z0-9]{5}$/", $data["job_reference"])) {
    $errors[] = "Job reference must contain exactly five letters or numbers.";
} else {
    $jobCheck = prepared_result($conn, "SELECT job_reference FROM jobs WHERE job_reference = ?", "s", array($data["job_reference"]));
    if (!mysqli_fetch_assoc($jobCheck)) {
        $errors[] = "Job reference must match an advertised job in the database.";
    }
}

if (!preg_match("/^[A-Za-z '-]{1,20}$/", $data["first_name"])) {
    $errors[] = "First name is required and must use letters, spaces, apostrophes, or hyphens only, up to 20 characters.";
}

if (!preg_match("/^[A-Za-z '-]{1,20}$/", $data["last_name"])) {
    $errors[] = "Last name is required and must use letters, spaces, apostrophes, or hyphens only, up to 20 characters.";
}

$dobDate = null;
if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/(19|20)[0-9]{2}$/", $data["date_of_birth"])) {
    $errors[] = "Date of birth must use the dd/mm/yyyy format.";
} else {
    $dob = DateTime::createFromFormat("d/m/Y", $data["date_of_birth"]);
    $dobErrors = DateTime::getLastErrors();
    $hasDobErrors = is_array($dobErrors) && ($dobErrors["warning_count"] > 0 || $dobErrors["error_count"] > 0);
    if (!$dob || $hasDobErrors || $dob->format("d/m/Y") !== $data["date_of_birth"]) {
        $errors[] = "Date of birth must be a real calendar date.";
    } else {
        $dobDate = $dob->format("Y-m-d");
    }
}

if (!array_key_exists($data["gender"], $validGenders)) {
    $errors[] = "Please select a valid gender option.";
}

if ($data["street_address"] === "" || strlen($data["street_address"]) > 40) {
    $errors[] = "Street address is required and must be 40 characters or fewer.";
}

if ($data["suburb_town"] === "" || strlen($data["suburb_town"]) > 40) {
    $errors[] = "Suburb or town is required and must be 40 characters or fewer.";
}

if (!in_array($data["state"], $validStates, true)) {
    $errors[] = "Please select a valid Australian state or territory.";
}

if (!preg_match("/^[0-9]{4}$/", $data["postcode"])) {
    $errors[] = "Postcode must contain exactly four digits.";
}

if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL) || strlen($data["email"]) > 100) {
    $errors[] = "Please enter a valid email address.";
}

if (!preg_match("/^[0-9]{8,12}$/", $data["phone_number"])) {
    $errors[] = "Phone number must contain 8 to 12 digits.";
}

$skillLabels = array();
foreach ($selectedSkills as $skill) {
    $skill = clean_input($skill);
    if (array_key_exists($skill, $validSkills)) {
        $skillLabels[] = $validSkills[$skill];
    }
}

if (count($skillLabels) === 0) {
    $errors[] = "Please select at least one skill.";
}

if (strlen($data["other_skills"]) > 500) {
    $errors[] = "Other skills must be 500 characters or fewer.";
}

$page_title = count($errors) > 0 ? "Application Errors | ShopSphere" : "Application Confirmation | ShopSphere";
$page_heading = count($errors) > 0 ? "Application needs attention" : "Application received";
$page_description = count($errors) > 0 ? "Please review the server-side validation messages below." : "Your Expression of Interest has been saved securely in the database.";
include __DIR__ . "/header.inc";
include __DIR__ . "/nav.inc";
?>

<main id="main-content" class="container page-main">
    <?php if (count($errors) > 0) { ?>
        <section class="content-card message-box error-box">
            <p class="card-tag">Validation Errors</p>
            <h2>Please fix these details</h2>
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?php echo h($error); ?></li>
                <?php } ?>
            </ul>
            <p><a class="button-link" href="apply.php">Return to the application form</a></p>
        </section>
    <?php } else {
        $skills = implode(", ", $skillLabels);
        $sql = "INSERT INTO eoi (job_reference, first_name, last_name, date_of_birth, gender, street_address, suburb_town, state, postcode, email, phone_number, skills, other_skills) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = array(
            $data["job_reference"],
            $data["first_name"],
            $data["last_name"],
            $dobDate,
            $data["gender"],
            $data["street_address"],
            $data["suburb_town"],
            $data["state"],
            $data["postcode"],
            $data["email"],
            $data["phone_number"],
            $skills,
            $data["other_skills"]
        );
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            db_fail(mysqli_error($conn));
        }
        bind_params($stmt, "sssssssssssss", $params);
        if (!mysqli_stmt_execute($stmt)) {
            db_fail(mysqli_stmt_error($stmt));
        }
        $eoiNumber = mysqli_insert_id($conn);
    ?>
        <section class="content-card message-box success-box">
            <p class="card-tag">Confirmation</p>
            <h2>Thank you, <?php echo h($data["first_name"]); ?>.</h2>
            <p>Your Expression of Interest has been recorded.</p>
            <p class="confirmation-number"><strong>EOI number:</strong> <?php echo h($eoiNumber); ?></p>
            <p><strong>Job reference:</strong> <?php echo h($data["job_reference"]); ?></p>
            <p><strong>Status:</strong> New</p>
            <p><a class="button-link" href="jobs.php">Back to jobs</a></p>
        </section>
    <?php } ?>
</main>

<?php include __DIR__ . "/footer.inc"; ?>
