<?php

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'settings.php';

$page_title = "Manager Dashboard | ShopSphere";
$page_heading = "HR Manager Dashboard";
$page_description = "Manage applications and applicant information.";

$filter_type = '';
$filter_value = '';
$sort_field = 'created_at';
$sort_order = 'DESC';
$results = array();
$query_executed = false;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query_executed = true;

    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Get sort preferences
        if (isset($_POST['sort_field'])) {
            $sort_field = $_POST['sort_field'];
        }
        if (isset($_POST['sort_order'])) {
            $sort_order = $_POST['sort_order'];
        }

        if ($action === 'list_all') {
            // List all EOIs
            $sql = "SELECT * FROM eoi ORDER BY $sort_field $sort_order";
            $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $results[] = $row;
                }
            }
            $filter_type = 'All EOIs';
        }

        elseif ($action === 'list_by_job') {
            // List by job reference
            if (isset($_POST['job_reference']) && !empty($_POST['job_reference'])) {
                $job_ref = $conn->real_escape_string($_POST['job_reference']);
                $sql = "SELECT * FROM eoi WHERE job_reference = '$job_ref' ORDER BY $sort_field $sort_order";
                $result = $conn->query($sql);
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $results[] = $row;
                    }
                }
                $filter_type = 'Job Reference';
                $filter_value = $job_ref;
            }
        }

        elseif ($action === 'list_by_name') {
            // List by applicant name
            if (isset($_POST['search_name']) && !empty($_POST['search_name'])) {
                $search_name = '%' . $conn->real_escape_string($_POST['search_name']) . '%';
                $sql = "SELECT * FROM eoi WHERE first_name LIKE '$search_name' OR last_name LIKE '$search_name' ORDER BY $sort_field $sort_order";
                $result = $conn->query($sql);
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $results[] = $row;
                    }
                }
                $filter_type = 'Applicant Name';
                $filter_value = $_POST['search_name'];
            }
        }

        elseif ($action === 'delete_by_job') {
            // Delete all EOIs by job reference
            if (isset($_POST['job_reference_delete']) && !empty($_POST['job_reference_delete'])) {
                $job_ref = $conn->real_escape_string($_POST['job_reference_delete']);
                $sql = "DELETE FROM eoi WHERE job_reference = '$job_ref'";
                if ($conn->query($sql)) {
                    $filter_type = "Deleted EOIs for job reference: $job_ref";
                    $filter_value = $conn->affected_rows . " records deleted";
                }
            }
        }

        elseif ($action === 'change_status') {
            // Change EOI status
            if (isset($_POST['eoi_id']) && isset($_POST['new_status'])) {
                $eoi_id = (int)$_POST['eoi_id'];
                $new_status = $conn->real_escape_string($_POST['new_status']);
                $sql = "UPDATE eoi SET status = '$new_status' WHERE eoi_id = $eoi_id";
                if ($conn->query($sql)) {
                    $filter_type = "Status Updated";
                    $filter_value = "EOI #$eoi_id updated to: $new_status";
                    // Re-fetch all data
                    $sql = "SELECT * FROM eoi ORDER BY $sort_field $sort_order";
                    $result = $conn->query($sql);
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $results[] = $row;
                        }
                    }
                }
            }
        }
    }
}

include 'header.inc';
include 'nav.inc';
?>

<main id="main-content" class="container page-main">
    <section class="page-intro">
        <p class="section-tag">Dashboard</p>
        <h2>HR Management Dashboard</h2>
        <p>
            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>. 
            Use the options below to manage EOI applications.
            <a href="logout.php" style="float: right;">Log Out</a>
        </p>
    </section>

    <section class="content-card">
        <header class="card-header">
            <div>
                <p class="card-tag">Query Options</p>
                <h2>Select an action</h2>
            </div>
        </header>

        <form method="post" action="manage.php">
            <div style="display: grid; gap: 1.5rem;">
                
                <!-- Sort Options -->
                <div style="padding: 1rem; background: #f8fbff; border: 1px solid #d9e4ef; border-radius: 0.8rem;">
                    <h3 style="margin-top: 0;">Sort Results By</h3>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <div class="field-group" style="flex: 1; min-width: 200px;">
                            <label for="sort_field">Sort Field</label>
                            <select id="sort_field" name="sort_field">
                                <option value="created_at">Date Applied</option>
                                <option value="job_reference">Job Reference</option>
                                <option value="first_name">First Name</option>
                                <option value="last_name">Last Name</option>
                                <option value="status">Status</option>
                            </select>
                        </div>
                        <div class="field-group" style="flex: 1; min-width: 200px;">
                            <label for="sort_order">Sort Order</label>
                            <select id="sort_order" name="sort_order">
                                <option value="DESC">Newest First</option>
                                <option value="ASC">Oldest First</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action 1: List All -->
                <div style="padding: 1rem; background: #f8fbff; border: 1px solid #d9e4ef; border-radius: 0.8rem;">
                    <h3 style="margin-top: 0;">1. List All EOIs</h3>
                    <button type="submit" name="action" value="list_all" style="padding: 0.7rem 1rem; background: #0f4c97; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: bold;">View All Applications</button>
                </div>

                <!-- Action 2: Filter by Job Reference -->
                <div style="padding: 1rem; background: #f8fbff; border: 1px solid #d9e4ef; border-radius: 0.8rem;">
                    <h3 style="margin-top: 0;">2. Filter by Job Reference</h3>
                    <div class="field-group">
                        <input type="text" name="job_reference" placeholder="Enter job reference (e.g., FWD25)" maxlength="5">
                    </div>
                    <button type="submit" name="action" value="list_by_job" style="padding: 0.7rem 1rem; background: #0f4c97; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: bold;">Search by Job</button>
                </div>

                <!-- Action 3: Filter by Name -->
                <div style="padding: 1rem; background: #f8fbff; border: 1px solid #d9e4ef; border-radius: 0.8rem;">
                    <h3 style="margin-top: 0;">3. Filter by Applicant Name</h3>
                    <div class="field-group">
                        <input type="text" name="search_name" placeholder="Enter first or last name">
                    </div>
                    <button type="submit" name="action" value="list_by_name" style="padding: 0.7rem 1rem; background: #0f4c97; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: bold;">Search by Name</button>
                </div>

                <!-- Action 4: Delete by Job Reference -->
                <div style="padding: 1rem; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 0.8rem;">
                    <h3 style="margin-top: 0; color: #7f1d1d;">4. Delete All EOIs by Job Reference</h3>
                    <p style="color: #7f1d1d; font-size: 0.9rem;">⚠️ Warning: This action cannot be undone.</p>
                    <div class="field-group">
                        <input type="text" name="job_reference_delete" placeholder="Enter job reference to delete" maxlength="5">
                    </div>
                    <button type="submit" name="action" value="delete_by_job" onclick="return confirm('Are you sure? This will delete ALL applications for this job reference.');" style="padding: 0.7rem 1rem; background: #dc2626; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: bold;">Delete All for Job</button>
                </div>

            </div>
        </form>
    </section>

    <!-- Results Section -->
    <?php if ($query_executed): ?>
        <section class="content-card">
            <header class="card-header">
                <div>
                    <p class="card-tag">Results</p>
                    <h2><?php echo htmlspecialchars($filter_type); ?></h2>
                    <?php if (!empty($filter_value)): ?>
                        <p class="card-summary">
                            <?php echo htmlspecialchars($filter_value); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </header>

            <?php if (count($results) > 0): ?>
                <div class="table-wrap">
                    <table class="manage-table" style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                        <thead>
                            <tr style="background: #123d7a; color: white;">
                                <th style="padding: 0.9rem; text-align: left; border: 1px solid #d7e5f2;">EOI Number</th>
                                <th style="padding: 0.9rem; text-align: left; border: 1px solid #d7e5f2;">Name</th>
                                <th style="padding: 0.9rem; text-align: left; border: 1px solid #d7e5f2;">Job Ref</th>
                                <th style="padding: 0.9rem; text-align: left; border: 1px solid #d7e5f2;">Email</th>
                                <th style="padding: 0.9rem; text-align: left; border: 1px solid #d7e5f2;">Status</th>
                                <th style="padding: 0.9rem; text-align: left; border: 1px solid #d7e5f2;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $index => $eoi): ?>
                                <tr style="<?php echo ($index % 2 === 0) ? 'background: #f8fbff;' : 'background: white;'; ?>">
                                    <td style="padding: 0.9rem; border: 1px solid #d7e5f2;"><?php echo htmlspecialchars($eoi['eoi_number']); ?></td>
                                    <td style="padding: 0.9rem; border: 1px solid #d7e5f2;"><?php echo htmlspecialchars($eoi['first_name'] . ' ' . $eoi['last_name']); ?></td>
                                    <td style="padding: 0.9rem; border: 1px solid #d7e5f2;"><?php echo htmlspecialchars($eoi['job_reference']); ?></td>
                                    <td style="padding: 0.9rem; border: 1px solid #d7e5f2;"><?php echo htmlspecialchars($eoi['email']); ?></td>
                                    <td style="padding: 0.9rem; border: 1px solid #d7e5f2;">
                                        <form method="post" action="manage.php" style="display: inline;">
                                            <input type="hidden" name="action" value="change_status">
                                            <input type="hidden" name="eoi_id" value="<?php echo $eoi['eoi_id']; ?>">
                                            <select name="new_status" onchange="this.form.submit();" style="padding: 0.4rem;">
                                                <option value="New" <?php echo ($eoi['status'] === 'New') ? 'selected' : ''; ?>>New</option>
                                                <option value="Current" <?php echo ($eoi['status'] === 'Current') ? 'selected' : ''; ?>>Current</option>
                                                <option value="Final" <?php echo ($eoi['status'] === 'Final') ? 'selected' : ''; ?>>Final</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td style="padding: 0.9rem; border: 1px solid #d7e5f2;">
                                        <a href="#" style="color: #0f4c97; text-decoration: none;">View Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p style="margin-top: 1rem; color: #52667a;">
                    <strong>Total Results: <?php echo count($results); ?></strong>
                </p>
            <?php else: ?>
                <p style="color: #52667a; padding: 1rem;">No records found matching your criteria.</p>
            <?php endif; ?>
        </section>
    <?php endif; ?>

</main>

<?php include 'footer.inc'; ?>