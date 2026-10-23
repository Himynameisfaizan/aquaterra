<?php
session_start();
include "db-conn.php";

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Delete Page Logic
if (isset($_GET['deleteId'])) {
    $delete_id = intval($_GET['deleteId']);
    $stmt = $conn->prepare("DELETE FROM custom_pages WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Page deleted successfully!";
    }
    $stmt->close();
    header("Location: view-pages.php");
    exit();
}

// Fetch all pages
$result = $conn->query("SELECT * FROM custom_pages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>View Custom Pages | Admin Panel</title>
    <?php include "links.php"; ?>
</head>
<body class="crm_body_bg">
    <?php include "header.php"; ?>
    
    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid g-0"><div class="row"><div class="col-lg-12 p-0"><?php include "top_nav.php"; ?></div></div></div>

        <div class="main_content_iner">
            <div class="container-fluid p-0 sm_padding_15px">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="m-0">Manage SEO Pages</h2>
                                    </div>
                                    <div class="add_button ms-2">
                                        <a href="add-page.php" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Add New Page
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body">
                                <?php if (isset($_SESSION['message'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?= $_SESSION['message']; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <?php unset($_SESSION['message']); ?>
                                <?php endif; ?>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="bg-dark text-white">
                                            <tr>
                                                <th>ID</th>
                                                <th>Page Title</th>
                                                <th>Live URL (Slug)</th>
                                                <th>Status</th>
                                                <th>Date Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($result && mysqli_num_rows($result) > 0): ?>
                                                <?php while ($page = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($page['id']); ?></td>
                                                        <td><strong><?= htmlspecialchars($page['page_title']); ?></strong></td>
                                                        <td>
                                                            <!-- Frontend Link -->
                                                            <a href="../page.php?slug=<?= htmlspecialchars($page['slug_url']); ?>" target="_blank" class="text-primary text-decoration-none">
                                                                /page.php?slug=<?= htmlspecialchars($page['slug_url']); ?> <i class="fa-solid fa-external-link-alt ms-1" style="font-size: 12px;"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?= $page['status'] == 1 ? 'bg-success' : 'bg-secondary'; ?>">
                                                                <?= $page['status'] == 1 ? 'Published' : 'Draft'; ?>
                                                            </span>
                                                        </td>
                                                        <td><?= date('M d, Y', strtotime($page['created_at'])); ?></td>
                                                        <td>
                                                            <a href="edit-page.php?id=<?= $page['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="?deleteId=<?= $page['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this page? This cannot be undone.');" title="Delete">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr><td colspan="6" class="text-center py-4 text-muted">No custom pages created yet.</td></tr>
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