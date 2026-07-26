<?php require_once("includes/head.php"); ?>
<?php
require_once("model/functions.php");
$allSales = getAllSales($conn);
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
                    <h1 class="h3 mb-0 text-gray-800">Sales</h1>
                </div>
                <!-- Content Row -->
                <div class="row">
                    <div class="container-fluid">
                        <!-- DataTales Example -->
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center" id="dataTable" width="100%"
                                           cellspacing="0">
                                        <thead>
                                        <tr>
                                            <th>Bill #</th>
                                            <th>Sr#</th>
                                            <th>Total</th>
                                            <th>Items</th>
                                            <th>Payment</th>
                                            <th>Date & Time</th>
                                            <th>Details</th>
                                            <th>Edit</th>
                                            <th>Print</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($allSales as $sale):
                                            $items = $sale['items'];
                                            $i++;
                                            $items = json_decode($items, true);
                                            ?>
                                            <tr>
                                                <td><?php echo $i ?></td>
                                                <td><?php echo $sale['sale_id']; ?></td>
                                                <td><?php echo $sale['final_bill'] ?></td>
                                                <td><?php echo is_array($items) ? count($items) : 0; ?></td>
                                                <td><?php echo isset($sale['payment_method']) ? ucfirst($sale['payment_method']) : '-'; ?></td>
                                                <td><?php echo date('d/m/Y h:i:s a', strtotime($sale['created_at'])) ?></td>
                                                <td>
                                                    <a href="sale-details.php?sale_id=<?php echo $sale['sale_id']; ?>"
                                                       class="btn btn-success">Details</a></td>
                                                <td>
                                                    <a href="edit-sale.php?sale_id=<?php echo $sale['sale_id']; ?>"
                                                       class="btn btn-warning">Edit</a></td>
                                                <td>
                                                    <button class="btn btn-info"
                                                            onclick='printBill({
                                                                    sale_id: <?php echo $sale["sale_id"]; ?>,
                                                                    items: <?php echo json_encode($items); ?>,
                                                                    final_bill: <?php echo $sale["final_bill"]; ?>,
                                                                    payment_method: <?php echo json_encode(isset($sale["payment_method"]) ? $sale["payment_method"] : null); ?>,
                                                                    amount_received: <?php echo json_encode(isset($sale["amount_received"]) ? $sale["amount_received"] : null); ?>,
                                                                    change_given: <?php echo json_encode(isset($sale["change_given"]) ? $sale["change_given"] : null); ?>
                                                                    })'>
                                                        Print
                                                    </button>
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
