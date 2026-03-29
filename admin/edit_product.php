<?php
session_start();
require 'includes/admin_common.php';
check_admin_login();

if (!isset($_GET['id'])) {
    header('Location: products.php?error=Invalid product ID');
    exit();
}

$product_id = $_GET['id'];
$product = get_product_by_id($product_id);

if (!$product) {
    header('Location: products.php?error=Product not found');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Admin Panel</title>
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
                    <h1 class="h2">Edit Product</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="products.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Products
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
                ?>
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                            </div>
                            <div class="card-body">
                                <form action="edit_product_process.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    
                                    <div class="form-group">
                                        <label for="product_name">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="product_name" name="product_name" required value="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="category">Category <span class="text-danger">*</span></label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Fruits" <?php echo $product['category'] == 'Fruits' ? 'selected' : ''; ?>>Fruits</option>
                                            <option value="Vegetables" <?php echo $product['category'] == 'Vegetables' ? 'selected' : ''; ?>>Vegetables</option>
                                            <option value="Dairy" <?php echo $product['category'] == 'Dairy' ? 'selected' : ''; ?>>Dairy</option>
                                            <option value="Beverages" <?php echo $product['category'] == 'Beverages' ? 'selected' : ''; ?>>Beverages</option>
                                        </select>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="price">Price (Rs) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="price" name="price" required min="0" step="1" value="<?php echo $product['price']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discount">Discount (%) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="discount" name="discount" required min="0" max="100" value="<?php echo $product['discount']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="stock_quantity">Stock Quantity <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required min="0" value="<?php echo $product['stock_quantity']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="unit">Unit <span class="text-danger">*</span></label>
                                                <select class="form-control" id="unit" name="unit" required>
                                                    <option value="">Select Unit</option>
                                                    <option value="kg" <?php echo $product['unit'] == 'kg' ? 'selected' : ''; ?>>Kilogram (kg)</option>
                                                    <option value="liter" <?php echo $product['unit'] == 'liter' ? 'selected' : ''; ?>>Liter</option>
                                                    <option value="piece" <?php echo $product['unit'] == 'piece' ? 'selected' : ''; ?>>Piece</option>
                                                    <option value="pack" <?php echo $product['unit'] == 'pack' ? 'selected' : ''; ?>>Pack</option>
                                                    <option value="dozen" <?php echo $product['unit'] == 'dozen' ? 'selected' : ''; ?>>Dozen</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Current Image</label>
                                        <div>
                                            <?php if ($product['image_path']) { ?>
                                                <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image-preview">
                                            <?php } else { ?>
                                                <p class="text-muted">No image available</p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="product_image">Update Product Image (Optional)</label>
                                        <input type="file" class="form-control-file" id="product_image" name="product_image" accept="image/*" onchange="previewImage(this)">
                                        <small class="form-text text-muted">Leave empty to keep current image. Allowed formats: JPG, PNG, GIF, WEBP. Max size: 5MB</small>
                                        <img id="image_preview" class="product-image-preview" style="display: none;">
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Update Product
                                        </button>
                                        <a href="products.php" class="btn btn-secondary">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                    </div>
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
    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image_preview').src = e.target.result;
                document.getElementById('image_preview').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>
