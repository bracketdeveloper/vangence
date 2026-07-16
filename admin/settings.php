<?php require_once("includes/head.php"); ?>
<?php
$name = ucwords(strtolower($_SESSION["name"]));
$email = $_SESSION["email"];
$role = $_SESSION["role"];
?>
    <!-- Page Wrapper -->
    <div id="wrapper">
    <!-- Sidebar -->
<?php require_once("includes/sidebar.php"); ?>
    <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
    <div id="content">
        <!-- Topbar -->
        <?php require_once("includes/topbar.php"); ?>
        <!-- End of Topbar -->
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Settings</h1>
            </div>
            <!-- Content Row -->
            <div class="row">
                <!-- Account Details Section -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Account Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Name</label>
                                    <input class="form-control" type="text" disabled value="<?php echo $name; ?>"/>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Email</label>
                                    <input class="form-control" type="text" disabled value="<?php echo $email; ?>"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Role</label>
                                    <input class="form-control" type="text" disabled value="<?php echo $role; ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Change Password Section -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Current Password</label>
                                    <input class="form-control" type="password" id="current-password" required/>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input class="form-control" type="password" id="new-password" required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input class="form-control" type="password" id="confirm-password" required/>
                                </div>
                                <div class="mb-3 col-md-6 d-flex align-items-end">
                                    <input type="button" onclick="return validateChangePassword()"
                                           class="btn btn-primary btn-block" value="Change Password">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Pages Section -->
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Edit Website Pages</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex justify-content-between align-items-center border p-3 rounded">
                                        <span><strong>Home Page</strong></span>
                                        <a href="edit_home_page.php" class="btn btn-sm btn-outline-primary" target="_blank">Edit</a>
                                    </div>
                                </div>
                                <!-- Add more items here following the same structure -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->
    <!-- Footer -->
<?php require_once("includes/footer.php"); ?>