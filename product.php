<?php
include 'includes/products_db.php';

// Locate product by ID
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

foreach ($products as $p) {
    if ($p['id'] === $productId) {
        $product = $p;
        break;
    }
}

// Redirect or show elegant error if not found
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

// Get category labels
$categoryLabel = ucfirst($product['category'] === 'stitched' ? 'Stitched' : $product['category']);
$categoryUrl = "shop.php?category=" . urlencode($product['category']);

include 'includes/header.php';
?>

<!-- Breadcrumbs -->
<div class="bg-light py-3 border-bottom border-light">
    <div class="container-fluid px-lg-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $categoryUrl; ?>"><?php echo $categoryLabel; ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
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
                    <img src="<?php echo $product['image']; ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-detail-img img-fluid w-100 h-100 object-fit-cover">
                </div>
            </div>
            
            <!-- Right Side: Details & Selectors -->
            <div class="col-lg-6">
                <div class="ps-lg-4">
                    
                    <!-- Meta info -->
                    <span class="text-uppercase tracking-widest text-muted" style="font-size: 0.75rem; font-weight: 500;">
                        <?php echo $categoryLabel; ?> &bull; <?php echo htmlspecialchars($product['subcategory']); ?>
                    </span>
                    
                    <!-- Product Title -->
                    <h1 class="h2 text-navy text-uppercase tracking-wide mt-2 mb-3 fw-bold">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h1>
                    
                    <!-- Price Tag -->
                    <span class="fs-4 text-navy fw-semibold mb-4 d-block">
                        $<?php echo number_format($product['price'], 2); ?>
                    </span>
                    
                    <hr class="border-light-subtle my-4">
                    
                    <!-- Short Description -->
                    <p class="text-muted mb-4" style="line-height: 1.8; font-size: 0.9rem;">
                        <?php echo htmlspecialchars($product['description']); ?>
                    </p>
                    
                    <hr class="border-light-subtle my-4">
                    
                    <!-- Size Selectors -->
                    <div class="mb-4">
                        <span class="text-uppercase tracking-wider text-navy fw-semibold d-block mb-3" style="font-size: 0.75rem;">
                            Select Size
                        </span>
                        <div class="size-grid" style="max-width: 320px;">
                            <?php foreach ($product['sizes'] as $index => $sz): ?>
                                <button type="button" 
                                        class="size-btn d-flex align-items-center justify-content-center <?php echo $index === 0 ? 'active' : ''; ?>" 
                                        data-size-value="<?php echo $sz; ?>">
                                    <?php echo $sz; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Color Selectors -->
                    <div class="mb-4">
                        <span class="text-uppercase tracking-wider text-navy fw-semibold d-block mb-3" style="font-size: 0.75rem;">
                            Select Color
                        </span>
                        <div class="color-selector">
                            <?php foreach ($product['colors'] as $index => $col): ?>
                                <div class="color-dot color-<?php echo strtolower($col); ?> <?php echo $index === 0 ? 'active' : ''; ?>" 
                                     data-color-name="<?php echo $col; ?>" 
                                     title="<?php echo $col; ?>"></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Quantity Selector -->
                    <div class="mb-4">
                        <span class="text-uppercase tracking-wider text-navy fw-semibold d-block mb-3" style="font-size: 0.75rem;">
                            Quantity
                        </span>
                        <div class="quantity-selector">
                            <button class="quantity-btn minus-btn">-</button>
                            <input type="text" class="quantity-input" value="1" readonly>
                            <button class="quantity-btn plus-btn">+</button>
                        </div>
                    </div>
                    
                    <!-- Add To Cart CTA Button -->
                    <div class="mt-4 pt-2">
                        <button class="btn btn-navy py-3 px-5 w-100 text-uppercase btn-add-to-cart-detail"
                                data-id="<?php echo $product['id']; ?>"
                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-price="<?php echo $product['price']; ?>"
                                data-image="<?php echo $product['image']; ?>"
                                data-size="<?php echo $product['sizes'][0]; ?>"
                                data-color="<?php echo $product['colors'][0]; ?>">
                            Add to Cart
                        </button>
                    </div>
                    
                    <!-- Collapsible Accordion Details -->
                    <div class="accordion accordion-flush mt-5" id="productSpecsAccordion">
                        
                        <!-- Fabric Specifications -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSpecs">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSpecs" aria-expanded="true" aria-controls="collapseSpecs">
                                    Fabric & Care Details
                                </button>
                            </h2>
                            <div id="collapseSpecs" class="collapse show" aria-labelledby="headingSpecs" data-bs-parent="#productSpecsAccordion">
                                <div class="accordion-body">
                                    <?php echo htmlspecialchars($product['details']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Shipping info -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingShipping">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseShipping" aria-expanded="false" aria-controls="collapseShipping">
                                    Shipping & Returns
                                </button>
                            </h2>
                            <div id="collapseShipping" class="collapse" aria-labelledby="headingShipping" data-bs-parent="#productSpecsAccordion">
                                <div class="accordion-body">
                                    Complimentary shipping across the country on all orders above $100. Deliveries within metropolitan zones take 2–3 business days. We offer hassle-free returns within 14 days of delivery, provided products are unworn, unwashed, and retain original tags.
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Related Products Section -->
<section class="py-5 border-top border-light mt-5">
    <div class="container px-lg-5">
        
        <!-- Header -->
        <div class="text-center mb-5">
            <span class="text-uppercase tracking-widest text-muted" style="font-size: 0.8rem; font-weight: 500;">You May Also Like</span>
            <h2 class="h3 text-uppercase tracking-wider mt-2 mb-3">Related Products</h2>
            <div class="mx-auto" style="width: 50px; height: 1.5px; background-color: var(--color-navy);"></div>
        </div>
        
        <!-- Products Grid -->
        <div class="row g-4 justify-content-center">
            <?php
            // Pull 4 related products from same category, excluding active product
            $related = [];
            foreach ($products as $p) {
                if ($p['id'] !== $product['id'] && ($p['category'] === $product['category'] || $p['subcategory'] === $product['subcategory'])) {
                    $related[] = $p;
                }
            }
            // Fallback to general list if no matches
            if (count($related) === 0) {
                foreach ($products as $p) {
                    if ($p['id'] !== $product['id']) $related[] = $p;
                }
            }
            
            $relatedToShow = array_slice($related, 0, 4);
            foreach ($relatedToShow as $relProd) {
                render_product_card($relProd, 'col-6 col-md-4 col-lg-3');
            }
            ?>
        </div>
        
    </div>
</section>

<?php include 'includes/footer.php'; ?>
