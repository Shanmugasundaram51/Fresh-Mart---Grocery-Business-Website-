<?php
session_start();
require 'includes/admin_common.php';
require 'includes/order_functions.php';
check_admin_login();

$filter = isset($_GET['filter']) ? $_GET['filter'] : null;
$time_filter = isset($_GET['time']) ? $_GET['time'] : null;
$orders = get_all_orders($filter, $time_filter);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Admin Panel</title>
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
                    <h1 class="h2">Manage Orders</h1>
                </div>
                
                <?php
                if (isset($_GET['success'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> ' . htmlspecialchars($_GET['success']) . '
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                          </div>';
                }
                ?>
                
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background: #10b981; color: white; border: none;">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search by Order #, Customer Name, Email, or Phone..." onkeyup="searchOrders()">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                            <i class="fa fa-times"></i> Clear
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fa fa-info-circle"></i> Search works across order number, customer name, email, and phone number
                                </small>
                            </div>
                            <div class="col-md-4 text-right">
                                <div id="searchStats" class="mt-2">
                                    <span class="badge badge-info" style="font-size: 0.9rem; padding: 8px 12px;">
                                        <i class="fa fa-list"></i> <span id="visibleCount">0</span> orders shown
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0 font-weight-bold text-primary">Orders List</h6>
                            <div>
                                <a href="orders.php<?php echo $time_filter ? '?time=' . $time_filter : ''; ?>" class="btn btn-sm btn-outline-secondary <?php echo $filter == null ? 'active' : ''; ?>">All</a>
                                <a href="orders.php?filter=pending<?php echo $time_filter ? '&time=' . $time_filter : ''; ?>" class="btn btn-sm btn-outline-warning <?php echo $filter == 'pending' ? 'active' : ''; ?>">Pending</a>
                                <a href="orders.php?filter=processing<?php echo $time_filter ? '&time=' . $time_filter : ''; ?>" class="btn btn-sm btn-outline-info <?php echo $filter == 'processing' ? 'active' : ''; ?>">Processing</a>
                                <a href="orders.php?filter=shipped<?php echo $time_filter ? '&time=' . $time_filter : ''; ?>" class="btn btn-sm btn-outline-primary <?php echo $filter == 'shipped' ? 'active' : ''; ?>">Shipped</a>
                                <a href="orders.php?filter=delivered<?php echo $time_filter ? '&time=' . $time_filter : ''; ?>" class="btn btn-sm btn-outline-success <?php echo $filter == 'delivered' ? 'active' : ''; ?>">Delivered</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="mb-0 mr-3 font-weight-bold" style="color: #6c757d;">
                                <i class="fa fa-clock-o"></i> Filter by Time:
                            </label>
                            <div class="btn-group" role="group">
                                <a href="orders.php<?php echo $filter ? '?filter=' . $filter : ''; ?>" 
                                   class="btn btn-sm <?php echo $time_filter == null ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fa fa-calendar"></i> All Time
                                </a>
                                <a href="orders.php?time=24hours<?php echo $filter ? '&filter=' . $filter : ''; ?>" 
                                   class="btn btn-sm <?php echo $time_filter == '24hours' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fa fa-clock-o"></i> Last 24 Hours
                                </a>
                                <a href="orders.php?time=3days<?php echo $filter ? '&filter=' . $filter : ''; ?>" 
                                   class="btn btn-sm <?php echo $time_filter == '3days' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fa fa-calendar-o"></i> Last 3 Days
                                </a>
                                <a href="orders.php?time=7days<?php echo $filter ? '&filter=' . $filter : ''; ?>" 
                                   class="btn btn-sm <?php echo $time_filter == '7days' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fa fa-calendar-check-o"></i> Last 7 Days
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        $active_filters = [];
                        if ($filter) {
                            $active_filters[] = '<span class="badge badge-info"><i class="fa fa-tag"></i> Status: ' . ucfirst($filter) . '</span>';
                        }
                        if ($time_filter == '24hours') {
                            $active_filters[] = '<span class="badge badge-info"><i class="fa fa-clock-o"></i> Last 24 Hours</span>';
                        } elseif ($time_filter == '3days') {
                            $active_filters[] = '<span class="badge badge-info"><i class="fa fa-calendar-o"></i> Last 3 Days</span>';
                        } elseif ($time_filter == '7days') {
                            $active_filters[] = '<span class="badge badge-info"><i class="fa fa-calendar-check-o"></i> Last 7 Days</span>';
                        }
                        
                        if (!empty($active_filters)) {
                            echo '<div class="alert alert-light mb-3" style="border-left: 4px solid #17a2b8;">';
                            echo '<strong><i class="fa fa-filter"></i> Active Filters:</strong> ';
                            echo implode(' ', $active_filters);
                            echo ' <a href="orders.php" class="btn btn-sm btn-outline-secondary ml-2"><i class="fa fa-times"></i> Clear All</a>';
                            echo '</div>';
                        }
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Payment Method</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($orders) > 0) {
                                        foreach ($orders as $order) {
                                            $status_class = 'secondary';
                                            switch($order['order_status']) {
                                                case 'Pending': $status_class = 'warning'; break;
                                                case 'Processing': $status_class = 'info'; break;
                                                case 'Shipped': $status_class = 'primary'; break;
                                                case 'Delivered': $status_class = 'success'; break;
                                                case 'Cancelled': $status_class = 'danger'; break;
                                            }
                                            
                                            $payment_class = $order['payment_status'] == 'Paid' ? 'success' : 'warning';
                                            
                                            $items_query = "SELECT COUNT(*) as count FROM order_items WHERE order_id='" . $order['id'] . "'";
                                            $items_result = mysqli_query($con, $items_query);
                                            $items_count = 0;
                                            if ($items_result) {
                                                $items_row = mysqli_fetch_assoc($items_result);
                                                $items_count = $items_row['count'];
                                            }
                                            
                                            $payment_method_icon = '';
                                            $payment_method_badge_class = 'badge-secondary';
                                            switch($order['payment_method']) {
                                                case 'Cash on Delivery':
                                                    $payment_method_icon = '<i class="fa fa-money"></i> ';
                                                    $payment_method_badge_class = 'badge-warning';
                                                    break;
                                                case 'UPI':
                                                    $payment_method_icon = '<i class="fa fa-mobile"></i> ';
                                                    $payment_method_badge_class = 'badge-info';
                                                    break;
                                                case 'Credit Card':
                                                case 'Debit Card':
                                                    $payment_method_icon = '<i class="fa fa-credit-card"></i> ';
                                                    $payment_method_badge_class = 'badge-primary';
                                                    break;
                                            }
                                            
                                            echo '<tr>';
                                            echo '<td><strong>' . htmlspecialchars($order['order_number']) . '</strong></td>';
                                            echo '<td>' . htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) . '<br><small class="text-muted">' . htmlspecialchars($order['email_id']) . '</small></td>';
                                            echo '<td>' . date('M d, Y', strtotime($order['order_date'])) . '<br><small class="text-muted">' . date('h:i A', strtotime($order['order_date'])) . '</small></td>';
                                            echo '<td>' . $items_count . ' items</td>';
                                            echo '<td><strong>Rs ' . number_format($order['final_amount'], 2) . '</strong>';
                                            if ($order['discount_amount'] > 0) {
                                                echo '<br><small class="text-success">Saved: Rs ' . number_format($order['discount_amount'], 2) . '</small>';
                                            }
                                            echo '</td>';
                                            echo '<td><span class="badge badge-' . $status_class . '">' . $order['order_status'] . '</span></td>';
                                            echo '<td><span class="badge badge-' . $payment_class . '">' . $order['payment_status'] . '</span></td>';
                                            echo '<td><span class="badge ' . $payment_method_badge_class . '">' . $payment_method_icon . htmlspecialchars($order['payment_method']) . '</span></td>';
                                            echo '<td class="table-actions">';
                                            echo '<a href="order_details.php?id=' . $order['id'] . '" class="btn btn-sm btn-info mb-1" title="View Details"><i class="fa fa-eye"></i></a> ';
                                            echo '<a href="../view_invoice.php?order_id=' . $order['id'] . '" class="btn btn-sm btn-success mb-1" title="View Invoice Details" target="_blank"><i class="fa fa-file-text"></i></a> ';
                                            echo '<a href="../generate_invoice.php?order_id=' . $order['id'] . '" class="btn btn-sm btn-primary mb-1" title="Download Invoice PDF"><i class="fa fa-download"></i></a>';
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" class="text-center">No orders found</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    
    <script>
    function searchOrders() {
        var input = document.getElementById('searchInput');
        var filter = input.value.toLowerCase();
        var table = document.querySelector('.table tbody');
        var rows = table.getElementsByTagName('tr');
        var visibleCount = 0;
        
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var cells = row.getElementsByTagName('td');
            
            if (cells.length > 0) {
                var orderNumber = cells[0].textContent || cells[0].innerText;
                var customerInfo = cells[1].textContent || cells[1].innerText;
                var date = cells[2].textContent || cells[2].innerText;
                
                var searchText = (orderNumber + ' ' + customerInfo + ' ' + date).toLowerCase();
                
                if (searchText.indexOf(filter) > -1) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
        }
        
        document.getElementById('visibleCount').textContent = visibleCount;
        
        if (filter === '') {
            document.getElementById('searchStats').innerHTML = '<span class="badge badge-info" style="font-size: 0.9rem; padding: 8px 12px;"><i class="fa fa-list"></i> <span id="visibleCount">' + visibleCount + '</span> orders shown</span>';
        } else {
            document.getElementById('searchStats').innerHTML = '<span class="badge badge-success" style="font-size: 0.9rem; padding: 8px 12px;"><i class="fa fa-filter"></i> <span id="visibleCount">' + visibleCount + '</span> results found</span>';
        }
    }
    
    function clearSearch() {
        document.getElementById('searchInput').value = '';
        searchOrders();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        searchOrders();
    });
    </script>
</body>
</html>
