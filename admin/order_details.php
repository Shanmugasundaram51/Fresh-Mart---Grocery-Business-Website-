<?php
session_start();
require 'includes/admin_common.php';
require 'includes/order_functions.php';
check_admin_login();

if (!isset($_GET['id'])) {
    header('Location: orders.php?error=Invalid order ID');
    exit();
}

$order_id = $_GET['id'];
$order = get_order_with_items($order_id);

if (!$order) {
    header('Location: orders.php?error=Order not found');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/admin_sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Order Details</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="orders.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>
                
                <?php
                if (isset($_GET['success'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> ' . htmlspecialchars($_GET['success']) . '
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                          </div>';
                }
                ?>
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Order #<?php echo htmlspecialchars($order['order_number']); ?></h6>
                                <?php
                                $status_class = 'secondary';
                                switch($order['order_status']) {
                                    case 'Pending': $status_class = 'warning'; break;
                                    case 'Processing': $status_class = 'info'; break;
                                    case 'Shipped': $status_class = 'primary'; break;
                                    case 'Delivered': $status_class = 'success'; break;
                                    case 'Cancelled': $status_class = 'danger'; break;
                                }
                                ?>
                                <span class="badge badge-<?php echo $status_class; ?> badge-lg"><?php echo $order['order_status']; ?></span>
                            </div>
                            <div class="card-body">
                                <h6 class="font-weight-bold mb-3">Order Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Discount</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($order['items'] as $item) {
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($item['product_name']) . '<br><small class="text-muted">Unit: ' . htmlspecialchars($item['product_unit']) . '</small></td>';
                                                echo '<td>Rs ' . number_format($item['product_price'], 2) . '</td>';
                                                echo '<td>' . $item['quantity'] . '</td>';
                                                echo '<td>';
                                                if ($item['discount_percent'] > 0) {
                                                    echo $item['discount_percent'] . '% OFF<br>';
                                                    echo '<small class="text-success">-Rs ' . number_format($item['discount_amount'], 2) . '</small>';
                                                } else {
                                                    echo '-';
                                                }
                                                echo '</td>';
                                                echo '<td><strong>Rs ' . number_format($item['final_price'], 2) . '</strong></td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                            <tr class="table-active">
                                                <td colspan="4" class="text-right"><strong>Total Amount:</strong></td>
                                                <td><strong>Rs <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                            </tr>
                                            <?php if ($order['discount_amount'] > 0) { ?>
                                            <tr class="table-success">
                                                <td colspan="4" class="text-right"><strong>Total Discount:</strong></td>
                                                <td><strong class="text-success">-Rs <?php echo number_format($order['discount_amount'], 2); ?></strong></td>
                                            </tr>
                                            <?php } ?>
                                            <tr class="table-info">
                                                <td colspan="4" class="text-right"><strong>Final Amount:</strong></td>
                                                <td><strong class="text-primary">Rs <?php echo number_format($order['final_amount'], 2); ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Name:</strong><br><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                                <p><strong>Email:</strong><br><?php echo htmlspecialchars($order['email_id']); ?></p>
                                <p><strong>Phone:</strong><br><?php 
                                    $phone = $order['phone'] ? $order['phone'] : ($order['delivery_phone'] ? $order['delivery_phone'] : 'N/A');
                                    echo htmlspecialchars($phone); 
                                ?></p>
                                <?php if ($order['delivery_address']) { ?>
                                <p><strong>Delivery Address:</strong><br><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Order Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Order Date:</strong><br><?php echo date('F d, Y h:i A', strtotime($order['order_date'])); ?></p>
                                <p><strong>Payment Method:</strong><br><?php echo htmlspecialchars($order['payment_method']); ?></p>
                                <p><strong>Payment Status:</strong><br>
                                    <span class="badge badge-<?php echo $order['payment_status'] == 'Paid' ? 'success' : 'warning'; ?>">
                                        <?php echo $order['payment_status']; ?>
                                    </span>
                                </p>
                                <?php if ($order['notes']) { ?>
                                <p><strong>Notes:</strong><br><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Update Status</h6>
                            </div>
                            <div class="card-body">
                                <form action="update_order_status.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <div class="form-group">
                                        <label for="order_status">Order Status</label>
                                        <select class="form-control" id="order_status" name="order_status">
                                            <option value="Pending" <?php echo $order['order_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Processing" <?php echo $order['order_status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="Shipped" <?php echo $order['order_status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="Delivered" <?php echo $order['order_status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="Cancelled" <?php echo $order['order_status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-save"></i> Update Status
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>
