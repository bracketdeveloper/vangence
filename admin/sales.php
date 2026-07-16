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
                                        <th>Sr#</th>
                                        <th>Total</th>
                                        <th>Date & Time</th>
                                        <th>Details</th>
                                        <th>Print</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($allSales as $sale):
                                        $items = $sale['items'];
                                        $items = json_decode($items, true);
                                        $i++;
                                        ?>
                                        <tr>
                                            <td><?php echo $i ?></td>
                                            <td><?php echo $sale['final_bill'] ?></td>
                                            <td><?php echo date('d/m/Y h:i:s a', strtotime($sale['created_at'])) ?></td>
                                            <td>
                                                <a href="sale-details.php?sale_id=<?php echo $sale['sale_id']; ?>"
                                                   class="btn btn-success">Details</a></td>
                                            <td>
                                                <button class="btn btn-info"
                                                        onclick='printBill(<?php echo json_encode($items); ?>, <?php echo $sale["final_bill"]; ?>)'>
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