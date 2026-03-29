<?php
require "includes/common.php";
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = mysqli_real_escape_string($con, $_GET['order_id']);

$order_check = "SELECT id, user_id, total_amount, discount_amount, final_amount 
                FROM orders 
                WHERE id='$order_id' AND user_id='$user_id'";
$order_result = mysqli_query($con, $order_check);

if (!$order_result || mysqli_num_rows($order_result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order = mysqli_fetch_assoc($order_result);

$items_query = "SELECT * FROM order_items WHERE order_id='$order_id' ORDER BY id";
$items_result = mysqli_query($con, $items_query);

$items = [];
if ($items_result && mysqli_num_rows($items_result) > 0) {
    while ($item = mysqli_fetch_assoc($items_result)) {
        $items[] = $item;
    }
}

echo json_encode([
    'success' => true,
    'order' => $order,
    'items' => $items
]);
?>
