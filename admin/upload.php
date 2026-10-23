<?php
include "db-conn.php";

if (!isset($_POST['product_id'])) {
    die("Invalid Product ID.");
}

$product_id = intval($_POST['product_id']);

if (isset($_FILES['productImages1']) && !empty($_FILES['productImages1']['name'][0])) {
    $upload_dir = "assets/img/uploads/";

    foreach ($_FILES['productImages1']['name'] as $key => $file_name) {
        $file_tmp = $_FILES['productImages1']['tmp_name'][$key];
        $file_size = $_FILES['productImages1']['size'][$key];
        $file_error = $_FILES['productImages1']['error'][$key];

        if ($file_error === 0) {
            $new_file_name = time() . "_" . basename($file_name);
            $upload_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {

                $stmt = $conn->prepare("UPDATE products SET pro_img = ? WHERE pro_id = ?");

                $stmt->bind_param("si", $new_file_name, $product_id);
                $stmt->execute();

                break;
            } else {
                echo "Failed to upload $file_name.<br>";
            }
        } else {
            echo "Error uploading $file_name.<br>";
        }
    }

    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        echo "No previous page found!";
    }
    exit;
} else {
    die("No files were uploaded.");
}
