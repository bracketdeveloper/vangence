<?php require_once("includes/head.php"); ?>

<?php require_once("includes/auth_admin.php"); ?>
<?php
require_once("model/functions.php");
$sale_id = $_GET['sale_id'];
$sale = getSaleById($conn, $sale_id);
if ($sale == null) {
    echo "<script>alert(`Invalid request`);
          window.location.href= 'sales.php'</script>";
}
$items = $sale[0]['items'];
$items = json_decode($items, true);
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
                    <h1 class="h3 mb-0 text-gray-800">Sale Details</h1>
                </div>

                <!-- Content Row -->
                <div class="row">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card mb-4">
                            <!-- Account -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center" id="bill-table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Line Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1;
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $item['product_name']; ?></td>
                                                <td><?php echo $item['price']; ?></td>
                                                <td><?php echo $item['qty']; ?></td>
                                                <td><?php echo $item['total']; ?></td>
                                            </tr>
                                            <?php $i++; endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Total -->
                                <div class="alert alert-dark mt-3">Final Bill: <span
                                            id="final-bill"><?php echo $sale[0]['final_bill']; ?></span></div>
                                <div class="row">
                                    <div class="col-md-4 col-12 mt-2 mt-md-0">
                                        <button class="btn btn-dark w-100"
                                                onclick='printBill(<?php echo json_encode($items); ?>, <?php echo $sale[0]["final_bill"]; ?>)'>
                                            Print
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- /Account -->
                        </div>

                    </div>
                </div>

                <!-- Content Row -->

                <div class="row">

                </div>

                <!-- Content Row -->
                <div class="row">


                </div>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <?php require_once("includes/footer.php"); ?>
