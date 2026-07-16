<?php require_once("includes/head.php"); ?>
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
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            </div>
            <div class="container">
                <h3 class="mb-3">POS</h3>
                <!-- Scan Input -->
                <div class="row mb-3">
                    <div class="col-md-8 col-12">
                        <input type="number" id="barcode" class="form-control"
                               placeholder="Scan or enter at least last 5 digits" autofocus>
                    </div>
                    <div class="col-md-4 col-12 mt-2 mt-md-0">
                        <button class="btn btn-primary w-100" onclick="validateProductForBill()">Add</button>
                    </div>
                </div>
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="bill-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th hidden>ID</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Line Total</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <!-- Total -->
                <div class="alert alert-dark mt-3" >Final Bill: <span id="final-bill">0</span></div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2 mt-md-0">
                        <button class="btn btn-success w-100" id="save-bill">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->
    <!-- Footer -->
<?php require_once("includes/footer.php"); ?>