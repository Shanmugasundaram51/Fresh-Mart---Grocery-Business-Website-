<?php
require "includes/common.php";
session_start();
if (!isset($_SESSION['email'])) {
    header('location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$orders_query = "SELECT o.*, 
                 (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                 FROM orders o 
                 WHERE o.user_id='$user_id' 
                 ORDER BY o.order_date DESC";
$orders_result = mysqli_query($con, $orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Dashboard | Fresh Mart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }
        
        .dashboard-header h1 {
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .dashboard-header p {
            opacity: 0.9;
            margin: 0;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
            border-left: 4px solid #10b981;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.2);
        }
        
        .stats-card .icon {
            font-size: 2.5rem;
            color: #10b981;
            margin-bottom: 10px;
        }
        
        .stats-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .stats-card .label {
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .order-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 4px solid #10b981;
        }
        
        .order-card:hover {
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.2);
            transform: translateX(5px);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f3f4f6;
        }
        
        .order-number {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .order-date {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            padding: 10px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .detail-item .label {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .detail-item .value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .detail-item .value.amount {
            color: #10b981;
        }
        
        .detail-item .value.savings {
            color: #dc2626;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-processing {
            background: #dbeafe;
            color: #2563eb;
        }
        
        .status-shipped {
            background: #e0e7ff;
            color: #6366f1;
        }
        
        .status-delivered {
            background: #d1fae5;
            color: #059669;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .order-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-view {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-view:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        }
        
        .btn-invoice-view {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 2px solid #3b82f6;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.2);
        }
        
        .btn-invoice-view:hover {
            background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%);
            color: #1e3a8a;
            border-color: #2563eb;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.3);
        }
        
        .btn-invoice-download {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
            color: #6b21a8;
            border: 2px solid #a855f7;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(168, 85, 247, 0.2);
        }
        
        .btn-invoice-download:hover {
            background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%);
            color: #581c87;
            border-color: #9333ea;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(168, 85, 247, 0.3);
        }
        
        .btn-order-details {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 2px solid #f59e0b;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.2);
        }
        
        .btn-order-details:hover {
            background: linear-gradient(135deg, #fde68a 0%, #fcd34d 100%);
            color: #78350f;
            border-color: #d97706;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(245, 158, 11, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #d1d5db;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: #6b7280;
            margin-bottom: 15px;
        }
        
        .payment-method-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            background: #e0f2fe;
            color: #0369a1;
            display: inline-block;
        }
    </style>
</head>
<body>
<?php include 'includes/header_menu.php'; ?>

<div class="dashboard-header">
    <div class="container">
        <h1><i class="fa fa-tachometer"></i> My Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Customer'); ?>!</p>
    </div>
</div>

<div class="container" style="margin-bottom: 100px;">
    <?php
    $total_orders = 0;
    $total_spent = 0;
    $total_saved = 0;
    
    if ($orders_result) {
        $total_orders = mysqli_num_rows($orders_result);
        
        $stats_query = "SELECT 
                        SUM(final_amount) as total_spent,
                        SUM(discount_amount) as total_saved
                        FROM orders 
                        WHERE user_id='$user_id'";
        $stats_result = mysqli_query($con, $stats_query);
        if ($stats_result && mysqli_num_rows($stats_result) > 0) {
            $stats = mysqli_fetch_assoc($stats_result);
            $total_spent = $stats['total_spent'] ?? 0;
            $total_saved = $stats['total_saved'] ?? 0;
        }
    }
    ?>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="icon"><i class="fa fa-shopping-bag"></i></div>
                <div class="value"><?php echo $total_orders; ?></div>
                <div class="label">Total Orders</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="icon"><i class="fa fa-rupee"></i></div>
                <div class="value">Rs <?php echo number_format($total_spent, 2); ?></div>
                <div class="label">Total Spent</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="icon"><i class="fa fa-tag"></i></div>
                <div class="value">Rs <?php echo number_format($total_saved, 2); ?></div>
                <div class="label">Total Savings</div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <h3 style="color: #1f2937; font-weight: 700; margin-bottom: 25px;">
                <i class="fa fa-history"></i> Order History
            </h3>
            
            <?php
            if ($orders_result && mysqli_num_rows($orders_result) > 0) {
                while ($order = mysqli_fetch_assoc($orders_result)) {
                    $status_class = 'status-' . strtolower($order['order_status']);
                    ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-number">
                                    <i class="fa fa-file-text"></i> <?php echo htmlspecialchars($order['order_number']); ?>
                                </div>
                                <div class="order-date">
                                    <i class="fa fa-calendar"></i> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?>
                                </div>
                            </div>
                            <div>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php 
                                    $status_icons = [
                                        'Pending' => 'fa-clock-o',
                                        'Processing' => 'fa-cog',
                                        'Shipped' => 'fa-truck',
                                        'Delivered' => 'fa-check-circle',
                                        'Cancelled' => 'fa-times-circle'
                                    ];
                                    $icon = $status_icons[$order['order_status']] ?? 'fa-info-circle';
                                    echo '<i class="fa ' . $icon . '"></i> ' . htmlspecialchars($order['order_status']);
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="order-details">
                            <div class="detail-item">
                                <div class="label"><i class="fa fa-shopping-basket"></i> Items</div>
                                <div class="value"><?php echo $order['item_count']; ?> items</div>
                            </div>
                            <div class="detail-item">
                                <div class="label"><i class="fa fa-rupee"></i> Total Amount</div>
                                <div class="value amount">Rs <?php echo number_format($order['final_amount'], 2); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="label"><i class="fa fa-tag"></i> You Saved</div>
                                <div class="value savings">Rs <?php echo number_format($order['discount_amount'], 2); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="label"><i class="fa fa-credit-card"></i> Payment Method</div>
                                <div class="value" style="font-size: 0.95rem;">
                                    <span class="payment-method-badge">
                                        <?php echo htmlspecialchars($order['payment_method']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($order['delivery_address'])): ?>
                        <div class="mt-3 p-3" style="background: #f9fafb; border-radius: 8px;">
                            <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 5px;">
                                <i class="fa fa-map-marker"></i> Delivery Address
                            </div>
                            <div style="color: #374151;">
                                <?php echo htmlspecialchars($order['delivery_address']); ?>
                            </div>
                            <?php if (!empty($order['delivery_phone'])): ?>
                            <div style="color: #374151; margin-top: 5px;">
                                <i class="fa fa-phone"></i> <?php echo htmlspecialchars($order['delivery_phone']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="order-actions mt-3">
                            <a href="view_invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-invoice-view">
                                <i class="fa fa-file-text-o"></i> View Invoice Details
                            </a>
                            <a href="generate_invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-invoice-download">
                                <i class="fa fa-download"></i> Download Invoice PDF
                            </a>
                            <button onclick="viewOrderDetails(<?php echo $order['id']; ?>)" class="btn btn-order-details">
                                <i class="fa fa-list-alt"></i> Order Details
                            </button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="empty-state">
                    <i class="fa fa-shopping-bag"></i>
                    <h3>No Orders Yet</h3>
                    <p style="color: #9ca3af; margin-bottom: 25px;">Start shopping to see your orders here!</p>
                    <a href="products.php" class="btn btn-view btn-lg">
                        <i class="fa fa-shopping-cart"></i> Start Shopping
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-list"></i> Order Items</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <div class="text-center py-4">
                    <i class="fa fa-spinner fa-spin fa-3x text-muted"></i>
                    <p class="mt-3 text-muted">Loading order details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover();
});

function viewOrderDetails(orderId) {
    $('#orderDetailsModal').modal('show');
    
    fetch('get_order_items.php?order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="table-responsive"><table class="table table-hover">';
                html += '<thead style="background: #10b981; color: white;">';
                html += '<tr><th>#</th><th>Product</th><th>Price</th><th>Qty</th><th>Discount</th><th>Total</th></tr>';
                html += '</thead><tbody>';
                
                let itemNum = 1;
                data.items.forEach(item => {
                    html += '<tr>';
                    html += '<td>' + itemNum + '</td>';
                    html += '<td><strong>' + item.product_name + '</strong></td>';
                    html += '<td>Rs ' + parseFloat(item.product_price).toFixed(2) + '</td>';
                    html += '<td>' + item.quantity + ' ' + item.product_unit + '</td>';
                    html += '<td>';
                    if (item.discount_percent > 0) {
                        html += '<span class="text-danger">' + item.discount_percent + '% (Rs ' + parseFloat(item.discount_amount).toFixed(2) + ')</span>';
                    } else {
                        html += '-';
                    }
                    html += '</td>';
                    html += '<td><strong>Rs ' + parseFloat(item.final_price).toFixed(2) + '</strong></td>';
                    html += '</tr>';
                    itemNum++;
                });
                
                html += '</tbody></table></div>';
                
                html += '<div class="mt-3 p-3" style="background: #f9fafb; border-radius: 8px;">';
                html += '<div class="d-flex justify-content-between mb-2">';
                html += '<span>Subtotal:</span><span><strong>Rs ' + parseFloat(data.order.total_amount).toFixed(2) + '</strong></span>';
                html += '</div>';
                html += '<div class="d-flex justify-content-between mb-2 text-danger">';
                html += '<span>Discount:</span><span><strong>- Rs ' + parseFloat(data.order.discount_amount).toFixed(2) + '</strong></span>';
                html += '</div>';
                html += '<div class="d-flex justify-content-between pt-2" style="border-top: 2px solid #10b981; font-size: 1.2rem; color: #10b981;">';
                html += '<span><strong>Grand Total:</strong></span><span><strong>Rs ' + parseFloat(data.order.final_amount).toFixed(2) + '</strong></span>';
                html += '</div>';
                html += '</div>';
                
                $('#orderDetailsContent').html(html);
            } else {
                $('#orderDetailsContent').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + data.message + '</div>');
            }
        })
        .catch(error => {
            $('#orderDetailsContent').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Failed to load order details. Please try again.</div>');
        });
}
</script>
</body>
</html>
