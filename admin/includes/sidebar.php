<?php
$userRole = 'user';
if (isset($_SESSION)) {
    $userRole = $_SESSION["role"];
}
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-text mx-3">Vangence Admin</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="categories.php">
            <i class="fas fa-fw fa-box"></i>
            <span>Categories</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="add-new-category.php">
            <i class="fas fa-fw fa-plus"></i>
            <span>Add new category</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="products.php">
            <i class="fas fa-fw fa-box"></i>
            <span>Products</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="add-new-product.php">
            <i class="fas fa-fw fa-plus"></i>
            <span>Add new product</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="sales.php">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Sales</span></a>
    </li>
    <?php if ($userRole === "admin"): ?>
        <li class="nav-item active">
            <a class="nav-link" href="users.php">
                <i class="fas fa-fw fa-users"></i>
                <span>Users</span></a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="add-new-user.php">
                <i class="fas fa-fw fa-user-plus"></i>
                <span>Register new user</span></a>
        </li>
    <?php endif; ?>
    <li class="nav-item active">
        <a class="nav-link" href="settings.php">
            <i class="fas fa-fw fa-tools"></i>
            <span>Settings</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
            Logout
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

</ul>
