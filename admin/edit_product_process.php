<?php
session_start();
require 'includes/admin_common.php';
check_admin_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = trim($_POST['product_name']);
    $category = $_POST['category'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $stock_quantity = $_POST['stock_quantity'];
    $unit = $_POST['unit'];
    
    if (empty($product_id) || empty($product_name) || empty($category) || empty($unit)) {
        header('Location: edit_product.php?id=' . $product_id . '&error=Please fill in all required fields');
        exit();
    }
    
    if ($price === '' || $stock_quantity === '') {
        header('Location: edit_product.php?id=' . $product_id . '&error=Please fill in all required fields');
        exit();
    }
    
    if (!is_numeric($price) || $price < 0) {
        header('Location: edit_product.php?id=' . $product_id . '&error=Invalid price');
        exit();
    }
    
    if (!is_numeric($discount) || $discount < 0 || $discount > 100) {
        header('Location: edit_product.php?id=' . $product_id . '&error=Discount must be between 0 and 100');
        exit();
    }
    
    if (!is_numeric($stock_quantity) || $stock_quantity < 0) {
        header('Location: edit_product.php?id=' . $product_id . '&error=Invalid stock quantity');
        exit();
    }
    
    $image_path = null;
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
        $upload_result = upload_product_image($_FILES['product_image']);
        
        if (!$upload_result['success']) {
            header('Location: edit_product.php?id=' . $product_id . '&error=' . urlencode($upload_result['error']));
            exit();
        }
        
        $image_path = $upload_result['path'];
    }
    
    if (update_product($product_id, $product_name, $category, $price, $discount, $stock_quantity, $unit, $image_path)) {
        header('Location: products.php?success=Product updated successfully');
        exit();
    } else {
        header('Location: edit_product.php?id=' . $product_id . '&error=Failed to update product');
        exit();
    }
} else {
    header('Location: products.php');
    exit();
}
?>
