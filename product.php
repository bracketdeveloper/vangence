<?php
require_once("admin/model/functions.php");

// Locate product by ID using database function
$productId = isset($_GET['product_id']) ? (string)$_GET['product_id'] : '';
$productResult = getProductById($conn, $productId);
$product = (!empty($productResult)) ? $productResult[0] : null;

// Deactivated products should behave like they don't exist on the storefront,
// even if someone has a direct/bookmarked link to them.
if ($product && isset($product['is_active']) && (int)$product['is_active'] !== 1) {
    $product = null;
}

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

// Maps common fashion color names to an actual swatch color.
// Falls back to the raw name (lowercased, spaces stripped) for anything
// not listed here, which still works for standard CSS colors like
// "Navy", "Black", "White", "Red", etc.
function getSwatchColor($colorName) {
    $map = [
            'off white'  => '#f8f5f0',
            'offwhite'   => '#f8f5f0',
            'ivory'      => '#fffff0',
            'cream'      => '#fdf6e3',
            'beige'      => '#f5f5dc',
            'charcoal'   => '#36454f',
            'maroon'     => '#800000',
            'mustard'    => '#e1ad01',
            'olive'      => '#708238',
            'rose gold'  => '#b76e79',
            'rosegold'   => '#b76e79',
            'sky blue'   => '#87ceeb',
            'skyblue'    => '#87ceeb',
            'wine'       => '#722f37',
            'mint'       => '#98ff98',
            'tan'        => '#d2b48c',
            'peach'      => '#ffdab9',
            'lilac'      => '#c8a2c8',
            'mauve'      => '#e0b0ff',
    ];
    $key = strtolower(trim($colorName));
    if (isset($map[$key])) {
        return $map[$key];
    }
    return str_replace(' ', '', $key);
}

include 'includes/header.php';
?>

    <!-- Breadcrumbs -->
    <div class="bg-light py-3 border-bottom border-light">
        <div class="container-fluid px-lg-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                    <li class="breadcrumb-item active"
                        aria-current="page"><?php echo htmlspecialchars($product['product_name']); ?></li>
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

                    <span class="text-uppercase tracking-widest text-muted"
                          style="font-size: 0.75rem; font-weight: 500;">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </span>

                        <h1 class="h2 text-navy text-uppercase tracking-wide mt-2 mb-3 fw-bold">
                            <?php echo htmlspecialchars($product['product_name']); ?>
                        </h1>

                        <span class="fs-4 text-navy fw-semibold mb-4 d-block">
                        PKR <?php echo number_format($product['selling_price'], 2); ?>
                    </span>

                        <hr class="border-light-subtle my-4">

                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 0.9rem;">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>

                        <hr class="border-light-subtle my-4">

                        <!-- Size Selectors -->
                        <div class="mb-4">
                            <span class="text-uppercase tracking-wider text-navy fw-semibold d-block mb-3"
                                  style="font-size: 0.75rem;">Select Size</span>
                            <div class="size-grid" style="max-width: 320px;">
                                <?php foreach ($sizes as $index => $sz): ?>
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
                            <span class="text-uppercase tracking-wider text-navy fw-semibold d-block mb-3"
                                  style="font-size: 0.75rem;">
                                Select Color: <span id="selected-color-name" class="text-muted fw-normal text-capitalize"><?php echo htmlspecialchars($colors[0]); ?></span>
                            </span>
                            <div class="color-swatch-group d-flex gap-3 flex-wrap">
                                <?php foreach ($colors as $index => $col):
                                    $inputId = 'color-' . strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $col));
                                    ?>
                                    <div class="color-swatch-wrapper">
                                        <input type="radio"
                                               class="color-swatch-input"
                                               name="color"
                                               id="<?php echo $inputId; ?>"
                                               value="<?php echo htmlspecialchars($col); ?>"
                                                <?php echo $index === 0 ? 'checked' : ''; ?>>
                                        <label class="color-swatch"
                                               for="<?php echo $inputId; ?>"
                                               style="background-color: <?php echo htmlspecialchars(getSwatchColor($col)); ?>;"
                                               title="<?php echo htmlspecialchars($col); ?>"
                                               aria-label="<?php echo htmlspecialchars($col); ?>">
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="mb-4">
                            <span class="text-uppercase tracking-wider text-navy fw-semibold d-block mb-3"
                                  style="font-size: 0.75rem;">Quantity</span>
                            <div class="quantity-selector">
                                <button class="quantity-btn minus-btn">-</button>
                                <input type="text" class="quantity-input" value="1" readonly>
                                <button class="quantity-btn plus-btn">+</button>
                            </div>
                        </div>

                        <div class="mt-4 pt-2">
                            <button class="btn btn-navy py-3 px-5 w-100 text-uppercase btn-add-to-cart-detail"
                                    id="add-to-cart-btn"
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
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseSpecs" aria-expanded="true"
                                            aria-controls="collapseSpecs">
                                        Fabric & Care Details
                                    </button>
                                </h2>
                                <div id="collapseSpecs" class="collapse show" aria-labelledby="headingSpecs"
                                     data-bs-parent="#productSpecsAccordion">
                                    <div class="accordion-body"><?php echo htmlspecialchars($product['details']); ?></div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingShipping">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseShipping" aria-expanded="false"
                                            aria-controls="collapseShipping">
                                        Shipping & Returns
                                    </button>
                                </h2>
                                <div id="collapseShipping" class="collapse" aria-labelledby="headingShipping"
                                     data-bs-parent="#productSpecsAccordion">
                                    <div class="accordion-body">Complimentary shipping on all orders above $100.
                                        Hassle-free returns within 14 days.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .color-swatch-group {
            gap: 14px;
        }
        .color-swatch-wrapper {
            position: relative;
        }
        .color-swatch-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }
        .color-swatch {
            display: inline-block;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #e2e2e2;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
            position: relative;
        }
        .color-swatch:hover {
            transform: scale(1.1);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.18);
        }
        .color-swatch-input:checked + .color-swatch {
            border-color: #1b2a4a;
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #1b2a4a;
            transform: scale(1.08);
        }
        .color-swatch-input:checked + .color-swatch::after {
            content: "\2713";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 15px;
            font-weight: bold;
            color: #fff;
            mix-blend-mode: difference;
        }
        .color-swatch-input:focus-visible + .color-swatch {
            outline: 2px solid #1b2a4a;
            outline-offset: 3px;
        }
    </style>

    <script>
        (function () {
            var colorInputs = document.querySelectorAll('.color-swatch-input');
            var colorNameLabel = document.getElementById('selected-color-name');
            var addToCartBtn = document.getElementById('add-to-cart-btn');

            colorInputs.forEach(function (input) {
                input.addEventListener('change', function () {
                    if (!input.checked) {
                        return;
                    }
                    if (colorNameLabel) {
                        colorNameLabel.textContent = input.value;
                    }
                    if (addToCartBtn) {
                        addToCartBtn.setAttribute('data-color', input.value);
                    }
                });
            });
        })();
    </script>

<?php include 'includes/footer.php'; ?>