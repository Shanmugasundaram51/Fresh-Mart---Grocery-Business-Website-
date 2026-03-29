<?php
session_start();
require 'includes/admin_common.php';
require 'includes/order_functions.php';
check_admin_login();

$total_products = get_total_products();
$total_orders = get_total_orders_count();
$low_stock = get_low_stock_products();
$out_of_stock = get_out_of_stock_products();
$pending_orders = get_pending_orders_count();
$total_revenue = get_total_revenue();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Fresh Mart</title>
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
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-calendar"></i> <?php echo date('F d, Y'); ?>
                            </button>
                        </div>
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
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Products
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_products; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-shopping-bag fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Orders
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_orders; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Low Stock Items
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $low_stock; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Out of Stock
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $out_of_stock; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-times-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xl-6 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Pending Orders
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_orders; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-clock-o fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-6 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Revenue
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rs <?php echo number_format($total_revenue, 2); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-inr fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="add_product.php" class="btn btn-success btn-block btn-lg">
                                            <i class="fa fa-plus-circle"></i> Add New Product
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="products.php" class="btn btn-primary btn-block btn-lg">
                                            <i class="fa fa-list"></i> Manage Products
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="orders.php" class="btn btn-info btn-block btn-lg">
                                            <i class="fa fa-shopping-cart"></i> View Orders
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="products.php?filter=low_stock" class="btn btn-warning btn-block btn-lg">
                                            <i class="fa fa-exclamation-triangle"></i> Low Stock Alert
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Products</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $products = get_all_products();
                                            $count = 0;
                                            foreach ($products as $product) {
                                                if ($count >= 5) break;
                                                $stock = $product['stock_quantity'];
                                                $status_class = 'success';
                                                $status_text = 'In Stock';
                                                
                                                if ($stock == 0) {
                                                    $status_class = 'danger';
                                                    $status_text = 'Out of Stock';
                                                } elseif ($stock < 5) {
                                                    $status_class = 'warning';
                                                    $status_text = 'Low Stock';
                                                }
                                                
                                                echo '<tr>';
                                                echo '<td>' . $product['id'] . '</td>';
                                                echo '<td>';
                                                if ($product['image_path']) {
                                                    echo '<img src="../' . htmlspecialchars($product['image_path']) . '" alt="' . htmlspecialchars($product['name']) . '" style="width: 50px; height: 50px; object-fit: cover;">';
                                                } else {
                                                    echo '<div style="width: 50px; height: 50px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;"><i class="fa fa-image"></i></div>';
                                                }
                                                echo '</td>';
                                                echo '<td>' . htmlspecialchars($product['name']) . '</td>';
                                                echo '<td>' . htmlspecialchars($product['category']) . '</td>';
                                                echo '<td>Rs ' . $product['price'] . '/' . htmlspecialchars($product['unit']) . '</td>';
                                                echo '<td>' . $stock . '</td>';
                                                echo '<td><span class="badge badge-' . $status_class . '">' . $status_text . '</span></td>';
                                                echo '</tr>';
                                                $count++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center">
                                    <a href="products.php" class="btn btn-primary">View All Products</a>
                                </div>
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
