<?php
require "includes/common.php";
session_start();
if (!isset($_SESSION['email'])) {
    header('location: index.php');
}
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
<div class="container" style="margin-top:80px">
    <?php
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        if ($error == 'stock_validation') {
            $items = isset($_GET['items']) ? $_GET['items'] : '';
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fa fa-exclamation-triangle"></i> Out of Stock!</strong><br>
                    The following items have insufficient stock: ' . htmlspecialchars($items) . '<br>
                    <small>Another customer may have purchased these items. Please adjust your cart.</small>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </div>';
        } elseif ($error == 'insufficient_stock') {
            $available = isset($_GET['available']) ? $_GET['available'] : 0;
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Insufficient Stock!</strong> Only ' . $available . ' units available.
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </div>';
        } elseif ($error == 'order_failed') {
            $message = isset($_GET['message']) ? $_GET['message'] : 'Order placement failed';
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fa fa-times-circle"></i> Order Failed!</strong><br>
                    ' . htmlspecialchars($message) . '
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </div>';
        }
    }
    if (isset($_GET['success']) && $_GET['success'] == 'quantity_updated') {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Updated!</strong> Cart quantity updated successfully.
                <button type="button" class="close" data-dismiss="alert">&times;</button>
              </div>';
    }
    ?>
</div>
<div class="d-flex justify-content-center">
                <div class=" col-md-6  my-5 table-responsive p-5">
                    <table class="table table-striped table-bordered table-hover ">
                    <?php
$sum = 0;
$user_id = $_SESSION['user_id'];
$query = "SELECT products.price AS Price, products.id, products.name AS Name, products.stock_quantity AS Stock, users_products.quantity AS Quantity, products.unit AS Unit, users_products.id AS cart_id
          FROM users_products 
          JOIN products ON users_products.item_id = products.id 
          WHERE users_products.user_id='$user_id' AND status='Added To Cart'";
$result = mysqli_query($con, $query);
$has_stock_issue = false;

if (mysqli_num_rows($result) >= 1) {
    ?>
                        <thead>
                            <tr>
                                <th>Item Number</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                                <th>Stock Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php
while ($row = mysqli_fetch_array($result)) {
        $quantity = $row["Quantity"];
        $item_total = $row["Price"] * $quantity;
        $sum += $item_total;
        $id = $row["id"] . ", ";
        $stock_status = "";
        $stock_class = "badge-success";
        
        if ($row["Stock"] == 0) {
            $stock_status = "Out of Stock";
            $stock_class = "badge-danger";
            $has_stock_issue = true;
        } elseif ($row["Stock"] < $quantity) {
            $stock_status = "Only " . $row["Stock"] . " available";
            $stock_class = "badge-warning text-dark";
            $has_stock_issue = true;
        } else {
            $stock_status = "Available";
        }
        
        $cart_id = $row["cart_id"];
        echo "<tr>
                <td>#" . $row["id"] . "</td>
                <td>" . $row["Name"] . "</td>
                <td>
                    <div class='d-flex align-items-center' style='gap: 5px;'>
                        <input type='number' id='qty_$cart_id' class='form-control form-control-sm' value='$quantity' min='1' max='{$row["Stock"]}' style='width: 70px;' onchange='updateQuantity($cart_id, {$row["id"]}, {$row["Stock"]})'>
                        <span class='text-muted'>{$row["Unit"]}</span>
                    </div>
                </td>
                <td>Rs " . $row["Price"] . "</td>
                <td id='subtotal_$cart_id'>Rs " . $item_total . "</td>
                <td><span class='badge $stock_class'>$stock_status</span></td>
                <td><a href='cart-remove.php?id={$row['id']}' class='remove_item_link'> Remove</a></td>
              </tr>";
    }
    $id = rtrim($id, ", ");
    
    if ($has_stock_issue) {
        echo "<tr><td colspan='7' class='text-danger text-center'><strong>Some items have stock issues. Please remove them or adjust quantities before confirming.</strong></td></tr>";
        echo "<tr><td></td><td colspan='3'><strong>Total</strong></td><td colspan='3'><strong>Rs " . $sum . "</strong></td></tr>";
    } else {
        echo "<tr><td></td><td colspan='3'><strong>Total</strong></td><td colspan='2'><strong>Rs " . $sum . "</strong></td><td><button type='button' class='btn btn-primary' data-toggle='modal' data-target='#addressModal'>Confirm Order</button></td></tr>";
    }
    ?>
                            </tbody>
                            <?php
} else {
    echo "<div class='text-center py-5'> <img src='images/emptycart.png' class='image-fluid' height='150' width='150'></div><br/>";
    echo "<div class='text-bold h5 text-center'>Add items to the cart first!</div>";
}
?>
                        <?php
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--footer -->
         <?php include 'includes/footer.php'?>
        <!--footer end-->

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-map-marker"></i> Delivery Information</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="success.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="delivery_phone">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="delivery_phone" name="delivery_phone" 
                               value="<?php 
                               $user_query = "SELECT phone FROM users WHERE id='$user_id'";
                               $user_result = mysqli_query($con, $user_query);
                               if ($user_result && mysqli_num_rows($user_result) > 0) {
                                   $user_data = mysqli_fetch_assoc($user_result);
                                   echo htmlspecialchars($user_data['phone']);
                               }
                               ?>" 
                               pattern="[0-9]{10}" 
                               placeholder="Enter 10 digit phone number" 
                               required>
                        <small class="form-text text-muted">We'll call you for delivery confirmation</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="delivery_address">Delivery Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="delivery_address" name="delivery_address" 
                                  rows="4" placeholder="Enter your complete delivery address" 
                                  required></textarea>
                        <small class="form-text text-muted">Include house/flat number, street, landmark, city, pincode</small>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fa fa-credit-card"></i> Payment Method <span class="text-danger">*</span></label>
                        <div class="payment-methods">
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="payment_cod" name="payment_method" class="custom-control-input" value="Cash on Delivery" checked onchange="updatePaymentSummary()">
                                <label class="custom-control-label" for="payment_cod">
                                    <i class="fa fa-money"></i> Cash on Delivery (COD)
                                </label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="payment_upi" name="payment_method" class="custom-control-input" value="UPI" onchange="updatePaymentSummary()">
                                <label class="custom-control-label" for="payment_upi">
                                    <i class="fa fa-mobile"></i> UPI
                                </label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="payment_credit" name="payment_method" class="custom-control-input" value="Credit Card" onchange="updatePaymentSummary()">
                                <label class="custom-control-label" for="payment_credit">
                                    <i class="fa fa-credit-card"></i> Credit Card
                                </label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="payment_debit" name="payment_method" class="custom-control-input" value="Debit Card" onchange="updatePaymentSummary()">
                                <label class="custom-control-label" for="payment_debit">
                                    <i class="fa fa-credit-card-alt"></i> Debit Card
                                </label>
                            </div>
                        </div>
                        <small class="form-text text-muted">Select your preferred payment method</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong><i class="fa fa-info-circle"></i> Order Summary:</strong><br>
                        Total Amount: <strong>Rs <?php echo $sum; ?></strong><br>
                        Payment Method: <strong id="selected_payment_display">Cash on Delivery</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check-circle"></i> Confirm Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

<script>
function updateQuantity(cartId, productId, maxStock) {
    var qty = parseInt(document.getElementById('qty_' + cartId).value);
    
    if (qty < 1) {
        alert('Quantity must be at least 1');
        document.getElementById('qty_' + cartId).value = 1;
        return;
    }
    
    if (qty > maxStock) {
        alert('Only ' + maxStock + ' units available in stock');
        document.getElementById('qty_' + cartId).value = maxStock;
        return;
    }
    
    window.location.href = 'update-cart-quantity.php?cart_id=' + cartId + '&qty=' + qty;
}

function updatePaymentSummary() {
    var selectedPayment = document.querySelector('input[name="payment_method"]:checked');
    if (selectedPayment) {
        document.getElementById('selected_payment_display').textContent = selectedPayment.value;
    }
}
</script>
</html>
