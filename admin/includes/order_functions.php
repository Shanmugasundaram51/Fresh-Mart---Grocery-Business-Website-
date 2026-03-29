<?php
function get_all_orders($filter = null, $time_filter = null) {
    global $con;
    $query = "SELECT o.*, u.first_name, u.last_name, u.email_id 
              FROM orders o 
              INNER JOIN users u ON o.user_id = u.id ";
    
    $where_conditions = [];
    
    if ($filter == 'pending') {
        $where_conditions[] = "o.order_status = 'Pending'";
    } elseif ($filter == 'processing') {
        $where_conditions[] = "o.order_status = 'Processing'";
    } elseif ($filter == 'shipped') {
        $where_conditions[] = "o.order_status = 'Shipped'";
    } elseif ($filter == 'delivered') {
        $where_conditions[] = "o.order_status = 'Delivered'";
    }
    
    if ($time_filter == '24hours') {
        $where_conditions[] = "o.order_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    } elseif ($time_filter == '3days') {
        $where_conditions[] = "o.order_date >= DATE_SUB(NOW(), INTERVAL 3 DAY)";
    } elseif ($time_filter == '7days') {
        $where_conditions[] = "o.order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    }
    
    if (!empty($where_conditions)) {
        $query .= "WHERE " . implode(' AND ', $where_conditions) . " ";
    }
    
    $query .= "ORDER BY o.order_date DESC";
    
    $result = mysqli_query($con, $query);
    $orders = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    }
    return $orders;
}

function get_order_with_items($order_id) {
    global $con;
    $order_id = mysqli_real_escape_string($con, $order_id);
    
    $order_query = "SELECT o.*, u.first_name, u.last_name, u.email_id, u.phone 
                    FROM orders o 
                    INNER JOIN users u ON o.user_id = u.id 
                    WHERE o.id='$order_id'";
    $order_result = mysqli_query($con, $order_query);
    
    if (!$order_result || mysqli_num_rows($order_result) == 0) {
        return null;
    }
    
    $order = mysqli_fetch_assoc($order_result);
    
    $items_query = "SELECT * FROM order_items WHERE order_id='$order_id'";
    $items_result = mysqli_query($con, $items_query);
    $items = [];
    
    if ($items_result && mysqli_num_rows($items_result) > 0) {
        while ($row = mysqli_fetch_assoc($items_result)) {
            $items[] = $row;
        }
    }
    
    $order['items'] = $items;
    return $order;
}

function update_order_status($order_id, $status) {
    global $con;
    $order_id = mysqli_real_escape_string($con, $order_id);
    $status = mysqli_real_escape_string($con, $status);
    
    $query = "UPDATE orders SET order_status='$status' WHERE id='$order_id'";
    return mysqli_query($con, $query);
}

function get_total_orders_count() {
    global $con;
    $query = "SELECT COUNT(*) as total FROM orders";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}

function get_total_revenue() {
    global $con;
    $query = "SELECT SUM(final_amount) as total FROM orders WHERE order_status != 'Cancelled'";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ? $row['total'] : 0;
    }
    return 0;
}

function get_pending_orders_count() {
    global $con;
    $query = "SELECT COUNT(*) as total FROM orders WHERE order_status = 'Pending'";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}
?>
