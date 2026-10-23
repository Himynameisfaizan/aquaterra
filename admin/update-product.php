<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db-conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-product'])) {
    // Get all POST data
    $pro_id = intval($_POST['pro_id']);
    $pro_name = mysqli_real_escape_string($conn, $_POST['pro_name'] ?? '');
    $brand_name = mysqli_real_escape_string($conn, $_POST['brand_name'] ?? '');
    $pro_cate = intval($_POST['pro_cate'] ?? 0);
    $pro_sub_cate = intval($_POST['pro_sub_cate'] ?? 0);
    $short_desc = mysqli_real_escape_string($conn, $_POST['short_desc'] ?? '');
    $description = mysqli_real_escape_string($conn, $_POST['pro_desc'] ?? '');
    $new_arrival = intval($_POST['new_arrival'] ?? 0);
    $trending = intval($_POST['trending'] ?? 0);
    $qty = intval($_POST['qty'] ?? 0);
    $mrp = floatval($_POST['mrp'] ?? 0);
    $selling_price = floatval($_POST['selling_price'] ?? 0);
    $whole_sale_selling_price = floatval($_POST['whole_sale_selling_price'] ?? 0);
    $stock = mysqli_real_escape_string($conn, $_POST['stock'] ?? '');
    $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title'] ?? '');
    $meta_desc = mysqli_real_escape_string($conn, $_POST['meta_desc'] ?? '');
    $meta_key = mysqli_real_escape_string($conn, $_POST['meta_key'] ?? '');
    
    // ---> Naya Schema Variable Yahan Catch Kiya Hai <---
    $schema_markup = mysqli_real_escape_string($conn, trim($_POST['schema_markup'] ?? ''));
    
    $status = intval($_POST['status'] ?? 0);
    $slug_url = mysqli_real_escape_string($conn, $_POST['slug_url'] ?? '');

    // Handle images
    $current_images = [];
    $query = "SELECT pro_img FROM products WHERE pro_id = $pro_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $current_images = !empty($row['pro_img']) ? explode(',', $row['pro_img']) : [];
    }

    // Remove images marked for deletion
    $removed_images = isset($_POST['removed_images']) ? explode(',', $_POST['removed_images']) : [];
    foreach ($removed_images as $removed) {
        $removed = trim($removed);
        if (!empty($removed) && ($key = array_search($removed, $current_images)) !== false) {
            unset($current_images[$key]);
            // Optional: Delete the physical file
            $file_path = "assets/img/uploads/" . $removed;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    // Handle new file uploads
    $new_images = [];
    if (!empty($_FILES['pro_img']['name'][0])) {
        $upload_dir = "assets/img/uploads/";

        foreach ($_FILES['pro_img']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['pro_img']['name'][$key];
            $file_size = $_FILES['pro_img']['size'][$key];
            $file_error = $_FILES['pro_img']['error'][$key];

            if ($file_error === 0) {
                // Generate unique filename
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
                $destination = $upload_dir . $new_file_name;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $new_images[] = $new_file_name;
                }
            }
        }
    }

    // Merge old and new images
    $all_images = array_merge($current_images, $new_images);
    $pro_img = !empty($all_images) ? implode(',', $all_images) : '';

    // Update query (Schema Column add kar diya gaya hai)
    $query = "UPDATE products SET 
                pro_name = '$pro_name',
                brand_name = '$brand_name',
                pro_cate = '$pro_cate',
                pro_sub_cate = '$pro_sub_cate',
                short_desc = '$short_desc',
                description = '$description',
                new_arrival = '$new_arrival',
                trending = '$trending',
                qty = '$qty',
                mrp = '$mrp',
                selling_price = '$selling_price',
                whole_sale_selling_price = '$whole_sale_selling_price',
                stock = '$stock',
                pro_img = '$pro_img',
                meta_title = '$meta_title',
                meta_desc = '$meta_desc',
                meta_key = '$meta_key',
                schema_markup = '$schema_markup',
                status = '$status',
                slug_url = '$slug_url'
              WHERE pro_id = '$pro_id'";

    if (mysqli_query($conn, $query)) {
        // Success
        header("Location: edit_products.php?edit_product_details=$pro_id&success=1");
        exit();
    } else {
        // Error
        echo "Error: " . mysqli_error($conn);
        // You might want to redirect with an error message
        header("Location: edit_products.php?edit_product_details=$pro_id&error=1");
        exit();
    }
} else {
    header("Location: show-products.php");
    exit();
}
?>