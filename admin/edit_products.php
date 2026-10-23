<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db-conn.php"; 
include "functions.php";

if (!isset($_GET['edit_product_details'])) {
    die("Product ID is missing from the URL.");
}

$product_id = intval($_GET['edit_product_details']);

// Fetch product details using mysqli_query()
$query = "SELECT * FROM products WHERE pro_id = $product_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
} else {
    die("Product not found.");
}

// Fetch Parent Categories
$sql = "SELECT * FROM `categories` WHERE `status` = 1 ORDER BY id DESC";
$check = mysqli_query($conn, $sql);

// Fetch Subcategories for the selected parent category
$parent_cate_id = $product['pro_cate'];
$sub_cate_query = "SELECT * FROM `sub_categories` WHERE cate_id = '$parent_cate_id' AND status = 1";
$sub_categories = mysqli_query($conn,$sub_cate_query);
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin - Edit Product</title>
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

        <div class="main_content_iner ">
            <div class="container-fluid p-0 sm_padding_15px">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="m-0">Update Product Details</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body">
                                <br />
                                <div class="card-body">
                                    <form action="update-product.php" method="POST" enctype="multipart/form-data">
                                        
                                        <input type="hidden" name="pro_id" value="<?= $product['pro_id'] ?>" />
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>" />

                                        <div class="row mb-3">
                                            <!-- Product Name -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="pro_name">Product Name</label>
                                                <input type="text" class="form-control" name="pro_name" id="pro_name" value="<?= htmlspecialchars($product['pro_name']) ?>" required />
                                            </div>

                                            <!-- NEW: Custom Slug Field -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="slug_url">Custom Slug (SEO URL)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" style="background-color: #f1f3f5;">site.com/</span>
                                                    <input type="text" class="form-control" name="slug_url" id="slug_url" value="<?= htmlspecialchars($product['slug_url'] ?? '') ?>" required />
                                                </div>
                                            </div>

                                            <!-- Brand Name -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="brand_name">Brand Name</label>
                                                <input type="text" class="form-control" name="brand_name" id="brand_name" value="<?= htmlspecialchars($product['brand_name'] ?? '') ?>" />
                                            </div>

                                            <!-- Parent Category -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="pro_cate">Parent Category</label>
                                                <select class="form-control" name="pro_cate" id="pro_cate" required onchange="get_subcategory(this.value)">
                                                    <option value="">--Select Category--</option>
                                                    <?php foreach ($check as$val) { ?>
                                                        <option value="<?= $val['cate_id'] ?>" <?= ($product['pro_cate'] ==$val['cate_id']) ? 'selected' : '' ?>>
                                                            <?= ucwords(htmlspecialchars($val['categories'])) ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <!-- Sub Category -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="pro_sub_cate">Sub Category</label>
                                                <select class="form-control" name="pro_sub_cate" id="subcate_id">
                                                    <option value="0">--Select Subcategory--</option>
                                                    <?php 
                                                    if ($sub_categories && mysqli_num_rows($sub_categories) > 0) {
                                                        while ($sub_cate = mysqli_fetch_assoc($sub_categories)) {$selected = ($product['pro_sub_cate'] ==$sub_cate['id']) ? 'selected' : '';
                                                    ?>
                                                            <option value="<?= $sub_cate['id'] ?>" <?= $selected ?>>
                                                                <?= ucwords(htmlspecialchars($sub_cate['categories'])) ?>
                                                            </option>
                                                    <?php 
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- Stock -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="stock">Stock Quantity</label>
                                                <input type="number" class="form-control" name="stock" id="stock" value="<?= $product['stock'] ?>" required />
                                            </div>

                                            <!-- Product Image -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="pro_img">Product Image</label>
                                                <input type="file" class="form-control" name="pro_img" id="pro_img" accept="image/*" />
                                                <input type="hidden" name="old_img" value="<?= $product['pro_img'] ?>">
                                                <?php if(!empty($product['pro_img'])): ?>
                                                <div class="mt-2">
                                                    <small class="text-muted">Current Image:</small>
                                                    <div class="mt-1">
                                                        <img src="assets/img/uploads/<?= trim($product['pro_img']) ?>" style="height: 100px; width: 100px; object-fit: contain; border-radius: 8px; border: 1px solid #ccc;" alt="Current Product Image">
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Exclusive Deal -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="new_arrival">Exclusive Deal</label>
                                                <select id="new_arrival" name="new_arrival" class="form-control">
                                                    <option value="0" <?= $product['new_arrival'] == 0 ? 'selected' : '' ?>>No</option>
                                                    <option value="1" <?= $product['new_arrival'] == 1 ? 'selected' : '' ?>>Yes</option>
                                                </select>
                                            </div>

                                            <!-- Special Offers -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="trending">Special Offers</label>
                                                <select id="trending" name="trending" class="form-control">
                                                    <option value="0" <?= $product['trending'] == 0 ? 'selected' : '' ?>>No</option>
                                                    <option value="1" <?= $product['trending'] == 1 ? 'selected' : '' ?>>Yes</option>
                                                </select>
                                            </div>

                                            <!-- Short Description -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label" for="short_desc">Short Description</label>
                                                <textarea class="form-control" name="short_desc" id="short_desc" required><?= $product['short_desc'] ?></textarea>
                                            </div>

                                            <!-- Long Description -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label" for="pro_desc">Long Description</label>
                                                <textarea class="form-control" name="pro_desc" id="pro_desc" required><?= $product['description'] ?></textarea>
                                            </div>

                                            <!-- MRP -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label" for="mrp">MRP</label>
                                                <input type="text" class="form-control" name="mrp" id="mrp" value="<?= $product['mrp'] ?>" required />
                                            </div>

                                            <!-- Selling Price -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label" for="selling_price">Selling Price</label>
                                                <input type="text" class="form-control" name="selling_price" id="selling_price" value="<?= $product['selling_price'] ?>" required />
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label" for="status">Status</label>
                                                <select id="status" name="status" class="form-control" required>
                                                    <option value="1" <?= $product['status'] == 1 ? 'selected' : '' ?>>Active</option>
                                                    <option value="0" <?= $product['status'] == 0 ? 'selected' : '' ?>>Deactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- SEO Section -->
                                        <h5 class="mt-4 mb-3 border-bottom pb-2">SEO Details</h5>
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="meta_title">Meta Title</label>
                                                <input type="text" class="form-control" name="meta_title" id="meta_title" value="<?= htmlspecialchars($product['meta_title'] ?? '') ?>" />
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="meta_key">Meta Keywords</label>
                                                <input type="text" class="form-control" name="meta_key" id="meta_key" value="<?= htmlspecialchars($product['meta_key'] ?? '') ?>" />
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label" for="meta_desc">Meta Description</label>
                                                <input type="text" class="form-control" name="meta_desc" id="meta_desc" value="<?= htmlspecialchars($product['meta_desc'] ?? '') ?>" />
                                            </div>
                                            
                                            <!-- Schema Markup Box -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label fw-bold" for="schema_markup">SEO Schema Markup (JSON-LD)</label>
                                                <textarea class="form-control" name="schema_markup" id="schema_markup" rows="6"><?= htmlspecialchars($product['schema_markup'] ?? '') ?></textarea>
                                                <small class="text-muted">SEO expert yahan product ka custom schema (jaise Product, Price, Rating) paste kar sakte hain.</small>
                                            </div>
                                        </div>

                                        <button type="submit" name="update-product" class="btn btn-primary px-5 py-2">
                                            <i class="fas fa-save me-2"></i> Update Product
                                        </button>
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
            if (document.getElementById('pro_desc')) {
                CKEDITOR.replace('pro_desc');
            }
            if (document.getElementById('short_desc')) {
                CKEDITOR.replace('short_desc');
            }

            // Slug Auto-Formatting
            function convertToSlug(text) {
                return text.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
            }
            
            document.getElementById('slug_url').addEventListener('blur', function() {
                this.value = convertToSlug(this.value);
            });
        </script>

        <script type="text/javascript">
            function get_subcategory(cate_id) {
                if (cate_id === '') {
                    $("#subcate_id").html('<option value="0">--Select Subcategory--</option>');
                    return;
                }
                
                $.ajax({
                    url: 'functions.php',
                    method: 'POST',
                    data: { 
                        action: 'get_subcategories', 
                        cate_id: cate_id 
                    },
                    success: function (data) {
                        $("#subcate_id").html(data);
                    },
                    error: function () {
                        alert("Network error: Could not fetch subcategories.");
                    }
                });
            }
        </script>
    </section>
</body>
</html>