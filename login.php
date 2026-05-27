<?php
require_once __DIR__ . "/settings.php";
start_project_session();

$conn = db_connect();
ensure_user_table($conn);

if (!empty($_SESSION["manager_logged_in"])) {
    header("Location: manage.php");
    exit;
}

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_valid($_POST["csrf_token"] ?? "")) {
        $login_error = "Session token expired. Please try again.";
    } else {
        $username = clean_input($_POST["username"] ?? "");
        $password = (string)($_POST["password"] ?? "");

        $result = prepared_result($conn, "SELECT id, username, password_hash FROM `user` WHERE username = ?", "s", array($username));
        $manager = mysqli_fetch_assoc($result);

        if ($manager && password_verify($password, $manager["password_hash"])) {
            session_regenerate_id(true);
            $_SESSION["manager_logged_in"] = true;
            $_SESSION["manager_username"] = $manager["username"];
            header("Location: manage.php");
            exit;
        }

        $login_error = "Invalid username or password.";
    }
}

$page_title = "Manager Login | ShopSphere";
$page_heading = "Manager Login";
$page_description = "Enter your credentials to access the management dashboard.";

include 'header.inc';
include 'nav.inc';
?>

<main id="main-content" class="container page-main">
    <section class="page-intro">
        <p class="section-tag">Admin Area</p>
        <h2>Manager Login</h2>
    </section>

    <section class="content-card login-card">
        <header class="card-header">
            <div>
                <p class="card-tag">Sign In</p>
                <h2>Enter your credentials</h2>
            </div>
        </header>

        <?php if ($login_error !== ""): ?>
            <div class="message-box error-box">
                <strong>Error:</strong> <?php echo h($login_error); ?>
            </div>
        <?php endif; ?>

        <form class="apply-form" method="post" action="login.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo h(csrf_token()); ?>">
            <div class="form-grid single-column-form">
                <div class="field-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" autofocus>
                </div>

                <div class="field-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="button-row">
                    <input type="submit" value="Login">
                </div>
            </div>
        </form>

        <p class="field-note login-note">
            Demo credentials: username <code>admin</code> and password <code>admin</code>.
        </p>
    </section>
</main>

<?php include 'footer.inc'; ?>
