<?php
function render_product_card($product_data, $image_name = null) {
    if (is_numeric($product_data)) {
        $product = get_product_details($product_data);
        if (!$product) return;
    } else {
        $product = $product_data;
    }
    
    $stock = $product['stock_quantity'];
    $product_id = $product['id'];
    $is_added = check_if_added_to_cart($product_id);
    
    $image_src = $product['image_path'] ? $product['image_path'] : 'images/' . $image_name;
    ?>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6 mb-4">
        <div class="card product-card-modern h-100">
            <div class="product-image-container">
                <img src="<?php echo $image_src; ?>" alt="<?php echo $product['name']; ?>" class="card-img-top product-img">
                <?php if ($product['discount'] > 0) { ?>
                    <div class="discount-badge-corner">
                        <i class="fa fa-tag"></i> <?php echo $product['discount']; ?>% OFF
                    </div>
                <?php } ?>
            </div>
            <div class="card-body d-flex flex-column">
                <h6 class="product-title"><?php echo $product['name']; ?></h6>
                <div class="product-price-section">
                    <div class="price-display">Rs <?php echo $product['price']; ?></div>
                    <div class="price-unit">per <?php echo $product['unit']; ?></div>
                </div>
                
                <div class="stock-badge-wrapper mt-2 mb-3">
                    <?php if (is_out_of_stock($stock)) { ?>
                        <span class="badge badge-danger">
                            <i class="fa fa-times-circle"></i> Out of Stock
                        </span>
                    <?php } elseif (is_stock_low($stock)) { ?>
                        <span class="badge badge-warning text-dark">
                            <i class="fa fa-exclamation-triangle"></i> Only <?php echo $stock; ?> left
                        </span>
                    <?php } else { ?>
                        <span class="badge badge-success">
                            <i class="fa fa-check-circle"></i> In Stock
                        </span>
                    <?php } ?>
                </div>
                
                <div class="mt-auto">
                    <?php if (!isset($_SESSION['email'])) { ?>
                        <a href="index.php#login" role="button" class="btn btn-warning btn-block text-white">
                            <i class="fa fa-shopping-cart"></i> Add To Cart
                        </a>
                    <?php } else { ?>
                        <?php if (is_out_of_stock($stock)) { ?>
                            <button class="btn btn-secondary btn-block text-white" disabled>
                                <i class="fa fa-ban"></i> Out of Stock
                            </button>
                        <?php } else { ?>
                            <div class="qty-selector-inline mb-2">
                                <label class="qty-label-inline">Qty:</label>
                                <input type="number" id="qty_<?php echo $product_id; ?>" class="form-control form-control-sm qty-input-inline" value="1" min="1" max="<?php echo $stock; ?>">
                            </div>
                            <a href="#" onclick="addToCart(<?php echo $product_id; ?>, <?php echo $stock; ?>); return false;" class="btn btn-warning btn-block text-white">
                                <i class="fa fa-shopping-cart"></i> <?php echo $is_added ? 'Add More' : 'Add to Cart'; ?>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
