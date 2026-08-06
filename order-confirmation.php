<?php
include 'includes/header.php';

// order-confirmation.php lives at the site root, same as checkout.php,
// so functions.php (which sets up $conn) needs the full path — same fix
// as the ajax.php path issue in checkout.
if (!isset($conn)) {
    require_once 'admin/model/functions.php';
}

$orderNumber = isset($_GET['order']) ? mysqli_real_escape_string($conn, $_GET['order']) : '';
$order = null;
$orderItems = [];

if ($orderNumber !== '') {
    $orderQuery = "SELECT * FROM `orders` WHERE `order_number` = '$orderNumber' LIMIT 1";
    $orderResult = mysqli_query($conn, $orderQuery);

    if ($orderResult && mysqli_num_rows($orderResult) > 0) {
        $order = mysqli_fetch_assoc($orderResult);

        $itemsQuery = "SELECT * FROM `order_items` WHERE `order_id` = '" . intval($order['order_id']) . "'";
        $itemsResult = mysqli_query($conn, $itemsQuery);
        if ($itemsResult) {
            while ($row = mysqli_fetch_assoc($itemsResult)) {
                $orderItems[] = $row;
            }
        }
    }
}
?>

    <!-- Page Title Banner -->
    <div class="bg-light py-5 border-bottom border-light">
        <div class="container px-lg-5 text-center">
            <span class="text-uppercase tracking-widest text-muted" style="font-size: 0.8rem; font-weight: 500;">Checkout</span>
            <h1 class="h2 text-uppercase tracking-wider text-navy mt-2 mb-0 fw-bold">
                <?= $order ? 'Order Confirmed' : 'Order Not Found' ?>
            </h1>
            <div class="mx-auto mt-3" style="width: 50px; height: 1.5px; background-color: var(--color-navy);"></div>
        </div>
    </div>

    <div class="container-fluid px-lg-5 py-5">

        <?php if (!$order) { ?>

            <div class="text-center py-5 my-5">
                <div class="mb-4"><i class="fa-solid fa-circle-exclamation text-navy" style="font-size: 4rem; opacity: 0.2;"></i></div>
                <h2 class="h4 mb-3 tracking-wide text-uppercase">We couldn't find that order</h2>
                <p class="text-muted mb-4">Double check the link, or contact us if you believe this is a mistake.</p>
                <a href="shop.php" class="btn btn-navy px-4 py-3">Continue Shopping</a>
            </div>

        <?php } else { ?>

            <div class="row g-5">
                <!-- Left Side: Confirmation & Shipping Details -->
                <div class="col-lg-7">

                    <div class="p-4 p-lg-5 border border-navy-light mb-4 text-center">
                        <i class="fa-solid fa-circle-check text-navy mb-3" style="font-size: 3rem;"></i>
                        <h3 class="h5 text-uppercase tracking-wider text-navy mb-2 fw-bold">Thank you, <?= htmlspecialchars($order['first_name']) ?>!</h3>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">
                            Your order has been placed successfully.
                            <?php if ($order['payment_method'] === 'cod') { ?>
                                Please keep the total amount ready for payment on delivery.
                            <?php } else { ?>
                                A confirmation has been sent to your email.
                            <?php } ?>
                        </p>
                    </div>

                    <div class="p-4 p-lg-5 border border-navy-light">
                        <h3 class="h5 text-uppercase tracking-wider text-navy mb-4 fw-bold">Order Details</h3>

                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                            <span class="text-muted">Order Number</span>
                            <span class="text-navy fw-semibold"><?= htmlspecialchars($order['order_number']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                            <span class="text-muted">Order Date</span>
                            <span class="text-navy fw-medium"><?= htmlspecialchars(date('d M Y, h:i A', strtotime($order['created_at']))) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                            <span class="text-muted">Payment Method</span>
                            <span class="text-navy fw-medium">
                            <?= $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Credit Card' ?>
                                <?php if ($order['payment_method'] === 'card' && $order['card_last_four']) { ?>
                                    &bull; ending in <?= htmlspecialchars($order['card_last_four']) ?>
                                <?php } ?>
                        </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                            <span class="text-muted">Order Status</span>
                            <span class="text-navy fw-medium text-uppercase"><?= htmlspecialchars($order['order_status']) ?></span>
                        </div>

                        <hr class="border-navy-light my-4">

                        <h3 class="h5 text-uppercase tracking-wider text-navy mb-4 fw-bold">Shipping To</h3>
                        <p class="mb-1 fw-semibold text-navy" style="font-size: 0.9rem;">
                            <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?>
                        </p>
                        <p class="mb-1 text-muted" style="font-size: 0.85rem;"><?= htmlspecialchars($order['address']) ?></p>
                        <p class="mb-1 text-muted" style="font-size: 0.85rem;"><?= htmlspecialchars($order['city'] . ', ' . $order['state']) ?></p>
                        <p class="mb-1 text-muted" style="font-size: 0.85rem;"><?= htmlspecialchars($order['phone']) ?></p>
                        <p class="mb-0 text-muted" style="font-size: 0.85rem;"><?= htmlspecialchars($order['email']) ?></p>
                    </div>

                </div>

                <!-- Right Side: Order Summary Panel -->
                <div class="col-lg-5">
                    <div class="cart-summary-box bg-light border-0 p-4 p-lg-5 h-100">
                        <h3 class="h5 pb-3 mb-4 border-bottom border-navy tracking-wider text-uppercase fw-bold">Order Summary</h3>

                        <div class="d-flex flex-column gap-3 mb-4" style="max-height: 350px; overflow-y: auto;">
                            <?php foreach ($orderItems as $item) { ?>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 50px; height: 65px; overflow: hidden; border: 1px solid var(--color-navy-light);">
                                            <img src="admin/uploads/<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div>
                                            <h4 class="h6 mb-1 text-navy" style="font-size: 0.8rem; font-weight: 600; max-width: 180px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                                <?= htmlspecialchars($item['product_name']) ?>
                                            </h4>
                                            <span class="text-muted d-block" style="font-size: 0.75rem;">
                                            Size: <?= htmlspecialchars($item['size']) ?> | Color: <?= htmlspecialchars($item['color']) ?> | Qty: <?= (int) $item['quantity'] ?>
                                        </span>
                                        </div>
                                    </div>
                                    <span class="text-navy fw-semibold" style="font-size: 0.8rem;"><?= number_format($item['line_total'], 2) ?></span>
                                </div>
                            <?php } ?>
                        </div>

                        <hr class="border-navy-light my-4">

                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                            <span>Subtotal</span>
                            <span class="text-navy fw-medium">PKR <?= number_format($order['subtotal'], 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                            <span>Shipping</span>
                            <span class="text-navy fw-medium">PKR <?= number_format($order['shipping_cost'], 2) ?></span>
                        </div>

                        <div class="d-flex justify-content-between py-3 border-top border-navy border-2 mb-4">
                            <span class="fw-semibold">Total Paid</span>
                            <span class="text-navy fw-bold h5 mb-0">PKR <?= number_format($order['total_amount'], 2) ?></span>
                        </div>

                        <a href="shop.php" class="btn btn-navy w-100 py-3 text-uppercase fw-bold">Continue Shopping</a>
                    </div>
                </div>
            </div>

        <?php } ?>

    </div>

<?php include 'includes/footer.php'; ?>