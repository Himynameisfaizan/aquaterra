<?php
session_start();
include "db-conn.php";

// Get testimonial id from URL
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $testimonial_id = intval($_GET['edit']);
    
    // Fetch testimonial details directly
    $stmt = $conn->prepare("SELECT * FROM testimonials WHERE test_id = ?");
    $stmt->bind_param("i", $testimonial_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $testimonial = $result->fetch_assoc();
    
    if (!$testimonial) {
        echo "Testimonial not found.";
        exit;
    }
} else {
    echo "Invalid testimonial id.";
    exit;
}

// Process form submission for updating the testimonial
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_name = mysqli_real_escape_string($conn, $_POST['client_name'] ?? '');
    $client_title = mysqli_real_escape_string($conn, $_POST['client_title'] ?? '');
    $client_company = mysqli_real_escape_string($conn, $_POST['client_company'] ?? '');
    $testimonial_text = mysqli_real_escape_string($conn, $_POST['testimonial_text'] ?? '');
    $rating = intval($_POST['rating'] ?? 5);
    $project_name = mysqli_real_escape_string($conn, $_POST['project_name'] ?? '');
    $project_date = mysqli_real_escape_string($conn, $_POST['project_date'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $display_order = intval($_POST['display_order'] ?? 0);
    
    // Handle file upload
    $client_photo = $testimonial['image']; 
    
    if (!empty($_FILES['client_photo']['name'])) {
        $upload_dir = "uploads/testimonials/";
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true); 
        }
        
        $file_name = basename($_FILES['client_photo']['name']);
        $target_path = $upload_dir . $file_name;
        
        $imageFileType = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['client_photo']['tmp_name'], $target_path)) {
                $client_photo = $file_name;
                if (!empty($testimonial['image']) && $testimonial['image'] != $file_name) {
                    @unlink($upload_dir . $testimonial['image']);
                }
            } else {
                $_SESSION['error'] = "Error uploading file.";
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, GIF & WEBP files are allowed.";
        }
    }
    
    $update_sql = "UPDATE testimonials SET 
                    name = '$client_name', 
                    designation = '$client_title', 
                    client_company = '$client_company', 
                    image = '$client_photo', 
                    message = '$testimonial_text', 
                    rating = '$rating', 
                    project_name = '$project_name', 
                    project_date = " . ($project_date ? "'$project_date'" : "NULL") . ", 
                    featured = '$featured', 
                    display_order = '$display_order' 
                   WHERE test_id = '$testimonial_id'";
                   
    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['success'] = "Testimonial updated successfully!";
        header("Location: view-testimonials.php"); 
        exit;
    } else {
        $_SESSION['error'] = "Error updating testimonial: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Testimonial</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <style>
        .rating-stars {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }
        .rating-stars i {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }
        .rating-stars i.active {
            color: #ffc107;
        }
        .preview-image {
            max-width: 150px;
            max-height: 150px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 3px;
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
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="m-0">Edit Testimonial</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body">
                                <div class="QA_section">
                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_SESSION['success'])): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="white_box_tittle list_header">
                                        <div class="box_right d-flex lms_block">
                                            <div class="add_button ms-2">
                                                <a href="view-testimonials.php" class="btn_1">Back To Testimonials</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="QA_table mb_30">
                                        <form action="" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="testimonial_id" value="<?= htmlspecialchars($testimonial['test_id']) ?>">
                                            
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Client Name *</label>
                                                    <input type="text" name="client_name" class="form-control" 
                                                           value="<?= htmlspecialchars($testimonial['name']) ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Client Title</label>
                                                    <input type="text" name="client_title" class="form-control" 
                                                           value="<?= htmlspecialchars($testimonial['designation']) ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Client Company</label>
                                                    <input type="text" name="client_company" class="form-control" 
                                                           value="<?= htmlspecialchars($testimonial['client_company']) ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Project Name</label>
                                                    <input type="text" name="project_name" class="form-control" 
                                                           value="<?= htmlspecialchars($testimonial['project_name']) ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Project Date</label>
                                                    <input type="date" name="project_date" class="form-control" 
                                                           value="<?= htmlspecialchars($testimonial['project_date']) ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Display Order</label>
                                                    <input type="number" name="display_order" class="form-control" 
                                                           value="<?= htmlspecialchars($testimonial['display_order']) ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Rating *</label>
                                                <!-- Hidden input to store rating value -->
                                                <input type="hidden" name="rating" id="ratingValue" value="<?= htmlspecialchars($testimonial['rating']) ?>" required>
                                                <div class="rating-stars" id="ratingStars">
                                                    <i class="fas fa-star" data-value="1"></i>
                                                    <i class="fas fa-star" data-value="2"></i>
                                                    <i class="fas fa-star" data-value="3"></i>
                                                    <i class="fas fa-star" data-value="4"></i>
                                                    <i class="fas fa-star" data-value="5"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Testimonial Text *</label>
                                                <textarea name="testimonial_text" class="form-control" rows="5" required><?= 
                                                    htmlspecialchars($testimonial['message']) ?></textarea>
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Client Photo</label>
                                                    <?php if (!empty($testimonial['image'])): ?>
                                                        <img src="uploads/testimonials/<?= htmlspecialchars($testimonial['image']) ?>" 
                                                             class="preview-image d-block mb-2">
                                                    <?php endif; ?>
                                                    <input type="file" name="client_photo" class="form-control" accept="image/*">
                                                    <small class="text-muted">Leave blank to keep existing photo</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch mt-4">
                                                        <input class="form-check-input" type="checkbox" name="featured" 
                                                               id="featured" <?= $testimonial['featured'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="featured">Featured Testimonial</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary">Update Testimonial</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include "footer.php"; ?>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingValue = document.getElementById('ratingValue').value;
            const stars = document.querySelectorAll('#ratingStars i');

            // Highlight initial rating loaded from DB
            if (ratingValue) {
                highlightStars(ratingValue);
            }

            // Click event to change rating
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    document.getElementById('ratingValue').value = value;
                    highlightStars(value);
                });
            });
        });

        function highlightStars(value) {
            const stars = document.querySelectorAll('#ratingStars i');
            stars.forEach(star => {
                if (star.getAttribute('data-value') <= value) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }
        
        // Image preview
        document.querySelector('input[name="client_photo"]').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const preview = document.querySelector('.preview-image') || 
                    document.createElement('img');
                
                if (!document.querySelector('.preview-image')) {
                    preview.className = 'preview-image d-block mb-2';
                    this.parentNode.insertBefore(preview, this);
                }
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>