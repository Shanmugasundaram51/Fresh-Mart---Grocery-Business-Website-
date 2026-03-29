<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fa fa-home"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                    <i class="fa fa-list"></i> All Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : ''; ?>" href="add_product.php">
                    <i class="fa fa-plus-circle"></i> Add Product
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' || basename($_SERVER['PHP_SELF']) == 'order_details.php' ? 'active' : ''; ?>" href="orders.php">
                    <i class="fa fa-shopping-cart"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../index.php" target="_blank">
                    <i class="fa fa-external-link"></i> View Store
                </a>
            </li>
        </ul>
    </div>
</nav>
