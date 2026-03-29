<?php
require("includes/common.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit();
}

if (isset($_GET['cart_id']) && isset($_GET['qty'])) {
    $cart_id = mysqli_real_escape_string($con, $_GET['cart_id']);
    $new_quantity = (int)$_GET['qty'];
    $user_id = $_SESSION['user_id'];
    
    if ($new_quantity < 1) {
        header('location: cart.php?error=invalid_quantity');
        exit();
    }
    
    $cart_query = "SELECT item_id FROM users_products WHERE id='$cart_id' AND user_id='$user_id' AND status='Added to cart'";
    $cart_result = mysqli_query($con, $cart_query);
    
    if (mysqli_num_rows($cart_result) == 0) {
        header('location: cart.php?error=item_not_found');
        exit();
    }
    
    $cart_row = mysqli_fetch_assoc($cart_result);
    $product_id = $cart_row['item_id'];
    
    if (!validate_stock($product_id, $new_quantity)) {
        $available = get_product_stock($product_id);
        header('location: cart.php?error=insufficient_stock&available=' . $available);
        exit();
    }
    
    $update_query = "UPDATE users_products SET quantity = $new_quantity WHERE id='$cart_id' AND user_id='$user_id'";
    
    if (mysqli_query($con, $update_query)) {
        header('location: cart.php?success=quantity_updated');
    } else {
        header('location: cart.php?error=update_failed');
    }
    exit();
} else {
    header('location: cart.php');
    exit();
}
?>
