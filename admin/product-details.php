<?php
require_once("includes/head.php");
require_once("includes/auth_admin.php");
require_once("model/functions.php");
// Ensure product_id is provided
if (!isset($_GET['product_id'])) {
    echo "<script>alert('Invalid request'); window.location.href='products.php';</script>";
    exit;
}
$product_id = $_GET['product_id'];
$product = getProductById($conn, $product_id);
if (!$product) {
    echo "<script>alert('Product not found'); window.location.href='products.php';</script>";
    exit;
}
// Prepare data
$p = $product[0];
$images = json_decode($p['image'], true) ?? [];
$productCategory = getCategoryById($conn, $p['category_id']);
$categoryName = $productCategory[0]['category'] ?? 'Uncategorized';
?>
<div id="wrapper">
    <?php require_once("includes/sidebar.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php require_once("includes/topbar.php"); ?>
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Product Details</h1>
                </div>
                <div class="row">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-10 bg-white shadow-sm p-4 rounded d-flex flex-wrap">
                                <div class="col-md-6 d-flex justify-content-center align-items-center">
                                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel" style="width: 100%; max-width: 400px;">
                                        <div class="carousel-inner border rounded shadow-sm">
                                            <?php if (!empty($images)): ?>
                                                <?php foreach ($images as $index => $image): ?>
                                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                        <img src="uploads/<?= htmlspecialchars($image) ?>" class="d-block w-100 rounded" style="height: 350px; object-fit: cover;" alt="Product Image">
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="carousel-item active">
                                                    <img src="assets/img/placeholder.png" class="d-block w-100 rounded" alt="No image">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (count($images) > 1): ?>
                                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%;"></span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%;"></span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                                    <h2 class="fw-bold"><?= htmlspecialchars($p['product_name']) ?></h2>
                                    <p class="text-muted mb-2">Category: <span class="fw-semibold"><?= htmlspecialchars($categoryName) ?></span></p>
                                    <p class="text-muted mb-2">Product ID: <span class="fw-semibold"><?= htmlspecialchars($p['product_id']) ?></span></p>
                                    <h6 class="fw-bold">Description:</h6>
                                    <p class="text-muted"><?= nl2br(htmlspecialchars($p['description'])); ?></p>
                                    <h6 class="fw-bold">Details:</h6>
                                    <p class="text-muted"><?= nl2br(htmlspecialchars($p['details'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once("includes/footer.php"); ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>