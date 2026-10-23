<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "db-conn.php"; // Update with your actual DB connection

$msg = "";
$msg_class = "";

// --- 1. HANDLE DELETE ACTION ---
if(isset($_GET['delete_id'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    // DB column 'image_url' use kar rahe hain
    $img_query = mysqli_query($conn, "SELECT image_url FROM about_us WHERE id='$del_id'");
    if(mysqli_num_rows($img_query) > 0) {
        $img_row = mysqli_fetch_assoc($img_query);
        if(!empty($img_row['image_url']) && file_exists('uploads/'.$img_row['image_url'])) {
            unlink('uploads/'.$img_row['image_url']);
        }
    }
    mysqli_query($conn, "DELETE FROM about_us WHERE id='$del_id'");
    $msg = "About section deleted successfully!";
    $msg_class = "alert-success";
}

// --- 2. HANDLE ADD/EDIT FORM SUBMISSION ---
if(isset($_POST['submit'])) {
    $id = isset($_POST['id']) ? mysqli_real_escape_string($conn, $_POST['id']) : '';
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $content = mysqli_real_escape_string($conn, trim($_POST['content']));
    
    // SEO & Schema Fields
    $meta_title = mysqli_real_escape_string($conn, trim($_POST['meta_title']));
    $meta_key = mysqli_real_escape_string($conn, trim($_POST['meta_key']));
    $meta_desc = mysqli_real_escape_string($conn, trim($_POST['meta_desc']));
    $schema_markup = mysqli_real_escape_string($conn, trim($_POST['schema_markup']));
    
    // Image Upload Logic
    $image_name = $_POST['old_image'] ?? '';
    
    // Yahan $_FILES['image'] hi rahega kyunki HTML form mein name="image" hai
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_image = 'about_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $upload_path = "uploads/";
            
            if (!is_dir($upload_path)) { mkdir($upload_path, 0777, true); }
            
            // Yahan bhi $_FILES['image'] chalega
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path . $new_image)) {
                if(!empty($image_name) && file_exists($upload_path . $image_name)) {
                    unlink($upload_path . $image_name);
                }
                $image_name = $new_image;
            }
        } else {
            $msg = "Invalid image format! Only JPG, JPEG, PNG, and WEBP are allowed.";
            $msg_class = "alert-danger";
        }
    }

    if(empty($msg)) {
        if(!empty($id)) {
            // UPDATE: Database column 'image_url' use ho raha hai
            $update_query = "UPDATE about_us SET 
                title='$title', content='$content', image_url='$image_name', 
                meta_title='$meta_title', meta_key='$meta_key', meta_desc='$meta_desc', schema_markup='$schema_markup' 
                WHERE id='$id'";
            mysqli_query($conn, $update_query);
            $msg = "About section updated successfully!";
            $msg_class = "alert-success";
        } else {
            // INSERT: Database column 'image_url' use ho raha hai
            $insert_query = "INSERT INTO about_us (title, content, image_url, meta_title, meta_key, meta_desc, schema_markup) 
                VALUES ('$title', '$content', '$image_name', '$meta_title', '$meta_key', '$meta_desc', '$schema_markup')";
            mysqli_query($conn, $insert_query);
            $msg = "New about section published successfully!";
            $msg_class = "alert-success";
        }
        // Redirect to clear POST data and show list
        header("Location: add-about-us-section.php?status=success");
        exit;
    }
}

if(isset($_GET['status']) && $_GET['status'] == 'success') {
    $msg = "Action completed successfully!";
    $msg_class = "alert-success";
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$all_sections = mysqli_query($conn, "SELECT * FROM about_us ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage About Us | Admin Panel</title>
    <!-- Include your CSS links here as in blog.php[cite: 15] -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include "links.php"; ?>
    <style>
        .crm_body_bg { background-color: #f4f7f6; }
        .custom-card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
        .blog-thumb { width: 55px; height: 55px; object-fit: cover; border-radius: 5px; }
        .preview-img { max-width: 200px; max-height: 150px; display: none; margin-top: 10px; border-radius: 8px; border: 1px solid #ddd; padding: 5px;}
        .card-header h3 { color: #0a2540; }
    </style>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part">
        <!-- Top Nav[cite: 15] -->
        <div class="container-fluid g-0"><div class="row"><div class="col-lg-12 p-0"><?php  include "top_nav.php"; ?></div></div></div>

        <div class="main_content_iner">
            <div class="container-fluid p-3">
                
                <?php if (!empty($msg)): ?>
                    <div class="alert <?= $msg_class ?> alert-dismissible fade show" role="alert">
                        <?= $msg ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if($action == 'add' || $action == 'edit'): 
                    $edit_data = [];
                    if($action == 'edit' && isset($_GET['id'])) {
                        $edit_id = mysqli_real_escape_string($conn, $_GET['id']);
                        $query = mysqli_query($conn, "SELECT * FROM about_us WHERE id='$edit_id'");
                        $edit_data = mysqli_fetch_assoc($query);
                    }
                ?>
                <!-- ================= ADD / EDIT VIEW (Matches Add New Blog)[cite: 16] ================= -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 mb-4">
                        <div class="white_card custom-card">
                            <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0 fw-bold"><?= $action == 'edit' ? 'Update About Section' : 'Add New About Section'; ?></h3>
                                <a href="add-about-us-section.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
                            </div>
                            <div class="white_card_body py-3">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?? ''; ?>">
                                    <input type="hidden" name="old_image" value="<?= $edit_data['image'] ?? ''; ?>">

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">Section Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($edit_data['title'] ?? ''); ?>" placeholder="e.g., Mission, Vision, Overview" required>
                                        </div>

                                        <!-- SEO Fields Section[cite: 16] -->
                                        <div class="col-md-12"><hr class="my-3"><h5 class="fw-bold text-primary mb-3">SEO Meta & Schema Configuration</h5></div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Meta Title</label>
                                            <input type="text" class="form-control" name="meta_title" value="<?= htmlspecialchars($edit_data['meta_title'] ?? ''); ?>" placeholder="SEO Title for search engines">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Meta Keywords</label>
                                            <input type="text" class="form-control" name="meta_key" value="<?= htmlspecialchars($edit_data['meta_key'] ?? ''); ?>" placeholder="export, company, history">
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">Meta Description</label>
                                            <textarea class="form-control" name="meta_desc" rows="2" placeholder="Brief summary for Google search results..."><?= htmlspecialchars($edit_data['meta_desc'] ?? ''); ?></textarea>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">JSON-LD Schema Markup</label>
                                            <textarea class="form-control" name="schema_markup" rows="3" placeholder='<script type="application/ld+json">{ ... }</script>'><?= htmlspecialchars($edit_data['schema_markup'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-md-12"><hr class="my-3"></div>

                                        <div class="col-md-12 mb-4">
                                            <label class="form-label fw-bold">Featured Image</label>
                                            <input type="file" class="form-control" name="image" id="imageInput" accept="image/*">
                                            <img id="imagePreview" class="preview-img" src="#" alt="Preview">
                                            <?php if(!empty($edit_data['image'])): ?>
                                                <div class="mt-2" id="currentImage">
                                                    <span class="small text-muted d-block">Current Image:</span>
                                                    <img class="preview-img" style="display:block;" src="uploads/<?= $edit_data['image']; ?>" alt="current">
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-12 mb-4">
                                            <label class="form-label fw-bold">Section Content <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="content" id="about_content" rows="10" required><?= $edit_data['content'] ?? ''; ?></textarea>
                                        </div>
                                        
                                        <div class="col-md-12 text-end">
                                            <button type="submit" name="submit" class="btn btn-primary px-5 py-2" style="background-color: #0a2540; border-color: #0a2540;"><i class="fas fa-save me-2"></i><?= $action == 'edit' ? 'Update Details' : 'Publish Section'; ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Initialize CKEditor & Image Preview -->
                <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
                <script>
                    CKEDITOR.replace('about_content', {
                        on: {
                            dialogShow: function(dialogEvent) {
                                if (dialogEvent.data.name === 'link') {
                                    var dialog = dialogEvent.data;
                                    setTimeout(function() {
                                        var urlInput = dialog.getContentElement('info', 'url');
                                        if (urlInput && urlInput.getInputElement()) { urlInput.getInputElement().focus(); }
                                    }, 100);
                                }
                            }
                        }
                    });

                    document.getElementById('imageInput').addEventListener('change', function(event) {
                        const preview = document.getElementById('imagePreview');
                        const current = document.getElementById('currentImage');
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) { 
                                preview.src = e.target.result; 
                                preview.style.display = 'block'; 
                                if(current) current.style.display = 'none';
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                </script>

                <?php else: ?>
                <!-- ================= LIST VIEW (Matches Published Blogs) ================= -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="white_card custom-card">
                            <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0 fw-bold">Published About Sections</h3>
                                <a href="add-about-us-section.php?action=add" class="btn text-white fw-bold" style="background-color: #d4af37;"><i class="fas fa-plus me-2"></i>Add New Section</a>
                            </div>
                            <div class="white_card_body py-3">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Section Title</th>
                                                <th>SEO Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (mysqli_num_rows($all_sections) > 0): ?>
                                                <?php while($sec = mysqli_fetch_assoc($all_sections)): ?>
                                                    <tr>
                                                        <td>
                                                            <?php if (!empty($sec['image']) && file_exists("uploads/" . $sec['image'])): ?>
                                                                <img class="blog-thumb" src="uploads/<?= $sec['image']; ?>" alt="img">
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">No Image</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($sec['title']); ?></h6>
                                                        </td>
                                                        <td>
                                                            <?php if(!empty($sec['meta_title']) || !empty($sec['schema_markup'])): ?>
                                                                <span class="badge bg-success">Configured</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning text-dark">Missing</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <!-- Edit link via URL parameter instead of Modal -->
                                                            <a href="add-about-us-section.php?action=edit&id=<?= $sec['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                            <a href="add-about-us-section.php?delete_id=<?= $sec['id']; ?>" onclick="return confirm('Are you sure you want to delete this section?');" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr><td colspan="4" class="text-center py-4 text-muted">No about sections found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <!-- JS Scripts[cite: 15] -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>