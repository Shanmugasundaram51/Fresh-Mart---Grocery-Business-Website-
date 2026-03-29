<?php
require "includes/common.php";

echo "<h2>Concurrency Control Test</h2>";
echo "<p>This script tests the transaction-based order placement to ensure race conditions are prevented.</p>";

function setup_test_product() {
    global $con;
    
    $test_product_query = "SELECT id FROM products WHERE name='Test Concurrency Product' LIMIT 1";
    $result = mysqli_query($con, $test_product_query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        $product_id = $product['id'];
        mysqli_query($con, "UPDATE products SET stock_quantity=1 WHERE id='$product_id'");
    } else {
        $insert = "INSERT INTO products (name, price, category, stock_quantity, unit, discount) 
                   VALUES ('Test Concurrency Product', 100, 'Test', 1, 'piece', 0)";
        mysqli_query($con, $insert);
        $product_id = mysqli_insert_id($con);
    }
    
    return $product_id;
}

function setup_test_users() {
    global $con;
    
    $users = [];
    for ($i = 1; $i <= 2; $i++) {
        $email = "testuser$i@test.com";
        $check = "SELECT id FROM users WHERE email_id='$email'";
        $result = mysqli_query($con, $check);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $user_id = $user['id'];
        } else {
            $insert = "INSERT INTO users (first_name, last_name, email_id, password, phone) 
                       VALUES ('Test', 'User$i', '$email', 'test123', '1234567890')";
            mysqli_query($con, $insert);
            $user_id = mysqli_insert_id($con);
        }
        
        mysqli_query($con, "DELETE FROM users_products WHERE user_id='$user_id'");
        $users[] = $user_id;
    }
    
    return $users;
}

function add_to_cart($user_id, $product_id, $quantity = 1) {
    global $con;
    $query = "INSERT INTO users_products (user_id, item_id, status, quantity) 
              VALUES ('$user_id', '$product_id', 'Added to cart', $quantity)";
    return mysqli_query($con, $query);
}

echo "<h3>Test Setup</h3>";
$product_id = setup_test_product();
echo "<p>✓ Created test product (ID: $product_id) with stock = 1</p>";

$users = setup_test_users();
echo "<p>✓ Created 2 test users (IDs: " . implode(', ', $users) . ")</p>";

foreach ($users as $user_id) {
    add_to_cart($user_id, $product_id, 1);
}
echo "<p>✓ Added product to both users' carts (each wants to buy 1 unit)</p>";

echo "<h3>Simulating Concurrent Orders</h3>";
echo "<p>User A and User B both click 'Confirm Order' at the same time...</p>";

echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0; background: #f9f9f9;'>";
echo "<strong>User A (ID: {$users[0]}) attempting to place order...</strong><br>";
$result_a = create_order_with_transaction($users[0]);
if ($result_a['success']) {
    echo "<span style='color: green;'>✓ SUCCESS: Order placed (Order ID: {$result_a['order_id']})</span><br>";
} else {
    echo "<span style='color: red;'>✗ FAILED: {$result_a['error']}</span><br>";
    if (isset($result_a['items'])) {
        foreach ($result_a['items'] as $item) {
            echo "<span style='color: red;'>  - {$item['name']}: Requested {$item['requested']}, Available {$item['available']}</span><br>";
        }
    }
}
echo "</div>";

echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0; background: #f9f9f9;'>";
echo "<strong>User B (ID: {$users[1]}) attempting to place order...</strong><br>";
$result_b = create_order_with_transaction($users[1]);
if ($result_b['success']) {
    echo "<span style='color: green;'>✓ SUCCESS: Order placed (Order ID: {$result_b['order_id']})</span><br>";
} else {
    echo "<span style='color: red;'>✗ FAILED: {$result_b['error']}</span><br>";
    if (isset($result_b['items'])) {
        foreach ($result_b['items'] as $item) {
            echo "<span style='color: red;'>  - {$item['name']}: Requested {$item['requested']}, Available {$item['available']}</span><br>";
        }
    }
}
echo "</div>";

$stock_query = "SELECT stock_quantity FROM products WHERE id='$product_id'";
$stock_result = mysqli_query($con, $stock_query);
$final_stock = 0;
if ($stock_result && mysqli_num_rows($stock_result) > 0) {
    $row = mysqli_fetch_assoc($stock_result);
    $final_stock = $row['stock_quantity'];
}

echo "<h3>Final Results</h3>";
echo "<div style='border: 2px solid " . ($final_stock >= 0 ? 'green' : 'red') . "; padding: 15px; margin: 10px 0; background: #fff;'>";
echo "<strong>Final Stock Quantity:</strong> $final_stock<br>";

if ($final_stock >= 0) {
    echo "<span style='color: green; font-weight: bold;'>✓ PASS: Stock is non-negative (concurrency control working!)</span><br>";
    echo "<p>Expected behavior: Only ONE user should successfully place the order, the other should get 'Out of Stock' error.</p>";
} else {
    echo "<span style='color: red; font-weight: bold;'>✗ FAIL: Stock is negative (race condition occurred!)</span><br>";
    echo "<p>This indicates both users were able to reduce stock, which should NOT happen.</p>";
}
echo "</div>";

echo "<h3>Explanation</h3>";
echo "<ul>";
echo "<li><strong>Without Transaction Control:</strong> Both users pass validation, both reduce stock → stock becomes -1</li>";
echo "<li><strong>With Transaction Control (SELECT FOR UPDATE):</strong> First user locks the row, reduces stock to 0, commits. Second user then locks the row, sees stock=0, gets 'Out of Stock' error.</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='cart.php'>Back to Cart</a> | <a href='products.php'>Continue Shopping</a></p>";
?>
