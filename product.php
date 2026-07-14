<?php
require_once("admin/model/functions.php");

// Locate product by ID using database function
$productId = isset($_GET['product_id']) ? (string)$_GET['product_id'] : '';
$productResult = getProductById($conn, $productId);
$product = (!empty($productResult)) ? $productResult[0] : null;

// Redirect or show error if not found
if (!$product) {
    include 'includes/header.php';
    ?>
    <div class="container py-5 text-center my-5">
        <div class="mb-4">
            <i class="fa-solid fa-triangle-exclamation text-navy opacity-25" style="font-size: 4rem;"></i>
        </div>
        <h1 class="h3 text-uppercase tracking-wider text-navy mb-3">Product Not Found</h1>
        <p class="text-muted mb-4">The product you are looking for does not exist or has been moved.</p>
        <a href="shop.php" class="btn btn-navy px-4 py-3">Return to Shop</a>
    </div>
    <?php
    include 'includes/footer.php';
    exit;
}

// Decode JSON fields
$images = json_decode($product['image'], true);
$sizes = json_decode($product['sizes'], true);
$colors = json_decode($product['colors'], true);
$displayImage = (is_array($images) && count($images) > 0) ? $images[0] : 'default.jpg';

include 'includes/header.php';
?>

    <!-- Breadcrumbs -->
    <div class="bg-light py-3 border-bottom border-light">
        <div class="container-fluid px-lg-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_name']); ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Product Details Section -->
    <section class="py-5">
        <div class="container px-lg-5">
            <div class="row g-5">

                <!-- Left Side: Product Image -->
                <div class="col-lg-6">
                    <div class="product-detail-img-wrapper bg-light">
                        <img src="admin/uploads/<?php echo $displayImage; ?>"
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                             class="product-detail-img img-fluid w-100 h-100 object-fit-cover">
                    </div>
                </div>

                <!-- Right Side: Details & Selectors -->
                <div class="col-lg-6">
                    <div class="ps-lg-4">

                    <span class="text-uppercase tracking-widest text-muted" style="font-size: 0.75rem; font-weight: 500;">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </span>

                        <h1 class="h2 text-navy text-uppercase tracking-wide mt-2 mb-3 fw-bold">
                            <?php echo htmlspecialchars($product['product_name']); ?>
                        </h1>

                        <span class="fs-4 text-navy fw-semibold mb-4 d-block">
                        $<?php echo number_format($product['selling_price'], 2); ?>
                    </span>

                        <hr class="border-light-subtle my-4">

                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 0.9rem;">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>

                        <hr class="border-light-subtle my-4">

                        <!-- Size Selectors -->
                        <div class="mb-4">
                            <span class="text-uppercase tracking-wider text-navy fw-semibold d-block mb-3" style="font-size: 0.75rem;">Select Size</span>
                            <div class="size-grid" style="max-width: 320px;">
                                <?php foreach ($sizes as $index => $sz): ?>
                                    <button type="button" class="size-btn d-flex align-items-center justify-content-center <?php echo $index === 0 ? 'active' : ''; ?>" data-size-value="<?php echo $sz; ?>">
                                        <?php echo $sz; ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Color Selectors -->
                        <div class="mb-4">
                            <span class="text-uppercase tracking-wider text-navy fw-semibold d-block mb-3" style="font-size: 0.75rem;">Select Color</span>
                            <div class="color-selector">
                                <?php foreach ($colors as $index => $col): ?>
                                    <div class="color-dot color-<?php echo strtolower($col); ?> <?php echo $index === 0 ? 'active' : ''; ?>" data-color-name="<?php echo $col; ?>" title="<?php echo $col; ?>"></div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="mb-4">
                            <span class="text-uppercase tracking-wider text-navy fw-semibold d-block mb-3" style="font-size: 0.75rem;">Quantity</span>
                            <div class="quantity-selector">
                                <button class="quantity-btn minus-btn">-</button>
                                <input type="text" class="quantity-input" value="1" readonly>
                                <button class="quantity-btn plus-btn">+</button>
                            </div>
                        </div>

                        <div class="mt-4 pt-2">
                            <button class="btn btn-navy py-3 px-5 w-100 text-uppercase btn-add-to-cart-detail"
                                    data-id="<?php echo $product['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                    data-price="<?php echo $product['selling_price']; ?>"
                                    data-image="<?php echo $displayImage; ?>"
                                    data-size="<?php echo $sizes[0]; ?>"
                                    data-color="<?php echo $colors[0]; ?>">
                                Add to Cart
                            </button>
                        </div>

                        <div class="accordion accordion-flush mt-5" id="productSpecsAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingSpecs">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSpecs" aria-expanded="true" aria-controls="collapseSpecs">
                                        Fabric & Care Details
                                    </button>
                                </h2>
                                <div id="collapseSpecs" class="collapse show" aria-labelledby="headingSpecs" data-bs-parent="#productSpecsAccordion">
                                    <div class="accordion-body"><?php echo htmlspecialchars($product['details']); ?></div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingShipping">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseShipping" aria-expanded="false" aria-controls="collapseShipping">
                                        Shipping & Returns
                                    </button>
                                </h2>
                                <div id="collapseShipping" class="collapse" aria-labelledby="headingShipping" data-bs-parent="#productSpecsAccordion">
                                    <div class="accordion-body">Complimentary shipping on all orders above $100. Hassle-free returns within 14 days.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>