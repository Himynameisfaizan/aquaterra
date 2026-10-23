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

if (isset($_POST['add_blog'])) {$title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $author = mysqli_real_escape_string($conn, trim($_POST['author']));$description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;          // SEO Fields
    $meta_title = mysqli_real_escape_string($conn, trim($_POST['meta_title']));
    $meta_key = mysqli_real_escape_string($conn, trim($_POST['meta_key']));$meta_desc = mysqli_real_escape_string($conn, trim($_POST['meta_desc']));
    
    // Naya Schema Field add kiya gaya
    $schema_markup = mysqli_real_escape_string($conn, trim($_POST['schema_markup']));
    
    $user_slug = trim($_POST['slug']);
    $slug = !empty($user_slug) ? createSlug($user_slug) : createSlug($title);

    if (!empty($title) && !empty($description)) {$image_name = "";
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {$allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            
            if (in_array($file_extension,$allowed_extensions)) {
                $image_name = 'blog_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                $upload_path = "assets/img/uploads/blogs/";
                
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_path .$image_name);
            } else {
                $msg = "Invalid image format! Only JPG, JPEG, PNG, and WEBP are allowed.";
                $msg_class = "alert-danger";
            }
        }

        if (empty($msg)) {
            // Query mein schema_markup add kiya gaya
            $insert_query = "INSERT INTO `blogs` (`title`, `slug`, `author`, `image`, `description`, `status`, `meta_title`, `meta_key`, `meta_desc`, `schema_markup`) 
                             VALUES ('$title', '$slug', '$author', '$image_name', '$description', '$status', '$meta_title', '$meta_key', '$meta_desc', '$schema_markup')";
            
            if (mysqli_query($conn,$insert_query)) {
                header("Location: blog.php?status=added");
                exit();
            } else {
                $msg = "Database Error: " . mysqli_error($conn);$msg_class = "alert-danger";
            }
        }
    } else {
        $msg = "Title and Content are required fields.";
        $msg_class = "alert-warning";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Add New Blog | Admin Panel</title>
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
                                <h3 class="mb-0 fw-bold">Write New Blog Post</h3>
                                <a href="blog.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Blogs</a>
                            </div>
                            <div class="white_card_body py-3">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Blog Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="title" id="blog_title_add" placeholder="Enter blog title" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Custom Slug (SEO URL) <span class="text-danger">*</span></label>
                                            <div class="d-flex">
                                                <span class="slug-input-prefix">site.com/blog/</span>
                                                <input type="text" class="form-control" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" name="slug" id="blog_slug_add" placeholder="e.g. customized-url-structure" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Author Name</label>
                                            <input type="text" class="form-control" name="author" value="Admin">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="1">Publish (Active)</option>
                                                <option value="0">Draft (Hidden)</option>
                                            </select>
                                        </div>

                                        <!-- SEO Fields Section -->
                                        <div class="col-md-12"><hr class="my-3"><h5 class="fw-bold text-primary mb-3">SEO Meta Configuration</h5></div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Meta Title</label>
                                            <input type="text" class="form-control" name="meta_title" placeholder="SEO Title for search engines">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Meta Keywords</label>
                                            <input type="text" class="form-control" name="meta_key" placeholder="keyword1, keyword2, keyword3">
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">Meta Description</label>
                                            <textarea class="form-control" name="meta_desc" rows="2" placeholder="Brief summary for Google search results (150-160 characters)"></textarea>
                                        </div>
                                        
                                        <!-- Schema Markup Textarea -->
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">SEO Schema Markup (JSON-LD)</label>
                                            <textarea class="form-control" name="schema_markup" rows="6" placeholder="Yahan pura <script type='application/ld+json'>...</script> code paste karein"></textarea>
                                            <small class="text-muted">SEO expert yahan blog ka custom schema paste kar sakte hain.</small>
                                        </div>
                                        
                                        <div class="col-md-12"><hr class="my-3"></div>

                                        <div class="col-md-12 mb-4">
                                            <label class="form-label fw-bold">Featured Image</label>
                                            <input type="file" class="form-control" name="image" id="imageInput" accept="image/*">
                                            <img id="imagePreview" class="preview-img" src="#" alt="Preview">
                                        </div>

                                        <div class="col-md-12 mb-4">
                                            <label class="form-label fw-bold">Blog Content <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="description" id="add_blog_content" rows="10" placeholder="Type blog description here..." required></textarea>
                                        </div>
                                        
                                        <div class="col-md-12 text-end">
                                            <button type="submit" name="add_blog" class="btn btn-primary px-5 py-2"><i class="fas fa-paper-plane me-2"></i>Publish Blog Post</button>
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
        CKEDITOR.replace('add_blog_content', {
            on: {
                dialogShow: function(dialogEvent) {
                    if (dialogEvent.data.name === 'link') {
                        var dialog = dialogEvent.data;
                        setTimeout(function() {
                            var urlInput = dialog.getContentElement('info', 'url');
                            if (urlInput && urlInput.getInputElement()) {
                                urlInput.getInputElement().focus();
                            }
                        }, 100);
                    }
                }
            }
        });

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
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) { 
                    preview.src = e.target.result; 
                    preview.style.display = 'block'; 
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>