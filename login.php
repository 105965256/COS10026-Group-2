<?php

session_start();

$login_error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Simple hardcoded check for now (replace with database later)
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $username;
        header('Location: manage.php');
        exit();
    } else {
        $login_error = 'Invalid username or password.';
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

    <section class="content-card" style="max-width: 500px; margin: 0 auto;">
        <header class="card-header">
            <div>
                <p class="card-tag">Sign In</p>
                <h2>Enter your credentials</h2>
            </div>
        </header>

        <?php if (!empty($login_error)): ?>
            <div style="padding: 1rem; margin-bottom: 1.5rem; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 0.8rem; color: #991b1b;">
                <strong>Error:</strong> <?php echo htmlspecialchars($login_error); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="form-grid" style="grid-template-columns: 1fr;">
                <div class="field-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>

                <div class="field-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div style="text-align: center;">
                    <input type="submit" value="Login">
                </div>
            </div>
        </form>

        <p style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #d9e4ef; text-align: center; color: #52667a; font-size: 0.9rem;">
            <strong>Demo Credentials:</strong><br>
            Username: <code>admin</code><br>
            Password: <code>admin</code>
        </p>
    </section>
</main>

<?php include 'footer.inc'; ?>
