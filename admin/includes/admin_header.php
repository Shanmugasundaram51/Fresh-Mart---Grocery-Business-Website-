<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="dashboard.php">
        <i class="fa fa-shopping-cart"></i> Fresh Mart Admin
    </a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="navbar-nav px-3 ml-auto">
        <li class="nav-item text-nowrap">
            <span class="navbar-text mr-3">
                <i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
            </span>
            <a class="btn btn-sm btn-outline-light" href="logout.php">
                <i class="fa fa-sign-out"></i> Logout
            </a>
        </li>
    </ul>
</nav>
