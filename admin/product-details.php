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
$productCategory = getCategoryById($conn, $product[0]['category_id']);
$productCategory = $productCategory[0]['category'];
$categories = getAllCategories($conn);
$discount = round($product[0]['selling_price']/$product[0]['purchasing_price'] * 100);
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
          <h1 class="h3 mb-0 text-gray-800">Product Details</h1>
        </div>

        <!-- Content Row -->
        <div class="row">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-md-10 bg-white shadow-sm p-4 rounded d-flex flex-wrap">

                <!-- Left: Image Carousel -->
                <div class="col-md-6 d-flex justify-content-center align-items-center">
                  <div id="productCarousel" class="carousel slide" data-bs-ride="carousel"
                       style="width: 100%; max-width: 400px;">
                    <div class="carousel-inner border rounded shadow-sm">
                      <?php foreach ($images as $index => $image): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                          <img src="uploads/<?= htmlspecialchars($image) ?>"
                               class="d-block w-100 rounded"
                               style="height: 350px; object-fit: cover;"
                               alt="Product Image <?= $index + 1 ?>">
                        </div>
                      <?php endforeach; ?>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel"
                            data-bs-slide="prev"
                            style="width: 30px;">
      <span class="carousel-control-prev-icon" aria-hidden="true"
            style="background-color: rgba(0,0,0,0.5); border-radius: 50%; width: 25px; height: 25px;"></span>
                    </button>

                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel"
                            data-bs-slide="next"
                            style="width: 30px;">
      <span class="carousel-control-next-icon" aria-hidden="true"
            style=" border-radius: 50%; width: 25px; height: 25px;"></span>
                    </button>
                  </div>
                </div>

                <!-- Right: Product Details -->
                <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                  <h2 class="fw-bold"><?php echo $product[0]['product_name'] ?></h2>
                  <p class="text-muted mb-2">Category: <span class="fw-semibold"><?php echo $productCategory ?></span>
                  </p>
                  <p class="text-muted mb-2">Product ID: <span class="fw-semibold">PROD_1762161476</span></p>
                    <h5 class="text-success mb-3">Purchasing Price: <span>PKR<?php echo $product[0]["purchasing_price"];?></span></h5>
                    <h5 class="text-success mb-3">Selling Price: <span>PKR<?php echo $product[0]["selling_price"];?></span></h5>

                  <p><?php echo $product[0]['description'];?></p>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Footer -->
    <?php require_once("includes/footer.php"); ?>
