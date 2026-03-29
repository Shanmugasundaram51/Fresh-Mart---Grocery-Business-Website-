<?php
if (!defined('ADMIN_COMMON_INCLUDED')) {
    define('ADMIN_COMMON_INCLUDED', true);
    
    $con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }
} else {
    global $con;
}

if (!function_exists('check_admin_login')) {
function check_admin_login() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit();
    }
}

function verify_admin_credentials($username, $password) {
    global $con;
    $username = mysqli_real_escape_string($con, $username);
    $password = md5($password);
    
    $query = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
    $result = mysqli_query($con, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false;
}

function get_all_products() {
    global $con;
    $query = "SELECT * FROM products ORDER BY id DESC";
    $result = mysqli_query($con, $query);
    $products = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    return $products;
}

function get_product_by_id($product_id) {
    global $con;
    $product_id = mysqli_real_escape_string($con, $product_id);
    $query = "SELECT * FROM products WHERE id='$product_id'";
    $result = mysqli_query($con, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

function add_product($name, $category, $price, $discount, $stock_quantity, $unit, $image_path) {
    global $con;
    $name = mysqli_real_escape_string($con, $name);
    $category = mysqli_real_escape_string($con, $category);
    $price = (int)$price;
    $discount = (int)$discount;
    $stock_quantity = (int)$stock_quantity;
    $unit = mysqli_real_escape_string($con, $unit);
    $image_path = mysqli_real_escape_string($con, $image_path);
    
    $query = "INSERT INTO products (name, category, price, unit, discount, stock_quantity, image_path) 
              VALUES ('$name', '$category', $price, '$unit', $discount, $stock_quantity, '$image_path')";
    
    return mysqli_query($con, $query);
}

function update_product($id, $name, $category, $price, $discount, $stock_quantity, $unit, $image_path = null) {
    global $con;
    $id = (int)$id;
    $name = mysqli_real_escape_string($con, $name);
    $category = mysqli_real_escape_string($con, $category);
    $price = (int)$price;
    $discount = (int)$discount;
    $stock_quantity = (int)$stock_quantity;
    $unit = mysqli_real_escape_string($con, $unit);
    
    if ($image_path !== null) {
        $image_path = mysqli_real_escape_string($con, $image_path);
        $query = "UPDATE products SET name='$name', category='$category', price=$price, 
                  unit='$unit', discount=$discount, stock_quantity=$stock_quantity, image_path='$image_path' 
                  WHERE id=$id";
    } else {
        $query = "UPDATE products SET name='$name', category='$category', price=$price, 
                  unit='$unit', discount=$discount, stock_quantity=$stock_quantity 
                  WHERE id=$id";
    }
    
    return mysqli_query($con, $query);
}

function delete_product($product_id) {
    global $con;
    $product_id = (int)$product_id;
    $query = "DELETE FROM products WHERE id=$product_id";
    return mysqli_query($con, $query);
}

function upload_product_image($file) {
    $target_dir = "../images/";
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP allowed.'];
    }
    
    if ($file["size"] > 5000000) {
        return ['success' => false, 'error' => 'File size too large. Maximum 5MB allowed.'];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'path' => 'images/' . $new_filename];
    } else {
        return ['success' => false, 'error' => 'Failed to upload file.'];
    }
}

function get_total_products() {
    global $con;
    $query = "SELECT COUNT(*) as total FROM products";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}

function get_total_orders() {
    global $con;
    $query = "SELECT COUNT(DISTINCT user_id) as total FROM users_products WHERE status='Confirmed'";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}

function get_low_stock_products() {
    global $con;
    $query = "SELECT COUNT(*) as total FROM products WHERE stock_quantity < 5 AND stock_quantity > 0";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}

function get_out_of_stock_products() {
    global $con;
    $query = "SELECT COUNT(*) as total FROM products WHERE stock_quantity = 0";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}
}
?>
