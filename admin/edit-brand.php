<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Database central core mapping connection pipeline
include "db-conn.php";

$msg = "";
$msg_class = "";

// 1. VERIFY AND FETCH CURRENT RECORD SPECIFICATIONS FROM URL PARAMETER
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header("Location: brands.php");
    exit();
}

$brand_id = mysqli_real_escape_string($conn, trim($_GET['id']));

$fetch_query = "SELECT * FROM `brands` WHERE `id` = '$brand_id' LIMIT 1";
$fetch_res   = mysqli_query($conn, $fetch_query);

if (!$fetch_res || mysqli_num_rows($fetch_res) == 0) {
    // Redirect back to main grid if targeting an unlogged ID matrix
    header("Location: brands.php");
    exit();
}

$brand_data   = mysqli_fetch_assoc($fetch_res);
$current_name = $brand_data['brand_name'];
$current_logo = $brand_data['logo_path'];


// 2. TRANSACTION UPDATE EXECUTION BLOCK
if (isset($_POST['update'])) {
    $updated_name = mysqli_real_escape_string($conn, trim($_POST['brand_name']));
    $final_logo_path = $current_logo; // Maintain the existing file as fallback

    // Check if a new graphic file upload request has been submitted
    if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $file_extension = strtolower(pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions)) {
            // Generate a unique non-overlapping tracking filename
            $unique_filename = "brand_" . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $upload_directory = "uploads/";
            $new_upload_path = $upload_directory . $unique_filename;
            
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0777, true);
            }

            if (move_uploaded_file($_FILES['logo_image']['tmp_name'], $new_upload_path)) {
                $final_logo_path = $new_upload_path;
                
                // Clear the old file from server memory storage to optimize host bandwidth
                if (!empty($current_logo) && file_exists($current_logo)) {
                    @unlink($current_logo);
                }
            } else {
                $msg = "Failed to upload the updated graphic asset to the directory.";
                $msg_class = "alert-danger";
            }
        } else {
            $msg = "Invalid extension! Only PNG, JPG, JPEG, WEBP, and SVG formats are permitted.";
            $msg_class = "alert-danger";
        }
    }

    // Database sync operation if validation logs are completely clear
    if (empty($msg)) {
        if (!empty($updated_name)) {
            $update_query = "UPDATE `brands` SET `brand_name` = '$updated_name', `logo_path` = '$final_logo_path' WHERE `id` = '$brand_id'";
            
            if (mysqli_query($conn, $update_query)) {
                $msg = "Associated brand specifications updated successfully!";
                $msg_class = "alert-success";
                
                // Refresh memory variables with newly committed updates
                $current_name = $updated_name;
                $current_logo = $final_logo_path;
            } else {
                $msg = "Database Update Error: " . mysqli_error($conn);
                $msg_class = "alert-danger";
            }
        } else {
            $msg = "Brand title reference string cannot be logged empty.";
            $msg_class = "alert-danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Brand Specifications | Admin Panel</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <style>
        .form-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .current-asset-preview {
            width: 110px;
            height: 110px;
            object-fit: contain;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 6px;
            border-radius: 8px;
        }
        .preview-box-container {
            width: 110px;
            height: 110px;
            object-fit: contain;
            background-color: #f8f9fa;
            border: 2px dashed #0d6efd;
            display: none;
            padding: 6px;
            border-radius: 8px;
        }
    </style>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <?php include "top_nav.php"; ?>
                </div>
            </div>
        </div>

        <div class="main_content_iner">
            <div class="container-fluid p-3">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-12">
                        <div class="white_card mb_30 form-card">
                            <div class="card-header bg-white border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="mb-0 fw-bold">Modify Brand Partner</h2>
                                        <p class="text-muted mb-0 small">Update parameters for Registered Partner ID: #<?= $brand_id ?></p>
                                    </div>
                                    <a href="brands.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to List
                                    </a>
                                </div>
                            </div>

                            <div class="white_card_body py-4">
                                <?php if (!empty($msg)): ?>
                                    <div class="alert <?= $msg_class ?> alert-dismissible fade show" role="alert">
                                        <?= $msg ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Brand Associated Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="brand_name" placeholder="e.g. Atlas Copco" value="<?= htmlspecialchars($current_name) ?>" required>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                                            <label class="form-label d-block fw-bold">Current Active Logo</label>
                                            <img src="<?= (!empty($current_logo) && file_exists($current_logo)) ? $current_logo : 'uploads/default-brand.png' ?>" 
                                                 alt="Current Logo" 
                                                 class="current-asset-preview"
                                                 onerror="this.src='uploads/default-brand.png'">
                                        </div>
                                        
                                        <div class="col-md-8 col-12">
                                            <label class="form-label fw-bold">Upload New Logo (Optional)</label>
                                            <input type="file" class="form-control" name="logo_image" id="brandLogoInput" accept="image/*">
                                            <small class="text-muted d-block mt-1">Leave empty to keep the current graphic. Formats: PNG, JPG, WebP, SVG</small>
                                            
                                            <div class="mt-2">
                                                <img id="brandLogoPreview" class="preview-box-container" src="#" alt="New Logo Preview">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end pt-3 border-top">
                                        <a href="brands.php" class="btn btn-light me-2">Cancel</a>
                                        <button type="submit" name="update" class="btn btn-info px-4 text-white">
                                            <i class="fas fa-save me-2"></i>Update Specifications
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "footer.php"; ?>

    <script>
        // Real-time replacement file preview loader
        document.getElementById('brandLogoInput').addEventListener('change', function(event) {
            const previewImage = document.getElementById('brandLogoPreview');
            const fileReference = event.target.files[0];
            
            if (fileReference) {
                const fileReaderInstance = new FileReader();
                fileReaderInstance.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                }
                fileReaderInstance.readAsDataURL(fileReference);
            } else {
                previewImage.src = '#';
                previewImage.style.display = 'none';
            }
        });
    </script>
</body>

</html>