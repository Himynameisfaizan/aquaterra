<?php
session_start();
include "db-conn.php";
include "functions.php";

// Check if user is admin (uncomment when ready)
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
//     header("Location: login.php");
//     exit();
// }

// Handle delete action
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);

    $sql = "DELETE FROM policies WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Policy deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting policy: " . mysqli_error($conn);
    }

    header("Location: view-all-policies.php");
    exit();
}

// Handle toggle active status
if (isset($_GET['toggle_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['toggle_id']);

    // Get current status
    $check_sql = "SELECT is_active FROM policies WHERE id = $id";
    $result = mysqli_query($conn, $check_sql);
    $row = mysqli_fetch_assoc($result);
    $new_status = $row['is_active'] ? 0 : 1;

    $update_sql = "UPDATE policies SET is_active = $new_status WHERE id = $id";
    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['success'] = "Policy status updated!";
    } else {
        $_SESSION['error'] = "Error updating status: " . mysqli_error($conn);
    }

    header("Location: view-all-policies.php");
    exit();
}

// Fetch all policies
$sql = "SELECT * FROM policies ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Policy Management | Admin Panel</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">

    <?php include "links.php"; ?>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <?php include "top_nav.php"; ?>
                </div>
            </div>
        </div>

        <div class="main_content_iner">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="card-header bg-white border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="mb-0 fw-bold">Policy Management</h2>
                                        <p class="text-muted mb-0 small">Manage your company policies and procedures</p>
                                    </div>
                                    <div>
                                        <a href="add-policy.php" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Add New Policy
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="white_card_body">
                                <!-- Display error/success messages -->
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <?= htmlspecialchars($_SESSION['error']);
                                        unset($_SESSION['error']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <?= htmlspecialchars($_SESSION['success']);
                                        unset($_SESSION['success']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <div class="QA_section">
                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Policy Code</th>
                                                    <th scope="col">Title</th>
                                                    <th scope="col">Category</th>
                                                    <th scope="col">Version</th>
                                                    <th scope="col">Effective Date</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Added On</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (mysqli_num_rows($result) > 0): ?>
                                                    <?php
                                                    $count = 1;
                                                    while ($row = mysqli_fetch_assoc($result)):
                                                        ?>
                                                        <tr>
                                                            <td><?= $count++ ?></td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-secondary"><?= htmlspecialchars($row['policy_code']) ?></span>
                                                            </td>
                                                            <td><?= htmlspecialchars($row['title']) ?></td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-info"><?= htmlspecialchars($row['category']) ?></span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-secondary">v<?= htmlspecialchars($row['version']) ?></span>
                                                            </td>
                                                            <td><?= date('d M, Y', strtotime($row['effective_date'])) ?></td>
                                                            <td>
                                                                <?php if ($row['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= date('d M, Y', strtotime($row['created_at'])) ?></td>
                                                            <td>
                                                                <div class="d-flex gap-2">
                                                                    <a href="edit-policy.php?id=<?= $row['id'] ?>"
                                                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                                        title="Edit Policy">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="view-all-policies.php?toggle_id=<?= $row['id'] ?>"
                                                                        class="btn btn-sm btn-warning"
                                                                        title="<?= $row['is_active'] ? 'Deactivate' : 'Activate' ?>"
                                                                        onclick="return confirm('Are you sure you want to <?= $row['is_active'] ? 'deactivate' : 'activate' ?> this policy?')">
                                                                        <i class="fas fa-power-off"></i>
                                                                    </a>
                                                                    <a href="view-all-policies.php?delete_id=<?= $row['id'] ?>"
                                                                        class="btn btn-sm btn-danger" title="Delete Policy"
                                                                        onclick="return confirm('Are you sure you want to delete this policy? This action cannot be undone.')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </a>
                                                                    <a href="view-policy.php?slug=<?= $row['slug'] ?>"
                                                                        class="btn btn-sm btn-info" title="View Policy"
                                                                        target="_blank">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center py-4">
                                                            <div class="text-center">
                                                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                                                <h5>No Policies Found</h5>
                                                                <p class="text-muted mb-3">Get started by creating your
                                                                    first company policy</p>
                                                                <a href="add-policy.php" class="btn btn-primary">
                                                                    <i class="fas fa-plus me-2"></i>Add New Policy
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "footer.php"; ?>

        <script>
            $(document).ready(function () {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    </section>
</body>

</html>