<?php require_once("includes/head.php"); ?>
<?php
require_once("model/functions.php");
$allOrders = getAllOrdersForAdmin($conn);
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
                <h1 class="h3 mb-0 text-gray-800">Orders</h1>
            </div>
            <!-- Content Row -->
            <div class="row">
                <div class="container-fluid">
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Order List</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" id="dataTable" width="100%"
                                       cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Payment Method</th>
                                        <th>Total</th>
                                        <th>Order Status</th>
                                        <th>Order Date</th>
                                        <th>Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($allOrders as $order):
                                        $statusClass = 'badge-secondary';
                                        switch ($order['order_status']) {
                                            case 'pending':
                                                $statusClass = 'badge-warning';
                                                break;
                                            case 'processing':
                                                $statusClass = 'badge-info';
                                                break;
                                            case 'shipped':
                                                $statusClass = 'badge-primary';
                                                break;
                                            case 'delivered':
                                                $statusClass = 'badge-success';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'badge-danger';
                                                break;
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                            <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($order['phone']); ?></td>
                                            <td><?php echo $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Credit Card'; ?></td>
                                            <td>PKR <?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td><span class="badge <?php echo $statusClass; ?> text-uppercase"><?php echo htmlspecialchars($order['order_status']); ?></span></td>
                                            <td><?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($order['created_at']))); ?></td>
                                            <td>
                                                <a href="order-details.php?order_id=<?php echo $order['order_id']; ?>"
                                                   class="btn btn-info">Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
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