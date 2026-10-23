<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "db-conn.php";

$msg = "";
$msg_class = "";

function createSlug($string) {
    return preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($string)));
}

$blog_id = mysqli_real_escape_string($conn,$_GET['id']);

// --- UPDATE LOGIC ---
if (isset($_POST['update_blog'])) {
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));$author = mysqli_real_escape_string($conn, trim($_POST['author']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));$status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    
    $meta_title = mysqli_real_escape_string($conn, trim($_POST['meta_title']));
    $meta_key = mysqli_real_escape_string($conn, trim($_POST['meta_key']));$meta_desc = mysqli_real_escape_string($conn, trim($_POST['meta_desc']));
    
    // Schema field capture kiya
    $schema_markup = mysqli_real_escape_string($conn, trim($_POST['schema_markup']));
    
    $user_slug = trim($_POST['slug']);
    $slug = !empty($user_slug) ? createSlug($user_slug) : createSlug($title);

    $image_name =$_POST['old_image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {$allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension,$allowed_extensions)) {
            $new_image = 'blog_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $upload_path = "assets/img/uploads/blogs/";

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path .$new_image)) {
                if (!empty($image_name) && file_exists($upload_path .$image_name)) {
                    unlink($upload_path .$image_name);
                }
                $image_name =$new_image;
            }
        } else {
            $msg = "Invalid image format!";
            $msg_class = "alert-danger";
        }
    }

    if(empty($msg)) {
        // Query mein schema_markup update lagaya
        $update_query = "UPDATE `blogs` SET `title` = '$title', `slug` = '$slug', `author` = '$author', `image` = '$image_name', `description` = '$description', `status` = '$status', `meta_title` = '$meta_title', `meta_key` = '$meta_key', `meta_desc` = '$meta_desc', `schema_markup` = '$schema_markup' WHERE `blog_id` = '$blog_id'";
        
        if (mysqli_query($conn,$update_query)) {
            header("Location: blog.php?status=updated");
            exit;
        } else {
            $msg = "Update Error: " . mysqli_error($conn);$msg_class = "alert-danger";
        }
    }
}

// Fetch existing data for form
$fetch_query = mysqli_query($conn, "SELECT * FROM blogs WHERE blog_id = '$blog_id'");
if(mysqli_num_rows($fetch_query) == 0) { header("Location: blog.php"); exit; }
$blog_data = mysqli_fetch_assoc($fetch_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Blog | Admin Panel</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <style>
        .custom-card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
        .preview-img { max-width: 200px; max-height: 150px; display: none; margin-top: 10px; border-radius: 8px; border: 1px solid #ddd; padding: 5px;}
        .slug-input-prefix { background-color: #f1f3f5; color: #6c757d; font-size: 0.85rem; display: flex; align-items: center; padding: 0 10px; border: 1px solid #ced4da; border-right: 0; border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;}
    </style>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0"><div class="row"><div class="col-lg-12 p-0"><?php include "top_nav.php"; ?></div></div></div>

        <div class="main_content_iner">
            <div class="container-fluid p-3">
                
                <?php if (!empty($msg)): ?>
                    <div class="alert <?= $msg_class ?> alert-dismissible fade show" role="alert">
                        <?= $msg ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 mb-4">
                        <div class="white_card custom-card">
                            <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0 fw-bold">Update Blog Post</h3>
                                <a href="blog.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Blogs</a>
                            </div>
                            <div class="white_card_body py-3">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="old_image" value="<?= $blog_data['image']; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Blog Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="title" id="blog_title_add" value="<?= htmlspecialchars($blog_data['title']); ?>" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Custom Slug (SEO URL) <span class="text-danger">*</span></label>
                                            <div class="d-flex">
                                                <span class="slug-input-prefix">site.com/blog/</span>
                                                <input type="text" class="form-control" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" name="slug" id="blog_slug_add" value="<?= htmlspecialchars($blog_data['slug']); ?>" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Author Name</label>
                                            <input type="text" class="form-control" name="author" value="<?= htmlspecialchars($blog_data['author']); ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="1" <?= $blog_data['status'] == 1 ? 'selected' : ''; ?>>Publish (Active)</option>
                                                <option value="0" <?= $blog_data['status'] == 0 ? 'selected' : ''; ?>>Draft (Hidden)</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12"><hr class="my-3"><h5 class="fw-bold text-primary mb-3">SEO Meta Configuration</h5></div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Meta Title</label>
                                            <input type="text" class="form-control" name="meta_title" value="<?= htmlspecialchars($blog_data['meta_title']); ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Meta Keywords</label>
                                            <input type="text" class="form-control" name="meta_key" value="<?= htmlspecialchars($blog_data['meta_key']); ?>">
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">Meta Description</label>
                                            <textarea class="form-control" name="meta_desc" rows="2"><?= htmlspecialchars($blog_data['meta_desc']); ?></textarea>
                                        </div>
                                        
                                        <!-- Schema Markup Field fetch kiya gaya -->
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">SEO Schema Markup (JSON-LD)</label>
                                            <textarea class="form-control" name="schema_markup" rows="6"><?= htmlspecialchars($blog_data['schema_markup'] ?? ''); ?></textarea>
                                            <small class="text-muted">SEO expert yahan blog ka custom schema paste kar sakte hain.</small>
                                        </div>
                                        
                                        <div class="col-md-12"><hr class="my-3"></div>

                                        <div class="col-md-12 mb-4">
                                            <label class="form-label fw-bold">Change Image <small class="text-muted">(Leave empty to keep current)</small></label>
                                            <input type="file" class="form-control" name="image" id="imageInput" accept="image/*">
                                            
                                            <img id="imagePreview" class="preview-img" src="#" alt="Preview">
                                            <?php if(!empty($blog_data['image'])): ?>
                                                <div class="mt-2" id="currentImgContainer">
                                                    <span class="small text-muted d-block">Current Image:</span>
                                                    <img src="assets/img/uploads/blogs/<?= $blog_data['image']; ?>" style="max-width: 150px; border-radius: 5px; border: 1px solid #ddd; padding: 3px;">
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-12 mb-4">
                                            <label class="form-label fw-bold">Blog Content <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="description" id="add_blog_content" rows="10" required><?= $blog_data['description']; ?></textarea>
                                        </div>
                                        
                                        <div class="col-md-12 text-end">
                                            <button type="submit" name="update_blog" class="btn btn-primary px-5 py-2"><i class="fas fa-save me-2"></i>Update Blog Post</button>
                                        </div>
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
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('add_blog_content');

        function convertToSlug(text) {
            return text.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');        
        }

        document.getElementById('blog_title_add').addEventListener('input', function() {
            document.getElementById('blog_slug_add').value = convertToSlug(this.value);
        });
        
        document.getElementById('blog_slug_add').addEventListener('blur', function() {
            this.value = convertToSlug(this.value);
        });

        document.getElementById('imageInput').addEventListener('change', function(event) {
            const preview = document.getElementById('imagePreview');
            const current = document.getElementById('currentImgContainer');
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
</body>
</html>