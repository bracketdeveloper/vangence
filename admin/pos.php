<?php
require_once("includes/head.php");
require_once("includes/auth_admin.php");
?>

<body id="page-top">
<div id="wrapper">
    <?php require_once("includes/sidebar.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php require_once("includes/topbar.php"); ?>
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Point of Sale</h1>
                </div>

                <!-- Product Scanner Section -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Scan Product</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-10">
                                        <input type="number" class="form-control" id="barcode"
                                               placeholder="Enter barcode or last 5 digits" autofocus>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary btn-block" onclick="validateProductForBill()">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Current Bill</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center" id="bill-table">
                                        <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th hidden>ID</th>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Tax (12%)</th>
                                            <th>Line Total</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Dynamic rows will be added here -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Final Bill Display -->
                                <div class="alert alert-dark d-flex justify-content-between align-items-center mt-4">
                                    <span class="h5 mb-0">Final Bill:</span>
                                    <span id="final-bill" class="h5 mb-0">0</span>
                                </div>

                                <!-- Payment Method Selection -->
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h6 class="font-weight-bold mb-3">Payment Method:</h6>
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-primary active">
                                                <input type="radio" name="payment_method" id="cash" value="cash" checked> Cash
                                            </label>
                                            <label class="btn btn-outline-primary">
                                                <input type="radio" name="payment_method" id="card" value="card"> Card
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cash Payment Section -->
                                <div id="cash-section" class="row mt-4" style="display:none;">
                                    <div class="col-md-6">
                                        <label for="amount-received">Amount Received:</label>
                                        <input type="number" class="form-control" id="amount-received"
                                               placeholder="Enter amount received">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="change-given">Change to Give Back:</label>
                                        <input type="number" class="form-control" id="change-given"
                                               placeholder="0" readonly>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="row mt-4" id="action-buttons" style="display:none;">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-success btn-lg" id="save-bill" onclick="saveBill()">
                                            <i class="fas fa-save"></i> Save Bill
                                        </button>
                                        <button class="btn btn-info btn-lg" id="print-bill" onclick="printBill()">
                                            <i class="fas fa-print"></i> Print Bill
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php require_once("includes/footer.php"); ?>
    </div>
</div>

<script src="js/form-requests.js"></script>
</body>
</html>