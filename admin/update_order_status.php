<?php
session_start();
require 'includes/admin_common.php';
require 'includes/order_functions.php';
check_admin_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    
    if (empty($order_id) || empty($order_status)) {
        header('Location: orders.php?error=Invalid request');
        exit();
    }
    
    $allowed_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    if (!in_array($order_status, $allowed_statuses)) {
        header('Location: order_details.php?id=' . $order_id . '&error=Invalid status');
        exit();
    }
    
    if (update_order_status($order_id, $order_status)) {
        header('Location: order_details.php?id=' . $order_id . '&success=Order status updated successfully');
        exit();
    } else {
        header('Location: order_details.php?id=' . $order_id . '&error=Failed to update order status');
        exit();
    }
} else {
    header('Location: orders.php');
    exit();
}
?>
