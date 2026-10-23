<?php
session_start();
include "db-conn.php";

// ==========================================
// DELETE LOGIC (Jab Delete button click ho)
// ==========================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = $_GET['delete'];

    // Pehle purani image fetch karke folder se delete karo
    $img_query = $conn->prepare("SELECT img_path FROM services WHERE id = ?");
    $img_query->bind_param("i", $del_id);
    $img_query->execute();
    $result = $img_query->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (!empty($row['img_path'])) {
            $file_path = "assets/img/uploads/" . $row['img_path'];
            if (file_exists($file_path) && !is_dir($file_path)) {
                unlink($file_path); // Delete from folder
            }
        }
    }

    // Ab DB se record delete karo
    $del_stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $del_stmt->bind_param("i", $del_id);

    if ($del_stmt->execute()) {
        $_SESSION['success'] = "Service deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete service.";
    }
    header("Location: view-service.php");
    exit();
}

// ==========================================
// FETCH ALL SERVICES (Read)
// ==========================================
$sql = "SELECT * FROM services ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage Services | Admin</title>
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
            <div class="container-fluid p-0 sm_padding_15px">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">

                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="m-0">Manage Services</h2>
                                    </div>
                                    <div class="add_button ms-2">
                                        <a href="add-service.php" class="btn btn-success">
                                            <i class="fas fa-plus me-1"></i> Add New Service
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="white_card_body">

                                <!-- Session Alerts -->
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success font-weight-bold"><i class="fas fa-check-circle"></i> <?= $_SESSION['success'];
                                                                                                                            unset($_SESSION['success']); ?></div>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger font-weight-bold"><i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error'];
                                                                                                                                    unset($_SESSION['error']); ?></div>
                                <?php endif; ?>

                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="15%">Image</th>
                                                <th width="20%">Service Name</th>
                                                <th width="45%">Short Description</th>
                                                <th width="15%" class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result && $result->num_rows > 0): ?>
                                                <?php $i = 1;
                                                while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?= $i++; ?></td>
                                                        <td>
                                                            <?php if (!empty($row['img_path'])): ?>
                                                                <img src="assets/img/uploads/<?= htmlspecialchars($row['img_path']); ?>" alt="Service" style="width: 80px; height: 60px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                                            <?php else: ?>
                                                                <span class="text-muted">No Image</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="fw-bold text-primary"><?= htmlspecialchars($row['service_name']); ?></td>
                                                        <td>
                                                            <!-- Showing only first 80 characters of short description -->
                                                            <?= htmlspecialchars(mb_strlen($row['short_desc']) > 80 ? mb_substr($row['short_desc'], 0, 80) . '...' : $row['short_desc']); ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="add-service.php?edit=<?= $row['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="view-service.php?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service?');" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        <i class="fas fa-cogs fa-3x mb-3 text-light"></i><br>
                                                        No services added yet. Click 'Add New Service' to start.
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
        <?php include "footer.php"; ?>
    </section>
</body>

</html>