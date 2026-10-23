<?php
session_start();
include "db-conn.php";

// ==========================================
// FETCH DATA FOR EDIT MODE
// ==========================================
$edit_mode = false;
$country = [];

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM country WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $country = $result->fetch_assoc();
    } else {
        header("Location: view-country.php");
        exit();
    }
}

// ==========================================
// HANDLE FORM SUBMISSION (ADD & UPDATE)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $country_name = trim($_POST['country_name']);
    $country_desc = trim($_POST['country_desc']);
    $country_id = isset($_POST['country_id']) ? (int)$_POST['country_id'] : 0;
    
    // Existing flag name (for update logic)
    $country_flag = $edit_mode ? $country['country_flag'] : "";
    $upload_ok = true;

    // Validate Input
    if (empty($country_name)) {
        $_SESSION['error'] = "Country name is required!";
        $upload_ok = false;
    }

    // Image Upload Logic
    if ($upload_ok && isset($_FILES['country_flag']) && $_FILES['country_flag']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "assets/img/uploads/";
        if (!file_exists($uploadDir)) { mkdir($uploadDir, 0755, true); }
        
        $fileExt = strtolower(pathinfo($_FILES['country_flag']['name'], PATHINFO_EXTENSION));
        // Flag mostly PNG, JPG or SVG formats mein aate hain, isliye svg allow kiya hai
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'];
        
        if (in_array($fileExt, $allowed)) {
            $newFileName = time() . '_flag.' . $fileExt;
            $uploadPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['country_flag']['tmp_name'], $uploadPath)) {
                
                // Nayi image upload ho gayi, purani ko delete kar do (Agar update ho raha hai)
                if ($edit_mode && !empty($country['country_flag'])) {
                    $old_path = $uploadDir . $country['country_flag'];
                    if (file_exists($old_path)) { unlink($old_path); }
                }
                
                $country_flag = $newFileName;
            } else {
                $_SESSION['error'] = "Failed to upload flag image.";
                $upload_ok = false;
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, PNG, WEBP, and SVG are allowed.";
            $upload_ok = false;
        }
    }

    // Proceed to Database only if no errors
    if ($upload_ok) {
        if ($country_id > 0) {
            // UPDATE
            $sql = "UPDATE country SET country_name=?, country_desc=?, country_flag=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $country_name, $country_desc, $country_flag, $country_id);
            $msg = "Country updated successfully!";
        } else {
            // INSERT
            $sql = "INSERT INTO country (country_name, country_desc, country_flag) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $country_name, $country_desc, $country_flag);
            $msg = "Country added successfully!";
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = $msg;
            header("Location: view-country.php");
            exit();
        } else {
            $_SESSION['error'] = "Database Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= $edit_mode ? 'Edit' : 'Add' ?> Country | Admin</title>
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
                    <div class="col-lg-8"> <!-- 8 columns because it's a smaller form -->
                        <div class="white_card card_height_100 mb_30">
                            
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="m-0"><?= $edit_mode ? 'Edit' : 'Add New' ?> Country</h2>
                                    </div>
                                    <div class="add_button ms-2">
                                        <a href="view-country.php" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-arrow-left me-1"></i> Back to List
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="white_card_body">
                                
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger font-weight-bold">
                                        <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Notice: action is empty string so it submits to itself -->
                                <form action="" method="POST" enctype="multipart/form-data" class="p-3 shadow-sm bg-white rounded border">
                                    
                                    <?php if ($edit_mode): ?>
                                        <input type="hidden" name="country_id" value="<?= htmlspecialchars($country['id']); ?>">
                                    <?php endif; ?>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Country Name *</label>
                                        <input type="text" name="country_name" class="form-control" placeholder="e.g. United States, UAE, UK" 
                                               value="<?= $edit_mode ? htmlspecialchars($country['country_name']) : ''; ?>" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Short Description</label>
                                        <textarea name="country_desc" class="form-control" rows="4" placeholder="Brief details about export activities in this country..."><?= $edit_mode ? htmlspecialchars($country['country_desc']) : ''; ?></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Country Flag Image (<?= $edit_mode ? 'Optional to change' : 'Required' ?>)</label>
                                        <input type="file" name="country_flag" class="form-control" accept="image/*" id="flagInput" <?= $edit_mode ? '' : 'required' ?>>
                                        <small class="text-muted d-block mt-1">Recommended: SVG or PNG for transparent backgrounds.</small>
                                        
                                        <!-- Image Preview Container -->
                                        <div class="mt-3 text-center border p-2 bg-light rounded" style="min-height: 120px;">
                                            <?php if ($edit_mode && !empty($country['country_flag'])): ?>
                                                <img id="flagPreview" src="assets/img/uploads/<?= htmlspecialchars($country['country_flag']); ?>" alt="Flag" style="max-width: 150px; max-height: 100px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            <?php else: ?>
                                                <img id="flagPreview" src="#" alt="Preview" style="max-width: 150px; max-height: 100px; display: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                                <span id="previewPlaceholder" class="text-muted" <?= $edit_mode ? 'style="display:none;"' : '' ?>><br>Flag Preview Here</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mb-2 text-end">
                                        <button type="submit" class="btn btn-success btn-lg px-4">
                                            <i class="fas fa-save me-1"></i> <?= $edit_mode ? 'Update' : 'Save' ?> Country
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "footer.php"; ?>
        
        <script>
            // Simple Image Preview Logic
            document.getElementById('flagInput').addEventListener('change', function(e) {
                if(this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var placeholder = document.getElementById('previewPlaceholder');
                        if(placeholder) placeholder.style.display = 'none';
                        
                        var preview = document.getElementById('flagPreview');
                        preview.src = e.target.result;
                        preview.style.display = 'inline-block';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        </script>
    </section>
</body>
</html>