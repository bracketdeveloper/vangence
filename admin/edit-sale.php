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
$finalBill      = $sale['final_bill'];
$paymentMethod  = $sale['payment_method'];
$amountReceived = $sale['amount_received'];
$changeGiven    = $sale['change_given'];
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
                            <input type="number" id="edit-barcode" class="form-control"
                                   placeholder="Scan or enter at least last 5 digits">
                        </div>
                        <div class="col-md-4 col-12 mt-2 mt-md-0">
                            <button class="btn btn-primary w-100" onclick="validateProductForBillEdit()">Add</button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered text-center" id="bill-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th hidden>ID</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Discount %</th>
                                <th>Discount Amt</th>
                                <th>Tax (12%)</th>
                                <th>Line Total</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $index => $item):
                                $discountPercent = isset($item['discount_percent']) ? floatval($item['discount_percent']) : 0;
                                $discountAmount  = isset($item['discount_amount'])  ? floatval($item['discount_amount'])  : 0;
                                $tax             = isset($item['tax'])              ? floatval($item['tax'])              : 0;
                                $total           = isset($item['total'])            ? floatval($item['total'])            : 0;
                                // Recalculate for old bills missing tax/total
                                if ($tax == 0 && $total == 0) {
                                    $subtotal       = floatval($item['price']) * intval($item['qty']);
                                    $discountAmount = $subtotal * ($discountPercent / 100);
                                    $taxable        = $subtotal - $discountAmount;
                                    $tax            = $taxable * 0.12;
                                    $total          = $taxable + $tax;
                                }
                                ?>
                                <tr data-barcode="<?php echo htmlspecialchars($item['product_id']); ?>"
                                    data-stock="9999"
                                    data-discount="<?php echo $discountPercent; ?>">
                                    <td class="row-num"><?php echo $index + 1; ?></td>
                                    <td hidden class="product-id"><?php echo htmlspecialchars($item['product_id']); ?></td>
                                    <td class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td class="price"><?php echo number_format(floatval($item['price']), 2, '.', ''); ?></td>
                                    <td class="qty"><?php echo intval($item['qty']); ?></td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control discount-percent"
                                                   min="1" max="25" placeholder="0"
                                                   value="<?php echo $discountPercent > 0 ? $discountPercent : ''; ?>">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary apply-discount-edit"
                                                        type="button">Apply</button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="discount-amt"><?php echo number_format($discountAmount, 2, '.', ''); ?></td>
                                    <td class="tax"><?php echo number_format($tax, 2, '.', ''); ?></td>
                                    <td class="line-total"><?php echo number_format($total, 2, '.', ''); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary increase-edit">+</button>
                                        <button class="btn btn-sm btn-secondary decrease-edit">-</button>
                                        <button class="btn btn-sm btn-danger remove-edit">Remove</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total -->
                    <div class="alert alert-dark mt-3">
                        Final Bill: <span id="final-bill"><?php echo htmlspecialchars($finalBill); ?></span>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="row mb-3">
                        <div class="col-md-6 col-12">
                            <label class="d-block mb-2 font-weight-bold">Payment Method</label>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary">
                                    <input
                                            class="form-check-input"
                                            type="radio"
                                            name="payment-method"
                                            id="pm-cash"
                                            value="cash"
                                    > Cash
                                </label>
                                <label class="btn btn-outline-success">
                                    <input
                                            class="form-check-input"
                                            type="radio"
                                            name="payment-method"
                                            id="pm-card"
                                            value="card"
                                    > Card
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Cash inputs (shown only when cash selected) -->
                    <div id="cash-section" class="row mb-3" style="display:none;">
                        <div class="col-md-4 col-12">
                            <label>Amount Received</label>
                            <input type="number" id="amount-received" class="form-control"
                                   min="0" step="0.01" placeholder="0.00"
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
