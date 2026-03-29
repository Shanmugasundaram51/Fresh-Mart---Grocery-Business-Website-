<?php
session_start();
require 'includes/admin_common.php';
check_admin_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = trim($_POST['product_name']);
    $category = $_POST['category'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $stock_quantity = $_POST['stock_quantity'];
    $unit = $_POST['unit'];
    
    if (empty($product_name) || empty($category) || empty($unit)) {
        header('Location: add_product.php?error=Please fill in all required fields');
        exit();
    }
    
    if ($price === '' || $stock_quantity === '') {
        header('Location: add_product.php?error=Please fill in all required fields');
        exit();
    }
    
    if (!is_numeric($price) || $price < 0) {
        header('Location: add_product.php?error=Invalid price');
        exit();
    }
    
    if (!is_numeric($discount) || $discount < 0 || $discount > 100) {
        header('Location: add_product.php?error=Discount must be between 0 and 100');
        exit();
    }
    
    if (!is_numeric($stock_quantity) || $stock_quantity < 0) {
        header('Location: add_product.php?error=Invalid stock quantity');
        exit();
    }
    
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] == UPLOAD_ERR_NO_FILE) {
        header('Location: add_product.php?error=Please upload a product image');
        exit();
    }
    
    if ($_FILES['product_image']['error'] != UPLOAD_ERR_OK) {
        header('Location: add_product.php?error=Error uploading image');
        exit();
    }
    
    $upload_result = upload_product_image($_FILES['product_image']);
    
    if (!$upload_result['success']) {
        header('Location: add_product.php?error=' . urlencode($upload_result['error']));
        exit();
    }
    
    $image_path = $upload_result['path'];
    
    if (add_product($product_name, $category, $price, $discount, $stock_quantity, $unit, $image_path)) {
        header('Location: products.php?success=Product added successfully');
        exit();
    } else {
        header('Location: add_product.php?error=Failed to add product to database');
        exit();
    }
} else {
    header('Location: add_product.php');
    exit();
}
?>
