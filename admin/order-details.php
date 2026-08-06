<?php
require_once("includes/head.php");
require_once("includes/auth_admin.php");
require_once("model/functions.php");
// Ensure order_id is provided
if (!isset($_GET['order_id'])) {
    echo "<script>alert('Invalid request'); window.location.href='orders.php';</script>";
    exit;
}
$order_id = $_GET['order_id'];
$order = getOrderByIdForAdmin($conn, $order_id);
if (!$order) {
    echo "<script>alert('Order not found'); window.location.href='orders.php';</script>";
    exit;
}
// Prepare data
$o = $order[0];
$orderItems = getOrderItemsByOrderId($conn, $o['order_id']);
?>
    <div id="wrapper">
<?php require_once("includes/sidebar.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php require_once("includes/topbar.php"); ?>
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Order Details</h1>
            </div>
            <div class="row">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10 bg-white shadow-sm p-4 rounded d-flex flex-wrap">

                            <!-- Order Meta & Shipping Info -->
                            <div class="col-md-6">
                                <h2 class="fw-bold"><?= htmlspecialchars($o['order_number']) ?></h2>
                                <p class="text-muted mb-2">Order Date: <span class="fw-semibold"><?= htmlspecialchars(date('d M Y, h:i A', strtotime($o['created_at']))) ?></span></p>
                                <p class="text-muted mb-2">Order Status: <span class="fw-semibold text-uppercase"><?= htmlspecialchars($o['order_status']) ?></span></p>
                                <p class="text-muted mb-2">Payment Method: <span class="fw-semibold"><?= $o['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Credit Card' ?></span></p>
                                <?php if ($o['payment_method'] === 'card' && !empty($o['card_last_four'])): ?>
                                    <p class="text-muted mb-2">Card: <span class="fw-semibold">**** **** **** <?= htmlspecialchars($o['card_last_four']) ?></span></p>
                                <?php endif; ?>
                                <p class="text-muted mb-2">Payment Status: <span class="fw-semibold text-uppercase"><?= htmlspecialchars($o['payment_status']) ?></span></p>

                                <h6 class="fw-bold mt-4">Customer:</h6>
                                <p class="text-muted mb-1"><?= htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) ?></p>
                                <p class="text-muted mb-1"><?= htmlspecialchars($o['email']) ?></p>
                                <p class="text-muted mb-1"><?= htmlspecialchars($o['phone']) ?></p>

                                <h6 class="fw-bold mt-4">Shipping Address:</h6>
                                <p class="text-muted"><?= htmlspecialchars($o['address']) ?><br><?= htmlspecialchars($o['city'] . ', ' . $o['state']) ?></p>
                            </div>

                            <!-- Order Items & Totals -->
                            <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                                <h6 class="fw-bold">Items Ordered:</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered text-center align-middle">
                                        <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Size / Color</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($orderItems as $item): ?>
                                            <tr>
                                                <td class="text-start">
                                                    <div class="d-flex align-items-center">
                                                        <img src="uploads/<?= htmlspecialchars($item['product_image']) ?>"
                                                             alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                             style="width:45px; height:45px; object-fit:cover; border-radius:6px; margin-right:10px;">
                                                        <span><?= htmlspecialchars($item['product_name']) ?></span>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($item['size']) ?> / <?= htmlspecialchars($item['color']) ?></td>
                                                <td><?= (int) $item['quantity'] ?></td>
                                                <td><?= number_format($item['unit_price'], 2) ?></td>
                                                <td><?= number_format($item['line_total'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Subtotal</span>
                                    <span class="fw-semibold">PKR <?= number_format($o['subtotal'], 2) ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Shipping</span>
                                    <span class="fw-semibold">PKR <?= number_format($o['shipping_cost'], 2) ?></span>
                                </div>
                                <div class="d-flex justify-content-between pt-2 border-top">
                                    <span class="fw-bold">Total</span>
                                    <span class="fw-bold">PKR <?= number_format($o['total_amount'], 2) ?></span>
                                </div>

                                <!-- Update Order Status -->
                                <div class="mt-4">
                                    <h6 class="fw-bold">Update Order Status:</h6>
                                    <div class="d-flex">
                                        <select class="form-control mr-2" id="order-status-select">
                                            <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $statusOption): ?>
                                                <option value="<?= $statusOption ?>" <?= $o['order_status'] === $statusOption ? 'selected' : '' ?>>
                                                    <?= ucfirst($statusOption) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn btn-success" id="btn-update-status"
                                                data-order-id="<?= htmlspecialchars($o['order_id']) ?>">Update</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require_once("includes/footer.php"); ?>