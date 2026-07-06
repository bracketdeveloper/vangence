<?php
include 'includes/products_db.php';

// Extract filter parameters
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$selectedSubcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';
$selectedSize = isset($_GET['size']) ? $_GET['size'] : '';
$selectedColor = isset($_GET['color']) ? $_GET['color'] : '';
$priceMax = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 200.00;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Filter products array
$filteredProducts = array_filter($products, function($prod) use ($selectedCategory, $selectedSubcategory, $selectedSize, $selectedColor, $priceMax, $searchQuery) {
    if (!empty($selectedCategory) && strtolower($prod['category']) !== strtolower($selectedCategory)) {
        return false;
    }
    if (!empty($selectedSubcategory) && strtolower($prod['subcategory']) !== strtolower($selectedSubcategory)) {
        return false;
    }
    if ($prod['price'] > $priceMax) {
        return false;
    }
    if (!empty($selectedSize) && !in_array($selectedSize, $prod['sizes'])) {
        return false;
    }
    if (!empty($selectedColor) && !in_array($selectedColor, $prod['colors'])) {
        return false;
    }
    if (!empty($searchQuery)) {
        $nameMatch = strpos(strtolower($prod['name']), strtolower($searchQuery)) !== false;
        $descMatch = strpos(strtolower($prod['description']), strtolower($searchQuery)) !== false;
        if (!$nameMatch && !$descMatch) {
            return false;
        }
    }
    return true;
});

// Sort products array
if ($sort === 'price-asc') {
    usort($filteredProducts, function($a, $b) {
        return $a['price'] <=> $b['price'];
    });
} elseif ($sort === 'price-desc') {
    usort($filteredProducts, function($a, $b) {
        return $b['price'] <=> $a['price'];
    });
}

// Generate category display name
$categoryName = 'All Products';
if ($selectedCategory === 'men') {
    $categoryName = "Men's Collection";
} elseif ($selectedCategory === 'women') {
    $categoryName = "Women's Collection";
} elseif ($selectedCategory === 'stitched') {
    $categoryName = 'Stitched Fabrics';
}

include 'includes/header.php';
?>

<!-- Breadcrumbs Header -->
<div class="bg-light py-3 border-bottom border-light">
    <div class="container-fluid px-lg-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item <?php echo empty($selectedCategory) ? 'active' : ''; ?>">
                    <?php if (empty($selectedCategory)): ?>Shop<?php else: ?><a href="shop.php">Shop</a><?php endif; ?>
                </li>
                <?php if (!empty($selectedCategory)): ?>
                    <li class="breadcrumb-item <?php echo empty($selectedSubcategory) ? 'active' : ''; ?>">
                        <?php if (empty($selectedSubcategory)): ?>
                            <?php echo $categoryName; ?>
                        <?php else: ?>
                            <a href="shop.php?category=<?php echo $selectedCategory; ?>"><?php echo $categoryName; ?></a>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
                <?php if (!empty($selectedSubcategory)): ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php echo htmlspecialchars($selectedSubcategory); ?>
                    </li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<!-- Main Shop Container -->
<div class="container-fluid px-lg-5 py-5">
    <div class="row">
        
        <!-- Sidebar Filters - Desktop (col-lg-3) -->
        <aside class="col-lg-3 d-none d-lg-block">
            <form method="GET" action="shop.php" id="desktop-filter-form">
                <!-- Keep existing parameters when filters change -->
                <?php if (!empty($selectedCategory)): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($selectedCategory); ?>">
                <?php endif; ?>
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                <?php if (!empty($searchQuery)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <?php endif; ?>

                <!-- Filter Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h5 text-uppercase tracking-wider m-0 fw-bold">Filters</h2>
                    <a href="shop.php<?php echo !empty($selectedCategory) ? '?category=' . urlencode($selectedCategory) : ''; ?><?php echo !empty($selectedCategory) && !empty($selectedSubcategory) ? '&subcategory=' . urlencode($selectedSubcategory) : ''; ?>" 
                       class="text-decoration-underline text-muted" style="font-size: 0.8rem;">
                        Reset All
                    </a>
                </div>

                <!-- Category Links (Visual guide if not filtered or as submenu) -->
                <div class="filter-group">
                    <h3 class="filter-title">Collections</h3>
                    <ul class="list-unstyled d-flex flex-column gap-2" style="font-size: 0.85rem;">
                        <li>
                            <a href="shop.php" class="text-navy <?php echo empty($selectedCategory) ? 'fw-bold' : 'opacity-75'; ?>">
                                All Collection
                            </a>
                        </li>
                        <li>
                            <a href="shop.php?category=men" class="text-navy <?php echo $selectedCategory === 'men' ? 'fw-bold' : 'opacity-75'; ?>">
                                Men's Wear
                            </a>
                        </li>
                        <li>
                            <a href="shop.php?category=women" class="text-navy <?php echo $selectedCategory === 'women' ? 'fw-bold' : 'opacity-75'; ?>">
                                Women's Wear
                            </a>
                        </li>
                        <li>
                            <a href="shop.php?category=stitched" class="text-navy <?php echo $selectedCategory === 'stitched' ? 'fw-bold' : 'opacity-75'; ?>">
                                Stitched Fabrics
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Subcategories Filter if Category is Set -->
                <?php if ($selectedCategory === 'men' || $selectedCategory === 'women'): ?>
                <div class="filter-group">
                    <h3 class="filter-title">Subcategories</h3>
                    <div class="d-flex flex-column gap-2" style="font-size: 0.85rem;">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="subcategory" id="sub-all" value="" <?php echo empty($selectedSubcategory) ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="sub-all">All Items</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="subcategory" id="sub-shirts" value="Shirts" <?php echo $selectedSubcategory === 'Shirts' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="sub-shirts">Shirts</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="subcategory" id="sub-pants" value="Pants" <?php echo $selectedSubcategory === 'Pants' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="sub-pants">Pants</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="subcategory" id="sub-eastern" value="Eastern Wear" <?php echo $selectedSubcategory === 'Eastern Wear' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="sub-eastern">Eastern Wear</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="subcategory" id="sub-western" value="Western Wear" <?php echo $selectedSubcategory === 'Western Wear' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="sub-western">Western Wear</label>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Price Filter -->
                <div class="filter-group">
                    <h3 class="filter-title">Max Price</h3>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-size: 0.85rem;">Up to:</span>
                        <span class="fw-bold text-navy" id="price-val">$<?php echo number_format($priceMax, 2); ?></span>
                    </div>
                    <input type="range" class="form-range" min="50" max="200" step="10" name="price_max" id="price-slider"
                           value="<?php echo $priceMax; ?>" 
                           oninput="document.getElementById('price-val').innerText = '$' + parseFloat(this.value).toFixed(2)"
                           onchange="this.form.submit()">
                </div>

                <!-- Sizes Filter -->
                <div class="filter-group">
                    <h3 class="filter-title">Size</h3>
                    <div class="size-grid">
                        <?php 
                        $allSizes = ['S', 'M', 'L', 'XL', 'XS', '30', '32', '34', '36'];
                        foreach ($allSizes as $sz): 
                        ?>
                            <input type="radio" class="btn-check" name="size" id="size-<?php echo $sz; ?>" value="<?php echo $sz; ?>" 
                                   <?php echo $selectedSize === $sz ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="size-btn d-flex align-items-center justify-content-center" for="size-<?php echo $sz; ?>"><?php echo $sz; ?></label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($selectedSize)): ?>
                        <div class="mt-2 text-end">
                            <input type="radio" class="btn-check" name="size" id="size-clear" value="" onchange="this.form.submit()">
                            <label class="text-decoration-underline text-muted border-0 bg-transparent p-0" for="size-clear" style="font-size: 0.75rem; cursor: pointer;">Clear Size</label>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Colors Filter -->
                <div class="filter-group">
                    <h3 class="filter-title">Color</h3>
                    <div class="color-selector">
                        <input type="radio" class="btn-check" name="color" id="col-navy-radio" value="Navy" 
                               <?php echo $selectedColor === 'Navy' ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <label class="color-dot color-navy <?php echo $selectedColor === 'Navy' ? 'active' : ''; ?>" for="col-navy-radio" title="Navy"></label>

                        <input type="radio" class="btn-check" name="color" id="col-white-radio" value="White" 
                               <?php echo $selectedColor === 'White' ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <label class="color-dot color-white <?php echo $selectedColor === 'White' ? 'active' : ''; ?>" for="col-white-radio" title="White"></label>
                    </div>
                    <?php if (!empty($selectedColor)): ?>
                        <div class="mt-2">
                            <input type="radio" class="btn-check" name="color" id="color-clear" value="" onchange="this.form.submit()">
                            <label class="text-decoration-underline text-muted border-0 bg-transparent p-0" for="color-clear" style="font-size: 0.75rem; cursor: pointer;">Clear Color</label>
                        </div>
                    <?php endif; ?>
                </div>

            </form>
        </aside>
        
        <!-- Main Products Side (col-lg-9) -->
        <main class="col-lg-9">
            
            <!-- Mobile Filters and Sort Header -->
            <div class="d-flex flex-wrap align-items-center justify-content-between pb-4 border-bottom border-light mb-4">
                
                <!-- Category Heading -->
                <div>
                    <h1 class="h3 text-uppercase tracking-wider text-navy mb-1 fw-bold"><?php echo $categoryName; ?></h1>
                    <span class="text-muted" style="font-size: 0.85rem;">Showing <?php echo count($filteredProducts); ?> items</span>
                </div>
                
                <!-- Action Controls -->
                <div class="d-flex align-items-center gap-3 mt-3 mt-sm-0">
                    
                    <!-- Mobile Filter Toggler (Visible only on mobile/tablet) -->
                    <button class="btn btn-outline-navy d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters" aria-controls="mobileFilters">
                        <i class="fa-solid fa-sliders me-2"></i> Filters
                    </button>
                    
                    <!-- Sorting Dropdown -->
                    <div class="d-flex align-items-center gap-2">
                        <label for="sort-select" class="text-nowrap d-none d-sm-block text-muted" style="font-size: 0.85rem;">Sort By:</label>
                        <select class="form-select form-select-sm border-navy shadow-none" id="sort-select" 
                                style="border-radius: 0; min-width: 160px; font-size: 0.8rem; padding: 6px 12px;"
                                onchange="location.href = updateQueryStringParameter(window.location.href, 'sort', this.value)">
                            <option value="default" <?php echo $sort === 'default' ? 'selected' : ''; ?>>Featured</option>
                            <option value="price-asc" <?php echo $sort === 'price-asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price-desc" <?php echo $sort === 'price-desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </div>
                    
                </div>
                
            </div>
            
            <!-- Active Filter Badges -->
            <?php if (!empty($selectedSubcategory) || !empty($selectedSize) || !empty($selectedColor) || $priceMax < 200 || !empty($searchQuery)): ?>
                <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
                    <span class="text-muted" style="font-size: 0.8rem;">Active Filters:</span>
                    
                    <?php if (!empty($searchQuery)): ?>
                        <span class="badge bg-navy text-white text-uppercase tracking-wider px-2 py-1.5 font-weight-normal" style="font-size: 0.65rem; border-radius: 0;">
                            Search: <?php echo htmlspecialchars($searchQuery); ?>
                            <a href="<?php echo removeQueryStringParameter($_SERVER['REQUEST_URI'], 'search'); ?>" class="text-white ms-2 text-decoration-none"><i class="fa-solid fa-xmark"></i></a>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($selectedSubcategory)): ?>
                        <span class="badge bg-navy text-white text-uppercase tracking-wider px-2 py-1.5 font-weight-normal" style="font-size: 0.65rem; border-radius: 0;">
                            Subcategory: <?php echo htmlspecialchars($selectedSubcategory); ?>
                            <a href="<?php echo removeQueryStringParameter($_SERVER['REQUEST_URI'], 'subcategory'); ?>" class="text-white ms-2 text-decoration-none"><i class="fa-solid fa-xmark"></i></a>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($priceMax < 200): ?>
                        <span class="badge bg-navy text-white text-uppercase tracking-wider px-2 py-1.5" style="font-size: 0.65rem; border-radius: 0;">
                            Max Price: $<?php echo $priceMax; ?>
                            <a href="<?php echo removeQueryStringParameter($_SERVER['REQUEST_URI'], 'price_max'); ?>" class="text-white ms-2 text-decoration-none"><i class="fa-solid fa-xmark"></i></a>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedSize)): ?>
                        <span class="badge bg-navy text-white text-uppercase tracking-wider px-2 py-1.5" style="font-size: 0.65rem; border-radius: 0;">
                            Size: <?php echo htmlspecialchars($selectedSize); ?>
                            <a href="<?php echo removeQueryStringParameter($_SERVER['REQUEST_URI'], 'size'); ?>" class="text-white ms-2 text-decoration-none"><i class="fa-solid fa-xmark"></i></a>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedColor)): ?>
                        <span class="badge bg-navy text-white text-uppercase tracking-wider px-2 py-1.5" style="font-size: 0.65rem; border-radius: 0;">
                            Color: <?php echo htmlspecialchars($selectedColor); ?>
                            <a href="<?php echo removeQueryStringParameter($_SERVER['REQUEST_URI'], 'color'); ?>" class="text-white ms-2 text-decoration-none"><i class="fa-solid fa-xmark"></i></a>
                        </span>
                    <?php endif; ?>
                    
                    <a href="shop.php<?php echo !empty($selectedCategory) ? '?category=' . urlencode($selectedCategory) : ''; ?>" class="text-navy text-decoration-underline ms-2" style="font-size: 0.75rem;">Clear All</a>
                </div>
            <?php endif; ?>
            
            <!-- Products Grid -->
            <div class="row g-4">
                <?php if (count($filteredProducts) > 0): ?>
                    <?php foreach ($filteredProducts as $prod): ?>
                        <?php render_product_card($prod, 'col-6 col-md-6 col-lg-4'); ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="col-12 text-center py-5">
                        <div class="mb-3">
                            <i class="fa-solid fa-circle-info text-navy opacity-25" style="font-size: 3rem;"></i>
                        </div>
                        <h3 class="h5 text-uppercase tracking-wide text-navy">No products found</h3>
                        <p class="text-muted">Try adjusting your filters or search categories to find what you are looking for.</p>
                        <a href="shop.php" class="btn btn-navy mt-3">Reset All Filters</a>
                    </div>
                <?php endif; ?>
            </div>
            
        </main>
        
    </div>
</div>

<!-- Mobile Filters Drawer (Offcanvas) -->
<div class="offcanvas offcanvas-end border-0" tabindex="-1" id="mobileFilters" aria-labelledby="mobileFiltersLabel" style="width: 300px;">
    <div class="offcanvas-header d-flex justify-content-between align-items-center py-4 px-3 border-bottom border-light">
        <span class="h5 text-uppercase tracking-wider m-0 fw-bold" id="mobileFiltersLabel">Filters</span>
        <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="shop.php" id="mobile-filter-form">
            <?php if (!empty($selectedCategory)): ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($selectedCategory); ?>">
            <?php endif; ?>
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
            <?php if (!empty($searchQuery)): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <?php endif; ?>
            
            <!-- Category Selection Mobile -->
            <div class="filter-group">
                <h3 class="filter-title">Collections</h3>
                <ul class="list-unstyled d-flex flex-column gap-2" style="font-size: 0.85rem;">
                    <li><a href="shop.php" class="text-navy <?php echo empty($selectedCategory) ? 'fw-bold' : 'opacity-75'; ?>">All Collection</a></li>
                    <li><a href="shop.php?category=men" class="text-navy <?php echo $selectedCategory === 'men' ? 'fw-bold' : 'opacity-75'; ?>">Men's Wear</a></li>
                    <li><a href="shop.php?category=women" class="text-navy <?php echo $selectedCategory === 'women' ? 'fw-bold' : 'opacity-75'; ?>">Women's Wear</a></li>
                    <li><a href="shop.php?category=stitched" class="text-navy <?php echo $selectedCategory === 'stitched' ? 'fw-bold' : 'opacity-75'; ?>">Stitched Fabrics</a></li>
                </ul>
            </div>

            <!-- Subcategories Mobile -->
            <?php if ($selectedCategory === 'men' || $selectedCategory === 'women'): ?>
            <div class="filter-group">
                <h3 class="filter-title">Subcategories</h3>
                <div class="d-flex flex-column gap-2" style="font-size: 0.85rem;">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="subcategory" id="mob-sub-all" value="" <?php echo empty($selectedSubcategory) ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <label class="form-check-label" for="mob-sub-all">All Items</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="subcategory" id="mob-sub-shirts" value="Shirts" <?php echo $selectedSubcategory === 'Shirts' ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <label class="form-check-label" for="mob-sub-shirts">Shirts</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="subcategory" id="mob-sub-pants" value="Pants" <?php echo $selectedSubcategory === 'Pants' ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <label class="form-check-label" for="mob-sub-pants">Pants</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="subcategory" id="mob-sub-eastern" value="Eastern Wear" <?php echo $selectedSubcategory === 'Eastern Wear' ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <label class="form-check-label" for="mob-sub-eastern">Eastern Wear</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="subcategory" id="mob-sub-western" value="Western Wear" <?php echo $selectedSubcategory === 'Western Wear' ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <label class="form-check-label" for="mob-sub-western">Western Wear</label>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Price Slider Mobile -->
            <div class="filter-group">
                <h3 class="filter-title">Max Price</h3>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size: 0.85rem;">Up to:</span>
                    <span class="fw-bold text-navy" id="mob-price-val">$<?php echo number_format($priceMax, 2); ?></span>
                </div>
                <input type="range" class="form-range" min="50" max="200" step="10" name="price_max" id="mob-price-slider"
                       value="<?php echo $priceMax; ?>" 
                       oninput="document.getElementById('mob-price-val').innerText = '$' + parseFloat(this.value).toFixed(2)"
                       onchange="this.form.submit()">
            </div>

            <!-- Sizes Mobile -->
            <div class="filter-group">
                <h3 class="filter-title">Size</h3>
                <div class="size-grid">
                    <?php foreach ($allSizes as $sz): ?>
                        <input type="radio" class="btn-check" name="size" id="mob-size-<?php echo $sz; ?>" value="<?php echo $sz; ?>" 
                               <?php echo $selectedSize === $sz ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <label class="size-btn d-flex align-items-center justify-content-center" for="mob-size-<?php echo $sz; ?>"><?php echo $sz; ?></label>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($selectedSize)): ?>
                    <div class="mt-2 text-end">
                        <input type="radio" class="btn-check" name="size" id="mob-size-clear" value="" onchange="this.form.submit()">
                        <label class="text-decoration-underline text-muted border-0 bg-transparent p-0" for="mob-size-clear" style="font-size: 0.75rem; cursor: pointer;">Clear Size</label>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Colors Mobile -->
            <div class="filter-group">
                <h3 class="filter-title">Color</h3>
                <div class="color-selector">
                    <input type="radio" class="btn-check" name="color" id="mob-col-navy-radio" value="Navy" 
                           <?php echo $selectedColor === 'Navy' ? 'checked' : ''; ?> onchange="this.form.submit()">
                    <label class="color-dot color-navy <?php echo $selectedColor === 'Navy' ? 'active' : ''; ?>" for="mob-col-navy-radio" title="Navy"></label>

                    <input type="radio" class="btn-check" name="color" id="mob-col-white-radio" value="White" 
                           <?php echo $selectedColor === 'White' ? 'checked' : ''; ?> onchange="this.form.submit()">
                    <label class="color-dot color-white <?php echo $selectedColor === 'White' ? 'active' : ''; ?>" for="mob-col-white-radio" title="White"></label>
                </div>
                <?php if (!empty($selectedColor)): ?>
                    <div class="mt-2">
                        <input type="radio" class="btn-check" name="color" id="mob-color-clear" value="" onchange="this.form.submit()">
                        <label class="text-decoration-underline text-muted border-0 bg-transparent p-0" for="mob-color-clear" style="font-size: 0.75rem; cursor: pointer;">Clear Color</label>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-grid mt-4">
                <button type="button" class="btn btn-navy py-3 text-uppercase" data-bs-dismiss="offcanvas">Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<!-- Helper Functions for URL manipulation in Javascript -->
<script>
    // JS Helper to update query parameters in URL
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
            return uri + separator + key + "=" + value;
        }
    }

    // PHP function helper tags as fallback
    <?php
    // Helper to generate clean URLs in PHP without a specific parameter
    function removeQueryStringParameterPHP($url, $key) {
        $urlParts = parse_url($url);
        if (!isset($urlParts['query'])) return $url;
        parse_str($urlParts['query'], $params);
        unset($params[$key]);
        $newQuery = http_build_query($params);
        $path = isset($urlParts['path']) ? $urlParts['path'] : '';
        return $path . ($newQuery ? '?' . $newQuery : '');
    }
    ?>
</script>

<?php
// Expose JS-friendly URL cleanups
function removeQueryStringParameter($url, $key) {
    return removeQueryStringParameterPHP($url, $key);
}
?>

<?php include 'includes/footer.php'; ?>
