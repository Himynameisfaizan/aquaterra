<?php
include "db-conn.php";

$sql = "SELECT * FROM `categories` ORDER BY id DESC";
$check = mysqli_query($conn,$sql);
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Sales - Add Product</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
</head>

<body class="crm_body_bg">

    <?php include "header.php"; ?>
    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0 ">
                    <!-- Navigation / Header Icons hidden for brevity, same as your original file -->
                    <?php include "top_nav.php"; ?>
                </div>
            </div>
        </div>

        <div class="main_content_iner ">
            <div class="container-fluid p-0 sm_padding_15px">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="main_content_iner">
                            <div class="container-fluid p-0 sm_padding_15px">
                                <div class="row justify-content-center">

                                    <div class="col-lg-12">
                                        <div class="white_card card_height_100 mb_30">
                                            <div class="white_card_header">
                                                <div class="box_header m-0">
                                                    <div class="main-title">
                                                        <h3 class="m-0">Fill the Product details</h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="white_card_body">
                                                <div class="card-body">
                                                    <form id="myForm" action="functions.php" method="post" enctype="multipart/form-data">
                                                        <div class="row mb-3">
                                                            
                                                            <!-- Product Name -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="pro_name">Product Name</label>
                                                                <input type="text" class="form-control" name="pro_name" id="pro_name" placeholder="Product name" required />
                                                            </div>

                                                            <!-- NEW: Custom Slug Field -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="slug_url">Custom Slug (SEO URL)</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text" style="background-color: #f1f3f5;">site.com/</span>
                                                                    <input type="text" class="form-control" name="slug_url" id="slug_url" placeholder="e.g. customized-url-structure" required />
                                                                </div>
                                                            </div>

                                                            <!-- Brand Name -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="brand_name">Brand Name</label>
                                                                <input type="text" class="form-control" name="brand_name" id="brand_name" placeholder="Brand name" required />
                                                            </div>

                                                            <!-- Parent Category Name -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="pro_cate">Parent Category Name</label>
                                                                <select class="form-control" name="pro_cate" id="pro_cate" required onchange="get_subcategory(this.value)">
                                                                    <option value="">--select--</option>
                                                                    <?php foreach ($check as$val) { ?>
                                                                        <option value="<?= $val['cate_id'] ?>"><?= ucwords(htmlspecialchars($val['categories'])) ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>

                                                            <!-- Sub Category -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="pro_sub_cate">Sub Category</label>
                                                                <select class="form-control" name="pro_sub_cate" id="subcate_id">
                                                                   <option value="">Select</option>
                                                                </select>
                                                            </div>

                                                            <!-- Stock -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="stock">Stock</label>
                                                                <input type="text" class="form-control" name="stock" id="stock" placeholder="Stock" required />
                                                            </div>

                                                            <!-- Product Image -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="pro_img">Product Image</label>
                                                                <input type="file" class="form-control" name="pro_img[]" id="pro_img" multiple required />
                                                            </div>
                                                            
                                                            <!-- Exclusive Deal -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="new_arrival">Exclusive Deal & Offers</label>
                                                                <select id="new_arrival" name="new_arrival" class="form-control" required>
                                                                    <option value="0" selected>No</option>
                                                                    <option value="1">Yes</option>
                                                                </select>
                                                            </div>

                                                            <!-- Special Offers -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="trending">Special Offers</label>
                                                                <select id="trending" name="trending" class="form-control" required>
                                                                    <option value="0" selected>No</option>
                                                                    <option value="1">Yes</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <!-- Short Description -->
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label" for="short_desc">Short Description</label>
                                                                <textarea class="form-control" name="short_desc" required ></textarea>
                                                            </div>
                                                            
                                                            <!-- Product Description -->
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label" for="pro_desc">Product Description</label>
                                                                <textarea class="form-control" name="pro_desc" required ></textarea>
                                                            </div>

                                                            <!-- MRP -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="mrp">MRP</label>
                                                                <input type="text" class="form-control" name="mrp" id="mrp" placeholder="MRP" />
                                                            </div>
                                                            
                                                            <!-- Selling Price -->
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="selling_price">Selling Price</label>
                                                                <input type="text" class="form-control" name="selling_price" id="selling_price" placeholder="Selling Price" />
                                                            </div>
                                                        </div>

                                                        <!-- SEO SECTION -->
                                                        <div class="row mb-3">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="meta_title">Meta Title</label>
                                                                <input type="text" class="form-control" name="meta_title" id="meta_title" placeholder="Meta Title"  />
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label" for="meta_key">Meta Keyword</label>
                                                                <input type="text" class="form-control" name="meta_key" id="meta_key" placeholder="Meta Keyword"  />
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label" for="meta_desc">Meta Description</label>
                                                                <input type="text" class="form-control" name="meta_desc" id="meta_desc" placeholder="Meta Description" />
                                                            </div>
                                                            
                                                            <!-- Schema Markup Box -->
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label fw-bold" for="schema_markup">SEO Schema Markup (JSON-LD)</label>
                                                                <textarea class="form-control" name="schema_markup" id="schema_markup" rows="6" placeholder="Paste full <script type='application/ld+json'>...</script> here..."></textarea>
                                                                <small class="text-muted">SEO expert yahan product ka custom schema (jaise Product, Price, Rating) paste kar sakte hain.</small>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label" for="status">Status</label>
                                                                <select id="status" name="status" class="form-control" required>
                                                                    <option value="1">Active</option>
                                                                    <option value="0">Deactive</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <button type="submit" class="btn btn-primary" name="add-product">
                                                            Add Product
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "footer.php"; ?>

        <script>
            const form = document.getElementById('myForm');

            form.addEventListener('submit', function(event) {
                const select = document.getElementById('pro_cate');
                if (!select.value) {
                    alert('Please select a valid category.');
                    event.preventDefault(); 
                }
            });
        </script>
        
        <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('pro_desc')
            CKEDITOR.replace('short_desc')

            // Auto Generate Slug from Product Name
            function convertToSlug(text) {
                return text.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
            }
            
            document.getElementById('pro_name').addEventListener('input', function() {
                document.getElementById('slug_url').value = convertToSlug(this.value);
            });
            
            document.getElementById('slug_url').addEventListener('blur', function() {
                this.value = convertToSlug(this.value);
            });
        </script>

        <script type="text/javascript">
            function get_subcategory(cate_id){
                $.ajax({
                    url:'functions.php',
                    method:'post',
                    data: {cate_id:cate_id},
                    error:function(){
                        alert("something went wrong");
                    },
                    success:function(data){
                        $("#subcate_id").html(data);
                    }
                })
            }
        </script>
    </section>
</body>
</html>