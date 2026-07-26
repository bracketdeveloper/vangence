<?php require_once("includes/head.php"); ?>

<?php
require_once("model/functions.php");
$saleId = isset($_GET['sale_id']) ? (int)$_GET['sale_id'] : 0;
if ($saleId <= 0) {
    die("Invalid sale ID.");
}

$sale = getSaleById($conn, $saleId);
if (!$sale) {
    die("Sale not found.");
}

$items = json_decode($sale['items'], true);
if (!is_array($items)) {
    $items = [];
}
$finalBill = $sale['final_bill'];
$paymentMethod = $sale['payment_method'];
$amountReceived = $sale['amount_received'];
$changeGiven = $sale['change_given'];
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
                    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                </div>
                <div class="container">
                    <h3 class="mb-3">Edit Sale #<?php echo $saleId; ?></h3>
                    <input type="hidden" id="sale-id" value="<?php echo $saleId; ?>">

                    <!-- Scan Input -->
                    <div class="row mb-3">
                        <div class="col-md-8 col-12">
                            <input type="number" id="barcode" class="form-control"
                                   placeholder="Scan or enter at least last 5 digits">
                        </div>
                        <div class="col-md-4 col-12 mt-2 mt-md-0">
                            <button class="btn btn-primary w-100" onclick="validateProductForBillEdit()">Add</button>
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
                            <tbody>
                            <?php foreach ($items as $index => $item): ?>
                                <tr>
                                    <td class="row-num"><?php echo $index + 1; ?></td>
                                    <td hidden class="product-id"><?php echo htmlspecialchars($item['product_id']); ?></td>
                                    <td class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td class="price"><?php echo htmlspecialchars($item['price']); ?></td>
                                    <td class="qty"><?php echo htmlspecialchars($item['qty']); ?></td>
                                    <td class="line-total"><?php echo htmlspecialchars($item['total']); ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" onclick="removeRowEdit(this)">Remove</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Total -->
                    <div class="alert alert-dark mt-3">Final Bill: <span id="final-bill"><?php echo htmlspecialchars($finalBill); ?></span></div>

                    <!-- Payment Method Selection -->
                    <div class="row mb-3">
                        <div class="col-md-6 col-12">
                            <label class="d-block mb-2">Payment Method</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment-method" id="pm-cash" value="cash" <?php echo $paymentMethod === 'cash' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="pm-cash">Cash</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment-method" id="pm-card" value="card" <?php echo $paymentMethod === 'card' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="pm-card">Card</label>
                            </div>
                        </div>
                    </div>

                    <!-- Cash inputs (shown only when cash selected) -->
                    <div id="cash-section" class="row mb-3" style="display:none;">
                        <div class="col-md-4 col-12">
                            <label>Amount Received</label>
                            <input type="number" id="amount-received" class="form-control" min="0" step="0.01" placeholder="0.00"
                                   value="<?php echo htmlspecialchars($amountReceived); ?>">
                        </div>
                        <div class="col-md-4 col-12">
                            <label>Change to Give Back</label>
                            <input type="number" id="change-given" class="form-control" readonly
                                   value="<?php echo htmlspecialchars($changeGiven); ?>">
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div id="action-buttons" class="row" style="display:none;">
                        <div class="col-md-2 col-12 mt-2 mt-md-0">
                            <button class="btn btn-success w-100" id="update-sale-btn" onclick="saveSaleEdit()">Update</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
        <!-- Footer -->
        <?php require_once("includes/footer.php"); ?>
