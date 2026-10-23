<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Database central core mapping connection pipeline
include "db-conn.php";

$msg = "";
$msg_class = "";
$brand_name = "";

if (isset($_POST['submit'])) {
    $brand_name = mysqli_real_escape_string($conn, trim($_POST['brand_name']));

    // 1. BRAND LOGO VALIDATION & UPLOAD PROCESSING
    $final_logo_path = "";
    if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $file_extension = strtolower(pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions)) {
            // Unique file extension hash marker generation to prevent file overwrites
            $unique_filename = "brand_" . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            
            // Setting directory base target path as per your existing storage hierarchy
            $upload_directory = "uploads/";
            $final_logo_path = $upload_directory . $unique_filename;
            
            // Check if the base upload folder exists; if not, create it dynamically
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0777, true);
            }

            if (!move_uploaded_file($_FILES['logo_image']['tmp_name'], $final_logo_path)) {
                $msg = "Failed to upload brand logo to server folder.";
                $msg_class = "alert-danger";
            }
        } else {
            $msg = "Invalid extension! Only JPG, JPEG, PNG, WEBP, and SVG formats are allowed.";
            $msg_class = "alert-danger";
        }
    } else {
        $msg = "Please select a valid corporate brand logo image file.";
        $msg_class = "alert-danger";
    }

    // 2. DATABASE TRANSACTION BLOCK
    if (empty($msg)) {
        if (!empty($brand_name) && !empty($final_logo_path)) {
            // Mapping column headers strictly matching u776339737_bestok.brands table schema
            $insert_query = "INSERT INTO `brands` (`brand_name`, `logo_path`) VALUES ('$brand_name', '$final_logo_path')";
            
            if (mysqli_query($conn, $insert_query)) {
                $msg = "New client brand logo has been registered successfully!";
                $msg_class = "alert-success";
                // Reset text inputs to clear user form states upon transaction success
                $brand_name = "";
            } else {
                $msg = "Database Operational Failure: " . mysqli_error($conn);
                $msg_class = "alert-danger";
            }
        } else {
            $msg = "All operational form field nodes are required!";
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
    <title>Add Brand Logo | Admin Panel</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <style>
        .form-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .preview-box-container {
            width: 120px;
            height: 120px;
            object-fit: contain;
            background-color: #f8f9fa;
            border: 2px dashed #ddd;
            display: none;
            margin-top: 12px;
            padding: 8px;
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
                                        <h2 class="mb-0 fw-bold">Add New Brand Partner</h2>
                                        <p class="text-muted mb-0 small">Publish associated industrial corporate logos to the front-end carousel</p>
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
                                        <input type="text" class="form-control" name="brand_name" placeholder="e.g. Atlas Copco, Ingersoll Rand" value="<?= htmlspecialchars($brand_name) ?>" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Brand Corporate Logo Image <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="logo_image" id="brandLogoInput" accept="image/*" required>
                                        <small class="text-muted d-block mt-1">Recommended framework: Use transparent background landscape grid PNG assets. Allowed file forms: PNG, JPG, WebP, SVG</small>
                                        <img id="brandLogoPreview" class="preview-box-container" src="#" alt="Logo Preview">
                                    </div>

                                    <div class="text-end pt-3 border-top">
                                        <button type="reset" class="btn btn-light me-2" onclick="document.getElementById('brandLogoPreview').style.display='none';">Reset Fields</button>
                                        <button type="submit" name="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save me-2"></i>Save Brand Logo
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
        // Real-time local image upload rendering block
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