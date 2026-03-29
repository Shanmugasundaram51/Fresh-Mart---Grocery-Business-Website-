<?php
session_start();
require 'includes/admin_common.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        header('Location: login.php?error=Please fill in all fields');
        exit();
    }
    
    $admin = verify_admin_credentials($username, $password);
    
    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];
        header('Location: dashboard.php');
        exit();
    } else {
        header('Location: login.php?error=Invalid username or password');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>
