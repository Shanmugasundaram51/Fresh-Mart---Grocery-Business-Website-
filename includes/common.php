<?php
if (!defined('COMMON_INCLUDED')) {
    define('COMMON_INCLUDED', true);
    
    $con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }
} else {
    global $con;
}

if (!function_exists('get_product_stock')) {
function get_product_stock($product_id) {
    global $con;
    $product_id = mysqli_real_escape_string($con, $product_id);
    $query = "SELECT stock_quantity FROM products WHERE id='$product_id'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return (int)$row['stock_quantity'];
    }
    return 0;
}

function get_product_details($product_id) {
    global $con;
    $product_id = mysqli_real_escape_string($con, $product_id);
    $query = "SELECT * FROM products WHERE id='$product_id'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

function validate_stock($product_id, $requested_quantity = 1) {
    $available_stock = get_product_stock($product_id);
    return $available_stock >= $requested_quantity;
}

function get_cart_item_quantity($user_id, $product_id) {
    global $con;
    $user_id = mysqli_real_escape_string($con, $user_id);
    $product_id = mysqli_real_escape_string($con, $product_id);
    $query = "SELECT SUM(quantity) as total FROM users_products WHERE user_id='$user_id' AND item_id='$product_id' AND status='Added To Cart'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }
    return 0;
}

function reduce_stock($product_id, $quantity) {
    global $con;
    $product_id = mysqli_real_escape_string($con, $product_id);
    $quantity = (int)$quantity;
    $query = "UPDATE products SET stock_quantity = stock_quantity - $quantity WHERE id='$product_id' AND stock_quantity >= $quantity";
    return mysqli_query($con, $query);
}

function is_stock_low($stock_quantity) {
    return $stock_quantity > 0 && $stock_quantity < 5;
}

function is_out_of_stock($stock_quantity) {
    return $stock_quantity <= 0;
}

function create_order($user_id, $delivery_address = null, $delivery_phone = null, $payment_method = 'Cash on Delivery') {
    global $con;
    $user_id = mysqli_real_escape_string($con, $user_id);
    $payment_method = mysqli_real_escape_string($con, $payment_method);
    
    $order_number = 'ORD-' . str_pad($user_id, 4, '0', STR_PAD_LEFT) . '-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    $cart_query = "SELECT up.item_id, up.quantity, p.name, p.price, p.unit, p.discount 
                   FROM users_products up 
                   INNER JOIN products p ON up.item_id = p.id 
                   WHERE up.user_id='$user_id' AND up.status='Added to cart'";
    $cart_result = mysqli_query($con, $cart_query);
    
    if (!$cart_result || mysqli_num_rows($cart_result) == 0) {
        return false;
    }
    
    $total_amount = 0;
    $discount_amount = 0;
    $items = [];
    
    while ($item = mysqli_fetch_assoc($cart_result)) {
        $subtotal = $item['price'] * $item['quantity'];
        $item_discount = ($item['price'] * $item['discount'] / 100) * $item['quantity'];
        $final_price = $subtotal - $item_discount;
        
        $total_amount += $subtotal;
        $discount_amount += $item_discount;
        
        $items[] = [
            'product_id' => $item['item_id'],
            'product_name' => $item['name'],
            'product_price' => $item['price'],
            'product_unit' => $item['unit'],
            'discount_percent' => $item['discount'],
            'quantity' => $item['quantity'],
            'subtotal' => $subtotal,
            'discount_amount' => $item_discount,
            'final_price' => $final_price
        ];
    }
    
    $final_amount = $total_amount - $discount_amount;
    
    $delivery_address = $delivery_address ? mysqli_real_escape_string($con, $delivery_address) : 'NULL';
    $delivery_phone = $delivery_phone ? mysqli_real_escape_string($con, $delivery_phone) : 'NULL';
    
    $address_value = $delivery_address !== 'NULL' ? "'$delivery_address'" : 'NULL';
    $phone_value = $delivery_phone !== 'NULL' ? "'$delivery_phone'" : 'NULL';
    
    $order_query = "INSERT INTO orders (user_id, order_number, total_amount, discount_amount, final_amount, payment_status, payment_method, delivery_address, delivery_phone) 
                    VALUES ('$user_id', '$order_number', $total_amount, $discount_amount, $final_amount, 'Paid', '$payment_method', $address_value, $phone_value)";
    
    if (!mysqli_query($con, $order_query)) {
        return false;
    }
    
    $order_id = mysqli_insert_id($con);
    
    foreach ($items as $item) {
        $product_id = $item['product_id'];
        $product_name = mysqli_real_escape_string($con, $item['product_name']);
        $product_price = $item['product_price'];
        $product_unit = mysqli_real_escape_string($con, $item['product_unit']);
        $discount_percent = $item['discount_percent'];
        $quantity = $item['quantity'];
        $subtotal = $item['subtotal'];
        $item_discount_amount = $item['discount_amount'];
        $final_price = $item['final_price'];
        
        $item_query = "INSERT INTO order_items (order_id, product_id, product_name, product_price, product_unit, discount_percent, quantity, subtotal, discount_amount, final_price) 
                       VALUES ($order_id, $product_id, '$product_name', $product_price, '$product_unit', $discount_percent, $quantity, $subtotal, $item_discount_amount, $final_price)";
        
        if (!mysqli_query($con, $item_query)) {
            return false;
        }
    }
    
    return $order_id;
}

function create_order_with_transaction($user_id, $delivery_address = null, $delivery_phone = null, $payment_method = 'Cash on Delivery') {
    global $con;
    $user_id = mysqli_real_escape_string($con, $user_id);
    $payment_method = mysqli_real_escape_string($con, $payment_method);
    
    mysqli_autocommit($con, false);
    
    try {
        mysqli_begin_transaction($con, MYSQLI_TRANS_START_READ_WRITE);
        
        $cart_query = "SELECT up.item_id, up.quantity, p.name, p.price, p.unit, p.discount 
                       FROM users_products up 
                       INNER JOIN products p ON up.item_id = p.id 
                       WHERE up.user_id='$user_id' AND up.status='Added to cart'";
        $cart_result = mysqli_query($con, $cart_query);
        
        if (!$cart_result || mysqli_num_rows($cart_result) == 0) {
            mysqli_rollback($con);
            mysqli_autocommit($con, true);
            return ['success' => false, 'error' => 'Cart is empty'];
        }
        
        $cart_items = [];
        while ($item = mysqli_fetch_assoc($cart_result)) {
            $cart_items[] = $item;
        }
        
        $insufficient_stock = [];
        
        foreach ($cart_items as $item) {
            $product_id = $item['item_id'];
            $requested_qty = $item['quantity'];
            
            $lock_query = "SELECT id, stock_quantity, name FROM products WHERE id='$product_id' FOR UPDATE";
            $lock_result = mysqli_query($con, $lock_query);
            
            if (!$lock_result || mysqli_num_rows($lock_result) == 0) {
                mysqli_rollback($con);
                mysqli_autocommit($con, true);
                return ['success' => false, 'error' => 'Product not found: ' . $item['name']];
            }
            
            $product = mysqli_fetch_assoc($lock_result);
            $available_stock = (int)$product['stock_quantity'];
            
            if ($available_stock < $requested_qty) {
                $insufficient_stock[] = [
                    'name' => $product['name'],
                    'requested' => $requested_qty,
                    'available' => $available_stock
                ];
            }
        }
        
        if (!empty($insufficient_stock)) {
            mysqli_rollback($con);
            mysqli_autocommit($con, true);
            return ['success' => false, 'error' => 'insufficient_stock', 'items' => $insufficient_stock];
        }
        
        foreach ($cart_items as $item) {
            $product_id = $item['item_id'];
            $requested_qty = $item['quantity'];
            
            $update_query = "UPDATE products SET stock_quantity = stock_quantity - $requested_qty WHERE id='$product_id'";
            if (!mysqli_query($con, $update_query)) {
                mysqli_rollback($con);
                mysqli_autocommit($con, true);
                return ['success' => false, 'error' => 'Failed to update stock for: ' . $item['name']];
            }
        }
        
        $order_number = 'ORD-' . str_pad($user_id, 4, '0', STR_PAD_LEFT) . '-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $total_amount = 0;
        $discount_amount = 0;
        $order_items = [];
        
        foreach ($cart_items as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $item_discount = ($item['price'] * $item['discount'] / 100) * $item['quantity'];
            $final_price = $subtotal - $item_discount;
            
            $total_amount += $subtotal;
            $discount_amount += $item_discount;
            
            $order_items[] = [
                'product_id' => $item['item_id'],
                'product_name' => $item['name'],
                'product_price' => $item['price'],
                'product_unit' => $item['unit'],
                'discount_percent' => $item['discount'],
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal,
                'discount_amount' => $item_discount,
                'final_price' => $final_price
            ];
        }
        
        $final_amount = $total_amount - $discount_amount;
        
        $delivery_address = $delivery_address ? mysqli_real_escape_string($con, $delivery_address) : 'NULL';
        $delivery_phone = $delivery_phone ? mysqli_real_escape_string($con, $delivery_phone) : 'NULL';
        
        $address_value = $delivery_address !== 'NULL' ? "'$delivery_address'" : 'NULL';
        $phone_value = $delivery_phone !== 'NULL' ? "'$delivery_phone'" : 'NULL';
        
        $order_query = "INSERT INTO orders (user_id, order_number, total_amount, discount_amount, final_amount, payment_status, payment_method, delivery_address, delivery_phone) 
                        VALUES ('$user_id', '$order_number', $total_amount, $discount_amount, $final_amount, 'Paid', '$payment_method', $address_value, $phone_value)";
        
        if (!mysqli_query($con, $order_query)) {
            mysqli_rollback($con);
            mysqli_autocommit($con, true);
            return ['success' => false, 'error' => 'Failed to create order'];
        }
        
        $order_id = mysqli_insert_id($con);
        
        foreach ($order_items as $item) {
            $product_id = $item['product_id'];
            $product_name = mysqli_real_escape_string($con, $item['product_name']);
            $product_price = $item['product_price'];
            $product_unit = mysqli_real_escape_string($con, $item['product_unit']);
            $discount_percent = $item['discount_percent'];
            $quantity = $item['quantity'];
            $subtotal = $item['subtotal'];
            $item_discount_amount = $item['discount_amount'];
            $final_price = $item['final_price'];
            
            $item_query = "INSERT INTO order_items (order_id, product_id, product_name, product_price, product_unit, discount_percent, quantity, subtotal, discount_amount, final_price) 
                           VALUES ($order_id, $product_id, '$product_name', $product_price, '$product_unit', $discount_percent, $quantity, $subtotal, $item_discount_amount, $final_price)";
            
            if (!mysqli_query($con, $item_query)) {
                mysqli_rollback($con);
                mysqli_autocommit($con, true);
                return ['success' => false, 'error' => 'Failed to create order items'];
            }
        }
        
        $update_cart_query = "UPDATE users_products SET status='Confirmed' WHERE user_id='$user_id' AND status='Added to cart'";
        if (!mysqli_query($con, $update_cart_query)) {
            mysqli_rollback($con);
            mysqli_autocommit($con, true);
            return ['success' => false, 'error' => 'Failed to update cart status'];
        }
        
        mysqli_commit($con);
        mysqli_autocommit($con, true);
        
        return ['success' => true, 'order_id' => $order_id];
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_autocommit($con, true);
        return ['success' => false, 'error' => 'Transaction failed: ' . $e->getMessage()];
    }
}

function get_order_details($order_id) {
    global $con;
    $order_id = mysqli_real_escape_string($con, $order_id);
    $query = "SELECT o.*, u.first_name, u.last_name, u.email_id, u.phone 
              FROM orders o 
              INNER JOIN users u ON o.user_id = u.id 
              WHERE o.id='$order_id'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

function get_order_items($order_id) {
    global $con;
    $order_id = mysqli_real_escape_string($con, $order_id);
    $query = "SELECT * FROM order_items WHERE order_id='$order_id'";
    $result = mysqli_query($con, $query);
    $items = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
    }
    return $items;
}
}
