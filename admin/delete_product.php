<?php
session_start();
require 'includes/admin_common.php';
check_admin_login();

if (!isset($_GET['id'])) {
    header('Location: products.php?error=Invalid product ID');
    exit();
}

$product_id = $_GET['id'];
$product = get_product_by_id($product_id);

if (!$product) {
    header('Location: products.php?error=Product not found');
    exit();
}

if (delete_product($product_id)) {
    if ($product['image_path'] && file_exists('../' . $product['image_path'])) {
        @unlink('../' . $product['image_path']);
    }
    header('Location: products.php?success=Product deleted successfully');
    exit();
} else {
    header('Location: products.php?error=Failed to delete product');
    exit();
}
?>
