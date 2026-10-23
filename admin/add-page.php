<?php
session_start();
include "db-conn.php";

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_page'])) {
    $page_title = mysqli_real_escape_string($conn, $_POST['page_title']);
    $breadcrumb_title = mysqli_real_escape_string($conn, $_POST['breadcrumb_title']);
    
    // Slug generation (Auto format to lowercase & hyphens)
    $slug_url = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug_url'])));
    
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title']);
    $meta_desc = mysqli_real_escape_string($conn, $_POST['meta_desc']);
    $meta_key = mysqli_real_escape_string($conn, $_POST['meta_key']);
    $schema_markup = mysqli_real_escape_string($conn, trim($_POST['schema_markup']));
    $status = $_POST['status'];

    $checkSlug = mysqli_query($conn, "SELECT id FROM custom_pages WHERE slug_url = '$slug_url'");
    if(mysqli_num_rows($checkSlug) > 0) {
        $message = "<div class='alert alert-danger'>Error: This Slug URL already exists. Please choose a unique slug.</div>";
    } else {
        $sql = "INSERT INTO custom_pages (page_title, breadcrumb_title, slug_url, content, meta_title, meta_desc, meta_key, schema_markup, status) 
                VALUES ('$page_title', '$breadcrumb_title', '$slug_url', '$content', '$meta_title', '$meta_desc', '$meta_key', '$schema_markup', '$status')";
        
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-success'>Page Created Successfully! View it at: site.com/page.php?slug=$slug_url</div>";
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
    <title>Add Custom Page | Admin Panel</title>
    <?php include "links.php"; ?>
    <!-- CKEditor included for rich text editing -->
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
                            <div class="card-header bg-white border-0 py-3">
                                <h2 class="mb-1 fw-bold">Create Dynamic SEO Page</h2>
                            </div>
                            <div class="white_card_body p-4">
                                <?= $message; ?>
                                <form action="" method="post">
                                    <div class="row g-4">
                                        <!-- Basic Info -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Page Title (H1)</label>
                                            <input type="text" name="page_title" class="form-control" required placeholder="e.g. Bulk Spices Exporter">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Breadcrumb Title</label>
                                            <input type="text" name="breadcrumb_title" class="form-control" required placeholder="e.g. Bulk Spices">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Slug URL</label>
                                            <input type="text" name="slug_url" class="form-control" required placeholder="e.g. bulk-spices-exporter">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="1">Publish</option>
                                                <option value="0">Draft</option>
                                            </select>
                                        </div>

                                        <!-- SEO Settings -->
                                        <div class="col-md-12 mt-4"><h5 class="border-bottom pb-2 text-primary">SEO Configuration</h5></div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Meta Title</label>
                                            <input type="text" name="meta_title" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Meta Keywords</label>
                                            <input type="text" name="meta_key" class="form-control">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Meta Description</label>
                                            <textarea name="meta_desc" class="form-control" rows="2" required></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Schema Markup (JSON-LD)</label>
                                            <textarea name="schema_markup" class="form-control" rows="4" placeholder="Paste <script type='application/ld+json'>...</script> here"></textarea>
                                        </div>

                                        <!-- Page Content -->
                                        <div class="col-md-12 mt-4"><h5 class="border-bottom pb-2 text-primary">Page Content</h5></div>
                                        <div class="col-md-12">
                                            <textarea name="content" id="page_content" class="form-control" rows="10"></textarea>
                                            <script>CKEDITOR.replace('page_content');</script>
                                        </div>

                                        <div class="col-12 mt-4 text-end">
                                            <button type="submit" name="create_page" class="btn btn-primary px-5 py-2 fw-bold">Create Page</button>
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