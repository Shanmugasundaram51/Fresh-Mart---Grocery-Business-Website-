<?php
require("includes/common.php");
session_start();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $item_id = mysqli_real_escape_string($con, $_GET['id']);
    $user_id = $_SESSION['user_id'];
    $quantity = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    
    if ($quantity < 1) {
        header('location: products.php?error=invalid_quantity');
        exit();
    }
    
    $current_cart_qty = get_cart_item_quantity($user_id, $item_id);
    $total_requested = $current_cart_qty + $quantity;
    
    if (!validate_stock($item_id, $total_requested)) {
        $available = get_product_stock($item_id);
        if ($available == 0) {
            header('location: products.php?error=out_of_stock');
        } else {
            header('location: products.php?error=insufficient_stock&available=' . $available . '&in_cart=' . $current_cart_qty);
        }
        exit();
    }
    
    $check_query = "SELECT id FROM users_products WHERE user_id='$user_id' AND item_id='$item_id' AND status='Added to cart' LIMIT 1";
    $check_result = mysqli_query($con, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $query = "UPDATE users_products SET quantity = quantity + $quantity WHERE user_id='$user_id' AND item_id='$item_id' AND status='Added to cart'";
    } else {
        $query = "INSERT INTO users_products(user_id, item_id, status, quantity) VALUES('$user_id', '$item_id', 'Added to cart', $quantity)";
    }
    
    if (mysqli_query($con, $query)) {
        header('location: products.php?success=added');
    } else {
        header('location: products.php?error=failed');
    }
    exit();
}
?>   