<?php
session_start();
include "db-conn.php";

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch policy data
$sql = "SELECT * FROM policies WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$policy = mysqli_fetch_assoc($result);

if (!$policy) {
    $_SESSION['error'] = "Policy not found!";
    header("Location: view-all-policies.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = $_POST['content'];
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $policy_code = mysqli_real_escape_string($conn, $_POST['policy_code']);
    $version = mysqli_real_escape_string($conn, $_POST['version']);
    $effective_date = mysqli_real_escape_string($conn, $_POST['effective_date']);
    $policy_type = mysqli_real_escape_string($conn, $_POST['policy_type']);
    $meta_description = mysqli_real_escape_string($conn, $_POST['meta_description']);
    $meta_keywords = mysqli_real_escape_string($conn, $_POST['meta_keywords']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $last_updated_by = $_SESSION['user_id'] ?? 'admin';
    
    // Generate slug from title
    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));
    
    // Check if slug already exists (excluding current policy)
    $check_sql = "SELECT id FROM policies WHERE slug = '$slug' AND id != $id";
    $check_result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($check_result) > 0) {
        $slug = $slug . '-' . time();
    }
    
    // Update policy
    $sql = "UPDATE policies SET 
            title = ?, 
            content = ?, 
            category = ?, 
            policy_code = ?, 
            version = ?, 
            effective_date = ?, 
            policy_type = ?, 
            meta_description = ?, 
            meta_keywords = ?, 
            slug = ?, 
            is_active = ?, 
            last_updated_by = ?,
            updated_at = NOW()
            WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssisi", 
        $title, $content, $category, $policy_code, $version, $effective_date, 
        $policy_type, $meta_description, $meta_keywords, $slug, $is_active, $last_updated_by, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Policy updated successfully!";
        header("Location: edit-policy.php");
        exit();
    } else {
        $_SESSION['error'] = "Database error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Policy | Admin Panel</title>
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
            <div class="container-fluid p-0 sm_padding_15px">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="text-center">Edit Policy</h2>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="white_card_body">
                                <!-- Display error/success messages -->
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                                <?php endif; ?>
                                
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
                                <?php endif; ?>
                                
                                <div class="col-md-12 mb-4">
                                    <a href="view-all-policies.php" class="btn btn-danger">
                                        <i class="fas fa-list"></i> Back to All Policies
                                    </a>
                                </div>
                                
                                <div class="card-body">
                                    <form method="POST" class="p-4 shadow bg-white">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label class="form-label">Policy Title:*</label>
                                                    <input type="text" name="title" class="form-control" required 
                                                           value="<?= htmlspecialchars($policy['title']) ?>">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Policy Content:*</label>
                                                    <textarea name="content" class="form-control" rows="10" id="editor" required>
                                                        <?= htmlspecialchars($policy['content']) ?>
                                                    </textarea>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Meta Description:</label>
                                                    <textarea name="meta_description" class="form-control" rows="3"><?= htmlspecialchars($policy['meta_description']) ?></textarea>
                                                    <small class="text-muted">Brief description for SEO (150-160 characters recommended)</small>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Meta Keywords:</label>
                                                    <input type="text" name="meta_keywords" class="form-control" 
                                                           value="<?= htmlspecialchars($policy['meta_keywords']) ?>">
                                                    <small class="text-muted">Comma separated keywords for SEO</small>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Category:*</label>
                                                    <select name="category" class="form-control select2" required>
                                                        <option value="">Select Category</option>
                                                        <option value="Privacy" <?= ($policy['category'] == 'Privacy') ? 'selected' : '' ?>>Privacy Policy</option>
                                                        <option value="Terms" <?= ($policy['category'] == 'Terms') ? 'selected' : '' ?>>Terms of Service</option>
                                                        <option value="Cookie" <?= ($policy['category'] == 'Cookie') ? 'selected' : '' ?>>Cookie Policy</option>
                                                        <option value="Refund" <?= ($policy['category'] == 'Refund') ? 'selected' : '' ?>>Refund Policy</option>
                                                        <option value="Shipping" <?= ($policy['category'] == 'Shipping') ? 'selected' : '' ?>>Shipping Policy</option>
                                                        <option value="Return" <?= ($policy['category'] == 'Return') ? 'selected' : '' ?>>Return Policy</option>
                                                        <option value="General" <?= ($policy['category'] == 'General') ? 'selected' : '' ?>>General Policy</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Policy Code:</label>
                                                    <input type="text" name="policy_code" class="form-control"
                                                           value="<?= htmlspecialchars($policy['policy_code']) ?>">
                                                    <small class="text-muted">Internal policy code (e.g., POL-001)</small>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Version:</label>
                                                    <input type="text" name="version" class="form-control"
                                                           value="<?= htmlspecialchars($policy['version']) ?>">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Effective Date:*</label>
                                                    <input type="text" name="effective_date" class="form-control datepicker" required
                                                           value="<?= date('Y-m-d', strtotime($policy['effective_date'])) ?>">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Policy Type:</label>
                                                    <select name="policy_type" class="form-control select2">
                                                        <option value="General" <?= ($policy['policy_type'] == 'General') ? 'selected' : '' ?>>General</option>
                                                        <option value="Legal" <?= ($policy['policy_type'] == 'Legal') ? 'selected' : '' ?>>Legal</option>
                                                        <option value="Internal" <?= ($policy['policy_type'] == 'Internal') ? 'selected' : '' ?>>Internal</option>
                                                        <option value="Public" <?= ($policy['policy_type'] == 'Public') ? 'selected' : '' ?>>Public</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                                           <?= $policy['is_active'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="is_active">Active Policy</label>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <button type="submit" class="btn btn-success btn-block">
                                                        <i class="fas fa-save"></i> Update Policy
                                                    </button>
                                                </div>
                                            </div>
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
        
       
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    
    <script>
        CKEDITOR.replace('content');

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                minimumResultsForSearch: Infinity
            });
            
            // Initialize Datepicker
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
            
           
        });
    </script>
    </section>
</body>
</html>
