<?php
require_once 'includes/head.php';
require_once 'includes/auth_admin.php';
require_once 'model/functions.php';

$sale_id = $_GET['sale_id'] ?? null;
$sale = getSaleById($conn, $sale_id);

if ($sale == null) {
    echo "<script>alert('Sale not found!'); window.location.href = 'sales.php';</script>";
    exit;
}

$items = json_decode($sale['items'], true);
?>

<body id="page-top">
<div id="wrapper">
    <?php require_once 'includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php require_once 'includes/topbar.php'; ?>
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Sale Details</h1>
                    <a href="sales.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Back to Sales
                    </a>
                </div>

                <!-- Content Row -->
                <div class="row">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h5 class="m-0 font-weight-bold text-primary">Bill #<?php echo $sale['sale_id']; ?></h5>
                            </div>
                            <div class="card-body">

                                <!-- Sale Meta Information - Row 1 -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <strong>Bill Number:</strong><br>
                                        <span class="text-muted">#<?php echo $sale['sale_id']; ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Date & Time:</strong><br>
                                        <span class="text-muted">
                                                <?php echo date('d/m/Y h:i:s a', strtotime($sale['created_at'])); ?>
                                            </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Payment Method:</strong><br>
                                        <span class="text-muted">
                                                <?php echo isset($sale['payment_method']) ? ucfirst($sale['payment_method']) : '-'; ?>
                                            </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Created By:</strong><br>
                                        <span class="text-muted">
                                                <?php echo isset($sale['created_by']) ? ucfirst($sale['created_by']) : '-'; ?>
                                            </span>
                                    </div>
                                </div>

                                <!-- Sale Meta Information - Row 2 -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <strong>Amount Received:</strong><br>
                                        <span class="text-muted">
                                                <?php echo isset($sale['amount_received']) ? number_format($sale['amount_received'], 2) : '0.00'; ?>
                                            </span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Change Given:</strong><br>
                                        <span class="text-muted">
                                                <?php echo isset($sale['change_given']) ? number_format($sale['change_given'], 2) : '0.00'; ?>
                                            </span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Final Bill:</strong><br>
                                        <span class="text-success font-weight-bold">
                                                <?php echo number_format($sale['final_bill'], 2); ?>
                                            </span>
                                    </div>
                                </div>

                                <hr>

                                <!-- Itemized Table -->
                                <h6 class="font-weight-bold mb-3">Items Purchased:</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Product ID</th>
                                            <th>Product Name</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Line Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $i = 1;
                                        $subtotal = 0;
                                        foreach ($items as $item):
                                            $subtotal += $item['total'];
                                            ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo $item['product_id']; ?></td>
                                                <td class="text-left"><?php echo $item['product_name']; ?></td>
                                                <td><?php echo number_format($item['price'], 2); ?></td>
                                                <td><?php echo $item['qty']; ?></td>
                                                <td><?php echo number_format($item['total'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                        <tr>
                                            <td colspan="5" class="text-right font-weight-bold">Subtotal</td>
                                            <td class="font-weight-bold"><?php echo number_format($subtotal, 2); ?></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Final Bill Summary -->
                                <div class="alert alert-dark d-flex justify-content-between align-items-center mt-4" role="alert">
                                    <span class="h5 mb-0">Final Bill:</span>
                                    <span id="final-bill-display" class="h5 mb-0"><?php echo number_format($sale['final_bill'], 2); ?></span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <button onclick='printBill(<?php echo json_encode([
                                                "sale_id" => $sale['sale_id'],
                                                "items" => $items,
                                                "subtotal" => $subtotal,
                                                "final_bill" => $sale['final_bill'],
                                                "payment_method" => $sale['payment_method'] ?? '',
                                                "amount_received" => $sale['amount_received'] ?? 0,
                                                "change_given" => $sale['change_given'] ?? 0,
                                                "created_at" => $sale['created_at'],
                                                "created_by" => $sale['created_by'] ?? ''
                                        ]); ?>)' class="btn btn-success">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                        <a href="edit-sale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php require_once 'includes/footer.php'; ?>
    </div>
</div>
</body>
</html>