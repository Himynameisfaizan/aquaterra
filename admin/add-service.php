<?php
session_start();
include "db-conn.php";

// ==========================================
// FETCH DATA FOR EDIT MODE
// ==========================================
$edit_mode = false;
$service = [];

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    } else {
        header("Location: view-service.php");
        exit();
    }
}

// ==========================================
// HANDLE FORM SUBMISSION (ADD & UPDATE)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = trim($_POST['service_name']);
    $short_desc = trim($_POST['short_desc']);
    $long_desc = trim($_POST['long_desc']);
    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;

    // Existing image path (for update logic)
    $img_path = $edit_mode ? $service['img_path'] : "";
    $upload_ok = true;

    // Validate Input
    if (empty($service_name) || empty($short_desc)) {
        $_SESSION['error'] = "Service Name and Short Description are required!";
        $upload_ok = false;
    }

    // Image Upload Logic
    if ($upload_ok && isset($_FILES['img_path']) && $_FILES['img_path']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "assets/img/uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExt = strtolower(pathinfo($_FILES['img_path']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($fileExt, $allowed)) {
            $newFileName = time() . '_service.' . $fileExt;
            $uploadFilePath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['img_path']['tmp_name'], $uploadFilePath)) {

                // Nayi image upload ho gayi, purani ko delete kar do (Agar update ho raha hai)
                if ($edit_mode && !empty($service['img_path'])) {
                    $old_path = $uploadDir . $service['img_path'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }

                $img_path = $newFileName;
            } else {
                $_SESSION['error'] = "Failed to upload image. Folder permissions issue.";
                $upload_ok = false;
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, PNG, WEBP allowed.";
            $upload_ok = false;
        }
    }

    // Proceed to Database only if no errors
    if ($upload_ok) {
        if ($service_id > 0) {
            // UPDATE
            $sql = "UPDATE services SET service_name=?, short_desc=?, long_desc=?, img_path=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $service_name, $short_desc, $long_desc, $img_path, $service_id);
            $msg = "Service updated successfully!";
        } else {
            // INSERT
            $sql = "INSERT INTO services (service_name, short_desc, long_desc, img_path) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $service_name, $short_desc, $long_desc, $img_path);
            $msg = "Service added successfully!";
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = $msg;
            header("Location: view-service.php");
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
    <title><?= $edit_mode ? 'Edit' : 'Add New' ?> Service | Admin</title>
    <?php include "links.php"; ?>

    <!-- CKEditor for Long Description -->
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
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
                    <div class="col-lg-10">
                        <div class="white_card card_height_100 mb_30">

                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="m-0"><?= $edit_mode ? 'Edit Service Details' : 'Add New Service' ?></h2>
                                    </div>
                                    <div class="add_button ms-2">
                                        <a href="view-service.php" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-list me-1"></i> View All Services
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="white_card_body">

                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger font-weight-bold">
                                        <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error'];
                                                                                    unset($_SESSION['error']); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Form submits to itself -->
                                <form action="" method="POST" enctype="multipart/form-data" class="p-4 shadow-sm bg-white rounded border">

                                    <?php if ($edit_mode): ?>
                                        <input type="hidden" name="service_id" value="<?= htmlspecialchars($service['id']); ?>">
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-4">
                                                <label class="form-label fw-bold">Service Name *</label>
                                                <input type="text" name="service_name" class="form-control" placeholder="e.g. Quality Inspection, Export Packaging"
                                                    value="<?= $edit_mode ? htmlspecialchars($service['service_name']) : ''; ?>" required>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label fw-bold">Short Description *</label>
                                                <textarea name="short_desc" class="form-control" rows="3" placeholder="A brief summary for the service card..." required><?= $edit_mode ? htmlspecialchars($service['short_desc']) : ''; ?></textarea>
                                                <small class="text-muted">Keep this under 150 characters for best UI appearance on cards.</small>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label fw-bold">Full/Long Description</label>
                                                <textarea name="long_desc" id="long_desc" class="form-control" rows="8" placeholder="Detailed service explanation..."><?= $edit_mode ? htmlspecialchars($service['long_desc']) : ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-4 p-3 border rounded bg-light">
                                                <label class="form-label fw-bold">Service Image (<?= $edit_mode ? 'Optional' : 'Required' ?>)</label>
                                                <input type="file" name="img_path" class="form-control" accept="image/*" id="imgInput" <?= $edit_mode ? '' : 'required' ?>>
                                                <small class="text-muted d-block mt-1">Max 5MB. PNG/JPG/WEBP allowed.</small>

                                                <!-- Image Preview Area -->
                                                <div class="mt-3 text-center border bg-white rounded" style="min-height: 200px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                    <?php if ($edit_mode && !empty($service['img_path'])): ?>
                                                        <img id="imgPreview" src="assets/img/uploads/<?= htmlspecialchars($service['img_path']); ?>" alt="Service Image" style="width: 100%; height: auto; display: block;">
                                                    <?php else: ?>
                                                        <img id="imgPreview" src="#" alt="Preview" style="width: 100%; height: auto; display: none;">
                                                        <span id="previewText" class="text-muted" <?= $edit_mode ? 'style="display:none;"' : '' ?>><i class="fas fa-image fa-3x mb-2"></i><br>Image Preview</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="mb-2 mt-4">
                                                <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm">
                                                    <i class="fas fa-save me-1"></i> <?= $edit_mode ? 'Update Service' : 'Publish Service' ?>
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

        <?php include "footer.php"; ?>

        <script>
            // Initialize CKEditor for Long Description
            CKEDITOR.replace('long_desc', {
                toolbar: [{
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Underline', 'Strike']
                    },
                    {
                        name: 'paragraph',
                        items: ['NumberedList', 'BulletedList']
                    },
                    {
                        name: 'links',
                        items: ['Link', 'Unlink']
                    },
                    {
                        name: 'document',
                        items: ['Source']
                    }
                ],
                height: 250
            });

            // Smart Image Preview Logic
            document.getElementById('imgInput').addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var text = document.getElementById('previewText');
                        if (text) text.style.display = 'none';

                        var preview = document.getElementById('imgPreview');
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        </script>
    </section>
</body>

</html>