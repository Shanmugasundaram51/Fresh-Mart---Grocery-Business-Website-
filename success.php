<?php
require "includes/common.php";
session_start();

$user_id = $_SESSION['user_id'];

$delivery_address = isset($_POST['delivery_address']) ? $_POST['delivery_address'] : null;
$delivery_phone = isset($_POST['delivery_phone']) ? $_POST['delivery_phone'] : null;
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'Cash on Delivery';

$result = create_order_with_transaction($user_id, $delivery_address, $delivery_phone, $payment_method);

if (!$result['success']) {
    if ($result['error'] === 'insufficient_stock' && isset($result['items'])) {
        $stock_errors = [];
        foreach ($result['items'] as $item) {
            $stock_errors[] = $item['name'] . " (Requested: {$item['requested']}, Available: {$item['available']})";
        }
        header('location: cart.php?error=stock_validation&items=' . urlencode(implode(', ', $stock_errors)));
    } else {
        $error_msg = isset($result['error']) ? $result['error'] : 'Order placement failed';
        header('location: cart.php?error=order_failed&message=' . urlencode($error_msg));
    }
    exit();
}

$_SESSION['last_order_id'] = $result['order_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Fresh Mart | Online Grocery Shopping</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" >
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
include 'includes/header_menu.php';
?>
    <div class="container-fluid mt-5 pt-5" id="content" style="margin-bottom:200px">
            <div class="col-md-8 mx-auto">
                <div class="jumbotron text-center">
                      <i class="fa fa-check-circle" style="font-size: 4rem; color: #10b981; margin-bottom: 1rem;"></i>
                      <h3 style="color: #10b981; font-weight: 700;">Order Confirmed!</h3>
                      <p style="color: #6b7280; font-size: 1.1rem;">Thank you for shopping with Fresh Mart. Your groceries will be delivered soon.</p>
                      <?php 
                      if (isset($_SESSION['last_order_id'])) {
                          $order = get_order_details($_SESSION['last_order_id']);
                          if ($order) {
                              echo '<div class="alert alert-info mt-3">';
                              echo '<strong>Order Number:</strong> ' . htmlspecialchars($order['order_number']) . '<br>';
                              echo '<strong>Total Amount:</strong> Rs ' . number_format($order['final_amount'], 2);
                              echo '</div>';
                              
                              echo '<div class="mt-4">';
                              echo '<a href="view_invoice.php?order_id=' . $order['id'] . '" class="btn btn-success btn-lg mr-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; padding: 12px 30px; font-weight: 600; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);">';
                              echo '<i class="fa fa-file-text"></i> View Invoice Details';
                              echo '</a>';
                              echo '<a href="generate_invoice.php?order_id=' . $order['id'] . '" class="btn btn-success btn-lg" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none; padding: 12px 30px; font-weight: 600; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);">';
                              echo '<i class="fa fa-download"></i> Download Invoice PDF';
                              echo '</a>';
                              echo '</div>';
                          }
                      }
                      ?>
                      <hr>
                    <p>Click <a href="products.php" style="color: #10b981; font-weight: 600;">here</a> to continue shopping.</p>
                </div>
            </div>
        </div>
         <!-- footer-->
         <?php include 'includes/footer.php'?>
        <!--footer ends-->
</body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
  $('[data-toggle="popover"]').popover();
});
$(document).ready(function() {

if(window.location.href.indexOf('#login') != -1) {
  $('#login').modal('show');
}

});
</script>
<?php if (isset($_GET['error'])) {$z = $_GET['error'];
    echo "<script type='text/javascript'>
$(document).ready(function(){
$('#signup').modal('show');
});
</script>";
    echo "<script type='text/javascript'>alert('" . $z . "')</script>";}?>
<?php if (isset($_GET['errorl'])) {$z = $_GET['errorl'];
    echo "<script type='text/javascript'>
$(document).ready(function(){
$('#login').modal('show');
});
</script>";
    echo "<script type='text/javascript'>alert('" . $z . "')</script>";}?>
</html>
