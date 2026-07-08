<?php require_once("includes/head.php"); ?>
<?php require_once ("includes/auth_admin.php"); ?>
<?php
require_once("model/functions.php");
$product_id = $_GET['product_id'];
$product = getProductById($conn, $product_id);
if ($product == null) {
    echo "<script>alert(`Invalid request`);
          window.location.href= 'products.php'</script>";
}
$images = json_decode($product[0]['image'], true);
$categories = getAllCategories($conn);
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
                    <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
                </div>

                <!-- Content Row -->
                <div class="row">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card mb-4">
                            <!-- Account -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Product Name</label>
                                        <input class="form-control" type="text" id="edit-product-name"
                                               autofocus value="<?php echo $product[0]["product_name"]; ?>"/>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Description</label>
                                        <textarea type="text" class="form-control"
                                                  id="edit-description"><?php echo $product[0]["description"]; ?></textarea>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Purchasing Price</label>
                                        <input type="number" min="1" class="form-control" id="edit-purchasing-price"
                                               value="<?php echo $product[0]["purchasing_price"]; ?>"/>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Selling Price</label>
                                        <input type="number" min="1" class="form-control" id="edit-selling-price"
                                               value="<?php echo $product[0]["selling_price"]; ?>"/>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Images</label>
                                        <input type="file" multiple class="form-control" id="edit-images"
                                               accept="image/*"/>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Qty</label>
                                        <input type="number" min="1" class="form-control" id="edit-qty"
                                               value="<?php echo $product[0]["qty"]; ?>"/>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Category</label>
                                        <select id="edit-category-id" class="select2 form-select form-control">
                                            <option value="" disabled>Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option
                                                        value="<?php echo $category['category_id']; ?>"
                                                    <?php echo ($category['category_id'] == $product[0]['category_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['category']); ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div id="existing-images"
                                             style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">
                                            <?php foreach ($images as $img): ?>
                                                <div style="position:relative; display:inline-block;">
                                                    <img src="uploads/<?php echo htmlspecialchars($img); ?>"
                                                         style="width:70px; height:70px; object-fit:cover; border-radius:6px;">
                                                    <button type="button" class="remove-image-btn"
                                                            data-img="<?php echo htmlspecialchars($img); ?>"
                                                            style="position:absolute; top:-6px; right:-6px; background:#f00; color:#fff; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer;">
                                                        ×
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                </div>
                                <div class="mt-2">
                                    <button type="button"
                                            onclick="return validateEditProduct('<?php echo $product[0]["product_id"]; ?>')"
                                            class="btn btn-primary me-2">Edit Product
                                    </button>
                                    <a class="btn btn-success" href="products.php">All Products</a>
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
