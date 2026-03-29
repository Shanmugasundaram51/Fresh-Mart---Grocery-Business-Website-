<?php
session_start();
require 'includes/admin_common.php';
check_admin_login();

$products = get_all_products();

$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
if ($filter == 'low_stock') {
    $products = array_filter($products, function($p) {
        return $p['stock_quantity'] > 0 && $p['stock_quantity'] < 5;
    });
} elseif ($filter == 'out_of_stock') {
    $products = array_filter($products, function($p) {
        return $p['stock_quantity'] == 0;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin Panel</title>
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
                    <h1 class="h2">Manage Products</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="add_product.php" class="btn btn-sm btn-success">
                            <i class="fa fa-plus-circle"></i> Add New Product
                        </a>
                    </div>
                </div>
                
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> ' . htmlspecialchars($_GET['error']) . '
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                          </div>';
                }
                if (isset($_GET['success'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> ' . htmlspecialchars($_GET['success']) . '
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                          </div>';
                }
                ?>
                
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Product List</h6>
                        <div>
                            <a href="products.php" class="btn btn-sm btn-outline-secondary <?php echo $filter == '' ? 'active' : ''; ?>">All</a>
                            <a href="products.php?filter=low_stock" class="btn btn-sm btn-outline-warning <?php echo $filter == 'low_stock' ? 'active' : ''; ?>">Low Stock</a>
                            <a href="products.php?filter=out_of_stock" class="btn btn-sm btn-outline-danger <?php echo $filter == 'out_of_stock' ? 'active' : ''; ?>">Out of Stock</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Stock</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($products) > 0) {
                                        foreach ($products as $product) {
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
                                                echo '<img src="../' . htmlspecialchars($product['image_path']) . '" alt="' . htmlspecialchars($product['name']) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
                                            } else {
                                                echo '<div style="width: 50px; height: 50px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 5px;"><i class="fa fa-image"></i></div>';
                                            }
                                            echo '</td>';
                                            echo '<td>' . htmlspecialchars($product['name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($product['category']) . '</td>';
                                            echo '<td>Rs ' . $product['price'] . '</td>';
                                            echo '<td>' . $product['discount'] . '%</td>';
                                            echo '<td>' . $stock . '</td>';
                                            echo '<td>' . htmlspecialchars($product['unit']) . '</td>';
                                            echo '<td><span class="badge badge-' . $status_class . '">' . $status_text . '</span></td>';
                                            echo '<td class="table-actions">';
                                            echo '<a href="edit_product.php?id=' . $product['id'] . '" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-edit"></i></a>';
                                            echo '<a href="delete_product.php?id=' . $product['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this product?\')" title="Delete"><i class="fa fa-trash"></i></a>';
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="10" class="text-center">No products found</td></tr>';
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
</body>
</html>
