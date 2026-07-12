<?php require_once ("includes/head.php"); ?>
<?php
  require_once ("model/functions.php");
  $categories = getAllCategories($conn);
?>

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
          <?php require_once ("includes/sidebar.php"); ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                  <?php require_once ("includes/topbar.php"); ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Add New Product</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Product Name</label>
                                            <input class="form-control" type="text" id="product-name" autofocus/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Category</label>
                                            <select id="category-id" class="select2 form-select form-control">
                                                <option value="" disabled selected>Select Category</option>
                                                <?php foreach ($categories as $category):?>
                                                    <option value="<?php echo $category['category_id'];?>">
                                                        <?php echo $category['category'];?>
                                                    </option>
                                                <?php endForeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Selling Price</label>
                                            <input type="number" min="1" class="form-control" id="selling-price"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" class="form-control" id="qty"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Sizes (comma separated)</label>
                                            <input type="text" class="form-control" id="sizes" placeholder="S, M, L, XL"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Colors (comma separated)</label>
                                            <input type="text" class="form-control" id="colors" placeholder="Navy, White"/>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Images</label>
                                            <input type="file" multiple class="form-control" id="images" accept="image/*"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" id="description" rows="3"></textarea>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Product Details</label>
                                            <textarea class="form-control" id="details" rows="3" placeholder="Materials, Care instructions..."></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" onclick="return validateNewProduct()" class="btn btn-primary me-2">Add Product</button>
                                        <a class="btn btn-success" href="products.php">All Products</a>
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
              <?php require_once ("includes/footer.php"); ?>
