<?php
session_start();
include "db-conn.php";

// Check admin authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Add Category | Admin Panel</title>
    <link rel="icon" href="img/logo.png" type="image/png">

    <?php include "links.php"; ?>

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --success-color: #4bb543;
        }

        .category-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .section-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(76, 201, 240, 0.25);
        }

        .btn-submit {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
        }

        .image-preview-container {
            display: none;
            margin-top: 1rem;
            text-align: center;
        }

        .image-preview {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #eee;
        }

        .file-upload-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .file-upload-label {
            display: block;
            padding: 0.75rem 1rem;
            border: 1px dashed #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-upload-label:hover {
            border-color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.05);
        }

        .file-upload-label i {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
    </style>
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
                                        <h2 class="m-0">Add New Category</h2>
                                    </div>
                                    <div class="add_button ms-2">
                                        <a href="view-categories.php" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-list me-1"></i> View Categories
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($_SESSION['errors'])): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($_SESSION['errors'] as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php unset($_SESSION['errors']); ?>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['form_data'])): ?>
                                <script>
                                    // Repopulate form fields
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const formData = <?= json_encode($_SESSION['form_data']) ?>;
                                        for (const key in formData) {
                                            const element = document.querySelector(`[name="${key}"]`);
                                            if (element) {
                                                element.value = formData[key];
                                            }
                                        }
                                    });
                                </script>
                                <?php unset($_SESSION['form_data']); ?>
                            <?php endif; ?>
                            <div class="white_card_body">
                                <div class="category-card">
                                    <h3 class="section-title">Category Details</h3>
                                    <form id="categoryForm" action="functions.php" method="post"
                                        enctype="multipart/form-data">
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="cate_name">Category Name</label>
                                                <input type="text" class="form-control" name="cate_name" id="cate_name"
                                                    placeholder="Enter category name" required />
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="meta_title">Meta Title</label>
                                                <input type="text" class="form-control" name="meta_title"
                                                    id="meta_title" placeholder="Enter meta title for SEO" required />
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="meta_key">Meta Keywords</label>
                                                <input type="text" class="form-control" name="meta_key" id="meta_key"
                                                    placeholder="Comma separated keywords" required />
                                                <small class="text-muted">Example: electronics, gadgets, tech</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="meta_desc">Meta Description</label>
                                                <textarea class="form-control" name="meta_desc" id="meta_desc" rows="1"
                                                    placeholder="Brief description for SEO" required></textarea>
                                            </div>
                                        </div>

                                        <!-- Schema Markup Box (Add category form ke andar SEO section ke niche add karein) -->
<div class="col-md-12 mb-3">
    <label class="form-label fw-bold" for="schema_markup">SEO Schema Markup (JSON-LD)</label>
    <textarea class="form-control" name="schema_markup" id="schema_markup" rows="6" placeholder="Paste full <script type='application/ld+json'>...</script> here..."></textarea>
    <small class="text-muted">Enter custom schema markup for this category.</small>
</div>

                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="status">Status</label>
                                                <select id="status" name="status" class="form-control" required>
                                                    <option value="1" selected>Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Category Image</label>
                                                <div class="file-upload-wrapper">
                                                    <label for="imageUpload" class="file-upload-label">
                                                        <i class="fas fa-cloud-upload-alt"></i>
                                                        <p class="mb-0">Click to upload or drag and drop</p>
                                                        <small class="text-muted">PNG, JPG, GIF up to 5MB</small>
                                                    </label>
                                                    <input type="file" class="form-control d-none" name="imageUpload"
                                                        id="imageUpload" accept="image/*"
                                                        onchange="previewImage(this)" />
                                                </div>
                                                <div class="image-preview-container" id="imagePreviewContainer">
                                                    <img id="imagePreview" class="image-preview" />
                                                    <button type="button" class="btn btn-sm btn-danger mt-2"
                                                        onclick="removeImage()">
                                                        <i class="fas fa-trash me-1"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="reset" class="btn btn-outline-secondary me-3">
                                                <i class="fas fa-undo me-1"></i> Reset
                                            </button>
                                            <button type="submit" class="btn btn-submit" name="add-categories">
                                                <i class="fas fa-save me-1"></i> Save Category
                                            </button>
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

        <script>
            // Image preview functionality
            function previewImage(input) {
                const previewContainer = document.getElementById('imagePreviewContainer');
                const preview = document.getElementById('imagePreview');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        previewContainer.style.display = 'block';
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Remove image selection
            function removeImage() {
                document.getElementById('imageUpload').value = '';
                document.getElementById('imagePreviewContainer').style.display = 'none';
            }

            // Form validation
            document.getElementById('categoryForm').addEventListener('submit', function (e) {
                const categoryName = document.getElementById('cate_name').value.trim();
                const metaTitle = document.getElementById('meta_title').value.trim();

                if (!categoryName || !metaTitle) {
                    e.preventDefault();
                    alert('Please fill in all required fields');
                    return false;
                }

                // You can add more validation as needed
                return true;
            });

            // Drag and drop functionality
            const uploadLabel = document.querySelector('.file-upload-label');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadLabel.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadLabel.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadLabel.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                uploadLabel.classList.add('bg-light');
            }

            function unhighlight() {
                uploadLabel.classList.remove('bg-light');
            }

            uploadLabel.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                const input = document.getElementById('imageUpload');

                if (files.length) {
                    input.files = files;
                    previewImage(input);
                }
            }
        </script>
    </section>
</body>

</html>