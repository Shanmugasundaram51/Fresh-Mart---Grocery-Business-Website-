<?php
session_start();
require 'includes/admin_common.php';
check_admin_login();

if (!isset($_GET['order_id'])) {
    header('Location: orders.php');
    exit();
}

$order_id = mysqli_real_escape_string($con, $_GET['order_id']);

require '../includes/common.php';
$order = get_order_details($order_id);
if (!$order) {
    header('Location: orders.php?error=Order not found');
    exit();
}

$order_items = get_order_items($order_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo htmlspecialchars($order['order_number']); ?> | Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .invoice-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            margin: -40px -40px 30px -40px;
            text-align: center;
        }
        
        .invoice-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .invoice-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        
        .invoice-title {
            text-align: center;
            color: #10b981;
            font-weight: 700;
            margin-bottom: 30px;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .info-box h5 {
            color: #10b981;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .info-box p {
            margin: 5px 0;
            color: #374151;
        }
        
        .invoice-table {
            width: 100%;
            margin: 20px 0;
        }
        
        .invoice-table thead {
            background: #10b981;
            color: white;
        }
        
        .invoice-table thead th {
            padding: 12px;
            font-weight: 600;
        }
        
        .invoice-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .invoice-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        
        .total-row {
            display: flex;
            justify-content: flex-end;
            padding: 8px 0;
        }
        
        .total-row .label {
            width: 150px;
            text-align: right;
            padding-right: 20px;
            font-weight: 500;
        }
        
        .total-row .value {
            width: 120px;
            text-align: right;
            font-weight: 600;
        }
        
        .grand-total {
            border-top: 2px solid #10b981;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 1.2rem;
            color: #10b981;
        }
        
        .discount-text {
            color: #dc2626;
        }
        
        .terms {
            margin-top: 40px;
            padding: 20px;
            background: #f9fafb;
            border-left: 4px solid #10b981;
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .action-buttons {
            margin: 20px 0;
            text-align: center;
        }
        
        .btn-download {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            margin: 0 10px;
        }
        
        .btn-download:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
        }
        
        .admin-badge {
            background: #6366f1;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="admin-badge no-print">
            <i class="fa fa-shield"></i> Admin View
        </div>
        
        <div class="invoice-header">
            <h1>FRESH MART</h1>
            <p>Your Fresh Grocery Store</p>
        </div>
        
        <h2 class="invoice-title">TAX INVOICE</h2>
        
        <div class="info-section">
            <div class="info-box">
                <h5>Bill To:</h5>
                <p><strong><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></strong></p>
                <p>Email: <?php echo htmlspecialchars($order['email_id']); ?></p>
                <?php if (!empty($order['phone'])): ?>
                <p>Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                <?php endif; ?>
                <?php if (!empty($order['delivery_address'])): ?>
                <p>Address: <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="info-box">
                <h5>Invoice Details:</h5>
                <p><strong>Invoice #:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
                <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($order['order_date'])); ?></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                <p><strong>Order Status:</strong> 
                    <span class="badge badge-<?php 
                        $badge_class = 'secondary';
                        switch($order['order_status']) {
                            case 'Pending': $badge_class = 'warning'; break;
                            case 'Processing': $badge_class = 'info'; break;
                            case 'Shipped': $badge_class = 'primary'; break;
                            case 'Delivered': $badge_class = 'success'; break;
                            case 'Cancelled': $badge_class = 'danger'; break;
                        }
                        echo $badge_class;
                    ?>"><?php echo htmlspecialchars($order['order_status']); ?></span>
                </p>
            </div>
        </div>
        
        <h5 style="color: #374151; font-weight: 600; margin-bottom: 15px;">Order Items</h5>
        
        <table class="invoice-table table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">Product Name</th>
                    <th style="width: 15%; text-align: right;">Price</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 15%; text-align: right;">Discount</th>
                    <th style="width: 15%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $item_num = 1;
                foreach ($order_items as $item): 
                ?>
                <tr>
                    <td style="text-align: center;"><?php echo $item_num; ?></td>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td style="text-align: right;">Rs <?php echo number_format($item['product_price'], 2); ?></td>
                    <td style="text-align: center;"><?php echo $item['quantity'] . ' ' . htmlspecialchars($item['product_unit']); ?></td>
                    <td style="text-align: right;">
                        <?php 
                        if ($item['discount_percent'] > 0) {
                            echo $item['discount_percent'] . '% (Rs ' . number_format($item['discount_amount'], 2) . ')';
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td style="text-align: right;"><strong>Rs <?php echo number_format($item['final_price'], 2); ?></strong></td>
                </tr>
                <?php 
                $item_num++;
                endforeach; 
                ?>
            </tbody>
        </table>
        
        <div class="total-section">
            <div class="total-row">
                <div class="label">Subtotal:</div>
                <div class="value">Rs <?php echo number_format($order['total_amount'], 2); ?></div>
            </div>
            <div class="total-row discount-text">
                <div class="label">Discount:</div>
                <div class="value">- Rs <?php echo number_format($order['discount_amount'], 2); ?></div>
            </div>
            <div class="total-row grand-total">
                <div class="label">Grand Total:</div>
                <div class="value">Rs <?php echo number_format($order['final_amount'], 2); ?></div>
            </div>
        </div>
        
        <div class="terms">
            <strong>Terms & Conditions:</strong><br>
            All products are subject to availability. Prices are inclusive of all taxes. Please check items upon delivery. For any queries, contact our customer support.
        </div>
        
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="btn btn-download">
                <i class="fa fa-print"></i> Print Invoice
            </button>
            <a href="../generate_invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-download">
                <i class="fa fa-download"></i> Download PDF
            </a>
            <a href="orders.php" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</body>
</html>
