<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db-conn.php";
include "functions.php";

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$sub_cat_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($sub_cat_id <= 0) {$_SESSION['error'] = "Invalid Sub-Category ID";
    header('Location: sub-categories.php');
    exit();
}

$sql = "SELECT * FROM `sub_categories` WHERE `cate_id` = ?";
$stmt = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt, "s", $sub_cat_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$subcategory = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$subcategory) {$_SESSION['error'] = "Sub-category not found";
    header('Location: sub-categories.php');
    exit();
}

$parent_categories = [];$parent_sql = "SELECT cate_id, categories FROM categories WHERE status = 1 ORDER BY categories";
$parent_result = mysqli_query($conn,$parent_sql);
while ($row = mysqli_fetch_assoc($parent_result)) {
    $parent_categories[] =$row;
}

$success_message = '';$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {$parent_id = mysqli_real_escape_string($conn,$_POST['parent_id']);
    $categories = mysqli_real_escape_string($conn, $_POST['categories']);$meta_title = mysqli_real_escape_string($conn,$_POST['meta_title']);
    $meta_desc = mysqli_real_escape_string($conn, $_POST['meta_desc']);$meta_key = mysqli_real_escape_string($conn,$_POST['meta_key']);
    $schema_markup = mysqli_real_escape_string($conn, trim($_POST['schema_markup'] ?? ''));$slug_url = mysqli_real_escape_string($conn,$_POST['slug_url']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);$updated_on = date('Y-m-d H:i:s');

    $imageName = $subcategory['sub_cat_img'];$uploadDir = 'uploads/sub-category/';
    
    if (isset($_FILES['sub_cat_img']['name']) && $_FILES['sub_cat_img']['error'] === UPLOAD_ERR_OK) {$fileTmpPath = $_FILES['sub_cat_img']['tmp_name'];$fileName = $_FILES['sub_cat_img']['name'];$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExtension,$allowedExtensions)) {
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $imageName = uniqid('subcat_') . '_' . time() . '.' . $fileExtension;
            if (move_uploaded_file($fileTmpPath, $uploadDir .$imageName)) {
                if (!empty($subcategory['sub_cat_img']) && file_exists($uploadDir .$subcategory['sub_cat_img'])) {
                    unlink($uploadDir .$subcategory['sub_cat_img']);
                }
            }
        }
    }

    // Update query including schema_markup
    $updateQuery = "UPDATE sub_categories SET 
        parent_id = ?, categories = ?, meta_title = ?, meta_desc = ?, meta_key = ?, schema_markup = ?, slug_url = ?, status = ?, sub_cat_img = ?, added_on = ?
        WHERE cate_id = ?";
    
    $stmt = mysqli_prepare($conn,$updateQuery);
    mysqli_stmt_bind_param($stmt, "sssssssssss", 
        $parent_id, $categories,$meta_title, $meta_desc,$meta_key, $schema_markup,$slug_url, $status,$imageName, $updated_on,$sub_cat_id
    );
    
    if (mysqli_stmt_execute($stmt)) {$success_message = "Sub-category updated successfully!";
        $subcategory['parent_id'] =$parent_id;
        $subcategory['categories'] =$categories;
        $subcategory['meta_title'] =$meta_title;
        $subcategory['meta_desc'] =$meta_desc;
        $subcategory['meta_key'] =$meta_key;
        $subcategory['schema_markup'] =$schema_markup;
        $subcategory['slug_url'] =$slug_url;
        $subcategory['status'] =$status;
        $subcategory['sub_cat_img'] =$imageName;
    } else {
        $error_message = "Error updating sub-category: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Sub-Category | Admin Dashboard</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <style>
        .form-card { background: white; border-radius: 10px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); padding: 30px; }
        .image-preview { width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; padding: 5px; }
        .form-section { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .section-title { color: #4361ee; font-weight: 600; margin-bottom: 20px; }
        .required:after { content: " *"; color: #dc3545; }
    </style>
</head>
<body class="crm_body_bg">
    <?php include "header.php"; ?>
    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid g-0"><div class="row"><div class="col-lg-12 p-0"><?php include "top_nav.php"; ?></div></div></div>
        <div class="main_content_iner">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <h2 class="mb-1 fw-bold">Edit Sub-Category</h2>
                                <a href="view-sub-categories.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Back</a>
                            </div>
                            <div class="white_card_body">
                                <?php if($success_message): ?><div class="alert alert-success"><?= $success_message ?></div><?php endif; ?>
                                <?php if($error_message): ?><div class="alert alert-danger"><?= $error_message ?></div><?php endif; ?>
                                <div class="form-card">
                                    <form action="" method="post" enctype="multipart/form-data">
                                        
                                        <div class="form-section">
                                            <h4 class="section-title">Basic Information</h4>
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <label class="form-label required">Parent Category</label>
                                                    <select name="parent_id" class="form-select" required>
                                                        <option value="">Select Parent Category</option>
                                                        <?php foreach($parent_categories as$parent): ?>
                                                            <option value="<?= htmlspecialchars($parent['cate_id']) ?>" <?= ($subcategory['parent_id'] ==$parent['cate_id']) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($parent['categories']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label required">Sub-Category Name</label>
                                                    <input type="text" name="categories" class="form-control" value="<?= htmlspecialchars($subcategory['categories']) ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label required">Slug URL</label>
                                                    <input type="text" name="slug_url" class="form-control" value="<?= htmlspecialchars($subcategory['slug_url']) ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label required">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="Active" <?= ($subcategory['status'] == 'Active') ? 'selected' : '' ?>>Active</option>
                                                        <option value="Inactive" <?= ($subcategory['status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-section">
                                            <h4 class="section-title">Image Upload</h4>
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <?php if(!empty($subcategory['sub_cat_img'])): ?>
                                                        <img src="uploads/sub-category/<?= htmlspecialchars($subcategory['sub_cat_img']) ?>" class="image-preview mb-2">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-8">
                                                    <label class="form-label">Replace Image</label>
                                                    <input type="file" name="sub_cat_img" class="form-control" accept="image/*">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-section">
                                            <h4 class="section-title">SEO Settings & Schema Markup</h4>
                                            <div class="row g-4">
                                                <div class="col-md-12">
                                                    <label class="form-label">Meta Title</label>
                                                    <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($subcategory['meta_title']) ?>">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">Meta Description</label>
                                                    <textarea name="meta_desc" class="form-control" rows="2"><?= htmlspecialchars($subcategory['meta_desc']) ?></textarea>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">Meta Keywords</label>
                                                    <input type="text" name="meta_key" class="form-control" value="<?= htmlspecialchars($subcategory['meta_key']) ?>">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label fw-bold">SEO Schema Markup (JSON-LD)</label>
                                                    <textarea name="schema_markup" class="form-control" rows="6" placeholder="Paste full <script type='application/ld+json'>...</script> here..."><?= htmlspecialchars($subcategory['schema_markup'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="view-sub-categories.php" class="btn btn-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Update Sub-Category</button>
                                        </div>
                                    </form>
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