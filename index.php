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
        body {
            background-color: #f9fafb;
        }
    </style>

</head>

<body style="margin-bottom:200px">
  
    <?php
include 'includes/header_menu.php';
include 'includes/check-if-added.php';
?>
    
    <div id="content">
        <div id="bg" class=" ">
            <div class="container" style="padding-top:150px">
            <div class="mx-auto p-5 text-white" id="banner_content" style="border-radius: 0.5rem;" >
            <h1>Fresh Groceries Delivered to Your Door</h1>
            <p>Amazing deals on fresh fruits, vegetables, dairy & beverages daily</p>
            <a href="products.php" class="btn btn-warning btn-lg text-white">Shop Now</a>

            </div>
            </div>

        </div>
    </div>
    <div class="text-center pt-5 pb-3">
        <h2 style="color: #10b981; font-weight: 700;">Shop by Category</h2>
        <p style="color: #6b7280; font-size: 1.1rem;">Browse our fresh selection of groceries</p>
    </div>
    <!--menu highlights start-->
 <div class="container pt-3 pb-5">
        <div class="row text-center">
            <div class="col-6 col-md-3 py-3 category-card">
                <a href="products.php#fruits">
                    <img src="images/fruit.png" class="img-fluid" alt="Fruits">
                    <div class="h5 pt-3 font-weight-bolder">
                      <i class="fa fa-apple text-success"></i> Fruits
                   </div>
                 </a>
             </div>
            <div class="col-6 col-md-3 py-3 category-card">
                <a href="products.php#vegetables">
                  <img src="images/vegetables.png" class="img-fluid" alt="Vegetables">
                     <div class="h5 pt-3 font-weight-bolder">
                        <i class="fa fa-leaf text-success"></i> Vegetables
                     </div>
                  </a>
             </div>
            <div class="col-6 col-md-3 py-3 category-card">
                <a href="products.php#dairy">
                 <img src="images/dairy.png" class="img-fluid" alt="Dairy">
                <div class="h5 pt-3 font-weight-bolder">
                    <i class="fa fa-coffee text-success"></i> Dairy
                 </div>
             </a>
             </div>
            <div class="col-6 col-md-3 py-3 category-card">
                <a href="products.php#beverages">
                 <img src="images/beverages.png" class="img-fluid" alt="Beverages">
                 <div class="h5 pt-3 font-weight-bolder">
                    <i class="fa fa-glass text-success"></i> Beverages
                 </div>
              </div>
            </a>
        </div>
    </div>

    <!--menu highlights end-->
    <!--footer -->
    <?php include 'includes/footer.php'?>
    <!--footer end-->




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