<?php
session_start();
include "db-conn.php";

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$page_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = "";

// Fetch existing page data
$query = mysqli_query($conn, "SELECT * FROM custom_pages WHERE id = '$page_id'");
$pageData = mysqli_fetch_assoc($query);

if (!$pageData) {
    $_SESSION['message'] = "Page not found!";
    header("Location: view-pages.php");
    exit();
}

// Update Page Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_page'])) {
    $page_title = mysqli_real_escape_string($conn, $_POST['page_title']);
    $breadcrumb_title = mysqli_real_escape_string($conn, $_POST['breadcrumb_title']);
    $slug_url = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug_url'])));
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title']);
    $meta_desc = mysqli_real_escape_string($conn, $_POST['meta_desc']);
    $meta_key = mysqli_real_escape_string($conn, $_POST['meta_key']);
    $schema_markup = mysqli_real_escape_string($conn, trim($_POST['schema_markup']));
    $status = $_POST['status'];

    // Check if slug exists for another page
    $checkSlug = mysqli_query($conn, "SELECT id FROM custom_pages WHERE slug_url = '$slug_url' AND id != '$page_id'");
    if(mysqli_num_rows($checkSlug) > 0) {
        $message = "<div class='alert alert-danger'>Error: This Slug URL is already used by another page.</div>";
    } else {
        $sql = "UPDATE custom_pages SET 
                page_title = '$page_title', 
                breadcrumb_title = '$breadcrumb_title', 
                slug_url = '$slug_url', 
                content = '$content', 
                meta_title = '$meta_title', 
                meta_desc = '$meta_desc', 
                meta_key = '$meta_key', 
                schema_markup = '$schema_markup', 
                status = '$status' 
                WHERE id = '$page_id'";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Page updated successfully!";
            header("Location: view-pages.php");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Custom Page | Admin Panel</title>
    <?php include "links.php"; ?>
    <script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
</head>
<body class="crm_body_bg">
    <?php include "header.php"; ?>
    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid g-0"><div class="row"><div class="col-lg-12 p-0"><?php include "top_nav.php"; ?></div></div></div>
        
        <div class="main_content_iner">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <h2 class="mb-1 fw-bold">Edit SEO Page</h2>
                                <a href="view-pages.php" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to Pages</a>
                            </div>
                            <div class="white_card_body p-4">
                                <?= $message; ?>
                                <form action="" method="post">
                                    <div class="row g-4">
                                        <!-- Basic Info -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Page Title (H1)</label>
                                            <input type="text" name="page_title" class="form-control" required value="<?= htmlspecialchars($pageData['page_title']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Breadcrumb Title</label>
                                            <input type="text" name="breadcrumb_title" class="form-control" required value="<?= htmlspecialchars($pageData['breadcrumb_title']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Slug URL</label>
                                            <input type="text" name="slug_url" class="form-control" required value="<?= htmlspecialchars($pageData['slug_url']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="1" <?= $pageData['status'] == 1 ? 'selected' : ''; ?>>Publish</option>
                                                <option value="0" <?= $pageData['status'] == 0 ? 'selected' : ''; ?>>Draft</option>
                                            </select>
                                        </div>

                                        <!-- SEO Settings -->
                                        <div class="col-md-12 mt-4"><h5 class="border-bottom pb-2 text-primary">SEO Configuration</h5></div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Meta Title</label>
                                            <input type="text" name="meta_title" class="form-control" required value="<?= htmlspecialchars($pageData['meta_title']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Meta Keywords</label>
                                            <input type="text" name="meta_key" class="form-control" value="<?= htmlspecialchars($pageData['meta_key']); ?>">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Meta Description</label>
                                            <textarea name="meta_desc" class="form-control" rows="2" required><?= htmlspecialchars($pageData['meta_desc']); ?></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Schema Markup (JSON-LD)</label>
                                            <textarea name="schema_markup" class="form-control" rows="4"><?= htmlspecialchars($pageData['schema_markup']); ?></textarea>
                                        </div>

                                        <!-- Page Content -->
                                        <div class="col-md-12 mt-4"><h5 class="border-bottom pb-2 text-primary">Page Content</h5></div>
                                        <div class="col-md-12">
                                            <textarea name="content" id="page_content" class="form-control" rows="10"><?= htmlspecialchars($pageData['content']); ?></textarea>
                                            <script>CKEDITOR.replace('page_content');</script>
                                        </div>

                                        <div class="col-12 mt-4 text-end">
                                            <button type="submit" name="update_page" class="btn btn-primary px-5 py-2 fw-bold"><i class="fa-solid fa-save me-1"></i> Update Page</button>
                                        </div>
                                    </div>
                                </form>
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