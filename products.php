<?php
session_start();
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
    <style>
        .category-section {
            margin-bottom: 50px;
            padding: 0 10px;
        }
        
        .category-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 3px solid #10b981;
        }
        
        .row {
            margin-left: -5px;
            margin-right: -5px;
            display: flex;
            flex-wrap: wrap;
        }
        
        .row > [class*='col-'] {
            padding-left: 5px;
            padding-right: 5px;
            margin-bottom: 15px;
        }
        
        .category-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        
        .category-info h3 {
            color: #1f2937;
            font-weight: 700;
            margin: 0 0 4px 0;
            font-size: 1.5rem;
        }
        
        .category-info p {
            color: #6b7280;
            font-size: 0.9rem;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .category-header {
                margin-bottom: 20px;
            }
            
            .category-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            
            .category-info h3 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
<!--header -->
 <?php
include 'includes/common.php';
include 'includes/header_menu.php';
include 'includes/check-if-added.php';
include 'includes/product_card.php';
?>
<!--header ends -->

<div class="container-fluid" style="max-width: 100%; padding: 0; margin-top: 80px; margin-bottom: 100px;">
         <!--Alert messages-->
         <?php
         if (isset($_GET['error'])) {
             $error = $_GET['error'];
             if ($error == 'out_of_stock') {
                 echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                         <strong>Out of Stock!</strong> This product is currently unavailable.
                         <button type="button" class="close" data-dismiss="alert">&times;</button>
                       </div>';
             } elseif ($error == 'insufficient_stock') {
                 $available = isset($_GET['available']) ? $_GET['available'] : 0;
                 echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                         <strong>Insufficient Stock!</strong> Only ' . $available . ' units available.
                         <button type="button" class="close" data-dismiss="alert">&times;</button>
                       </div>';
             } elseif ($error == 'invalid_quantity') {
                 echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                         <strong>Invalid Quantity!</strong> Please enter a valid quantity (minimum 1).
                         <button type="button" class="close" data-dismiss="alert">&times;</button>
                       </div>';
             }
         }
         if (isset($_GET['success']) && $_GET['success'] == 'added') {
             echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                     <strong>Success!</strong> Item added to cart.
                     <button type="button" class="close" data-dismiss="alert">&times;</button>
                   </div>';
         }
         ?>
         
        <!--breadcrumb start-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Products</li>
            </ol>
        </nav>
        <!--breadcrumb end-->
    <!--menu list-->
    <?php
    $query = "SELECT * FROM products ORDER BY category, id";
    $result = mysqli_query($con, $query);
    $products_by_category = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $category = $row['category'] ? $row['category'] : 'Other';
            if (!isset($products_by_category[$category])) {
                $products_by_category[$category] = [];
            }
            $products_by_category[$category][] = $row;
        }
    }
    
    $category_config = [
        'Fruits' => ['icon' => 'fa-apple', 'title' => 'Fresh Fruits', 'description' => 'Handpicked fresh fruits delivered daily'],
        'Vegetables' => ['icon' => 'fa-leaf', 'title' => 'Fresh Vegetables', 'description' => 'Farm-fresh vegetables for healthy living'],
        'Dairy' => ['icon' => 'fa-coffee', 'title' => 'Dairy Products', 'description' => 'Fresh dairy products from trusted sources'],
        'Beverages' => ['icon' => 'fa-glass', 'title' => 'Beverages', 'description' => 'Refreshing drinks for every occasion']
    ];
    
    foreach ($category_config as $category => $config) {
        if (isset($products_by_category[$category]) && count($products_by_category[$category]) > 0) {
            echo '<div class="category-section">';
            echo '<div class="category-header">';
            echo '<div class="category-icon"><i class="fa ' . $config['icon'] . '"></i></div>';
            echo '<div class="category-info">';
            echo '<h3>' . $config['title'] . '</h3>';
            echo '<p>' . $config['description'] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '<div class="row" id="' . strtolower($category) . '">';
            
            foreach ($products_by_category[$category] as $product) {
                render_product_card($product);
            }
            
            echo '</div>';
            echo '</div>';
        }
    }
    ?>
    
      </div>
      <!--menu list ends-->
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
</script>
<script>
function addToCart(productId, maxStock) {
    var qty = parseInt(document.getElementById('qty_' + productId).value);
    
    if (qty < 1) {
        alert('Quantity must be at least 1');
        return false;
    }
    
    if (qty > maxStock) {
        alert('Only ' + maxStock + ' units available in stock');
        return false;
    }
    
    window.location.href = 'addtocart.php?id=' + productId + '&qty=' + qty;
    return false;
}
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