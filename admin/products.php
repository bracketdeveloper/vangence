<?php require_once("includes/head.php"); ?>
<?php
require_once("model/functions.php");
$allProducts = getAllProducts($conn);
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
                    <h1 class="h3 mb-0 text-gray-800">Products</h1>
                </div>

                <!-- Content Row -->
                <div class="row">
                    <div class="container-fluid">

                        <!-- DataTales Example -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <a href="add-new-product.php" class="btn btn-success btn-icon-split">
                                        <span class="text">Add New Product</span>
                                    </a>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center" id="dataTable" width="100%"
                                           cellspacing="0">
                                        <thead>
                                        <tr>
                                            <th>Product Id</th>
                                            <th>Product Name</th>
                                            <th>Category</th> <th>Price</th>
                                            <th>Product Image</th>
                                            <th>Print Barcode</th>
                                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                                <th>Product Details</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            <?php endif; ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($allProducts as $product):
                                            $images = json_decode($product['image'], true);
                                            $firstImage = !empty($images) ? $images[0] : 'no-image.png';
                                            ?>
                                            <tr>
                                            <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td> <td><?php echo htmlspecialchars($product['selling_price']); ?></td>
                                            <td><img src="uploads/<?php echo htmlspecialchars($firstImage); ?>"
                                                     alt="Product Image"
                                                     style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                                            </td>
                                            <td><button class="btn btn-dark" onclick="printBarcode(
                                                        '<?php echo $product['barcode']; ?>',
                                                        '<?php echo htmlspecialchars($product['product_name']); ?>',
                                                        '<?php echo $product['selling_price']; ?>'
                                                        )">Print</button></td>
                                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                            <td>
                                                <a href="product-details.php?product_id=<?php echo $product['product_id']; ?>"
                                                   class="btn btn-info">Details</a></td>
                                            <td>
                                                <a href="edit-product.php?product_id=<?php echo $product['product_id']; ?>"
                                                   class="btn btn-success">Edit</a></td>
                                            <td>
                                                <a onclick="return validateDeleteProduct('<?php echo $product['product_id']; ?>')"
                                                   class="btn btn-danger">Delete</a></td>
                                            </tr>
                                        <?php endif; ?>
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
