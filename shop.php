<?php
include 'includes/products_db.php';

// Extract filter parameters with normalization
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';
$selectedSubcategory = isset($_GET['subcategory']) ? trim($_GET['subcategory']) : '';
$selectedSize = isset($_GET['size']) ? trim($_GET['size']) : '';
$selectedColor = isset($_GET['color']) ? trim($_GET['color']) : '';
$priceMax = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 200.00;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Filter products array
$filteredProducts = array_filter($products, function($prod) use ($selectedCategory, $selectedSubcategory, $selectedSize, $selectedColor, $priceMax, $searchQuery) {
    if (!empty($selectedCategory) && strtolower(trim($prod['category'])) !== strtolower($selectedCategory)) {
        return false;
    }
    if (!empty($selectedSubcategory) && strtolower(trim($prod['subcategory'])) !== strtolower($selectedSubcategory)) {
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
if (strtolower($selectedCategory) === 'men') {
    $categoryName = "Men's Collection";
} elseif (strtolower($selectedCategory) === 'women') {
    $categoryName = "Women's Collection";
} elseif (strtolower($selectedCategory) === 'stitched') {
    $categoryName = 'Stitched Fabrics';
} elseif (strtolower($selectedCategory) === 'accessories') {
    $categoryName = 'Accessories';
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
                                <a href="shop.php?category=<?php echo urlencode($selectedCategory); ?>"><?php echo $categoryName; ?></a>
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

            <!-- Sidebar Filters - Desktop -->
            <aside class="col-lg-3 d-none d-lg-block">
                <form method="GET" action="shop.php" id="desktop-filter-form">
                    <?php if (!empty($selectedCategory)): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($selectedCategory); ?>">
                    <?php endif; ?>
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    <?php if (!empty($searchQuery)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <?php endif; ?>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h5 text-uppercase tracking-wider m-0 fw-bold">Filters</h2>
                        <a href="shop.php<?php echo !empty($selectedCategory) ? '?category=' . urlencode($selectedCategory) : ''; ?>" class="text-decoration-underline text-muted" style="font-size: 0.8rem;">Reset All</a>
                    </div>

                    <!-- Collections -->
                    <div class="filter-group">
                        <h3 class="filter-title">Collections</h3>
                        <ul class="list-unstyled d-flex flex-column gap-2" style="font-size: 0.85rem;">
                            <li><a href="shop.php" class="text-navy <?php echo empty($selectedCategory) ? 'fw-bold' : 'opacity-75'; ?>">All Collection</a></li>
                            <li><a href="shop.php?category=men" class="text-navy <?php echo strtolower($selectedCategory) === 'men' ? 'fw-bold' : 'opacity-75'; ?>">Men's Wear</a></li>
                            <li><a href="shop.php?category=women" class="text-navy <?php echo strtolower($selectedCategory) === 'women' ? 'fw-bold' : 'opacity-75'; ?>">Women's Wear</a></li>
                            <li><a href="shop.php?category=accessories" class="text-navy <?php echo strtolower($selectedCategory) === 'accessories' ? 'fw-bold' : 'opacity-75'; ?>">Accessories</a></li>
                            <li><a href="shop.php?category=stitched" class="text-navy <?php echo strtolower($selectedCategory) === 'stitched' ? 'fw-bold' : 'opacity-75'; ?>">Stitched Fabrics</a></li>
                        </ul>
                    </div>

                    <!-- Subcategories -->
                    <?php if (!empty($selectedCategory)): ?>
                        <div class="filter-group">
                            <h3 class="filter-title">Subcategories</h3>
                            <div class="d-flex flex-column gap-2" style="font-size: 0.85rem;">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="subcategory" id="sub-all" value="" <?php echo empty($selectedSubcategory) ? 'checked' : ''; ?> onchange="this.form.submit()">
                                    <label class="form-check-label" for="sub-all">All Items</label>
                                </div>
                                <?php
                                $subs = ['Shirts', 'Pants', 'Eastern Wear', 'Western Wear', 'neckless', 'Flora', 'Gucci'];
                                foreach($subs as $sub): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="subcategory" id="sub-<?php echo strtolower($sub); ?>" value="<?php echo $sub; ?>" <?php echo $selectedSubcategory === $sub ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        <label class="form-check-label" for="sub-<?php echo strtolower($sub); ?>"><?php echo $sub; ?></label>
                                    </div>
                                <?php endforeach; ?>
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
                            $allSizes = ['S', 'M', 'L', 'XL', 'XS', '30', '32', '34', '36', 'One Size'];
                            foreach ($allSizes as $sz):
                                ?>
                                <input type="radio" class="btn-check" name="size" id="size-<?php echo str_replace(' ', '-', $sz); ?>" value="<?php echo $sz; ?>"
                                        <?php echo $selectedSize === $sz ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="size-btn d-flex align-items-center justify-content-center" for="size-<?php echo str_replace(' ', '-', $sz); ?>"><?php echo $sz; ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Colors Filter -->
                    <div class="filter-group">
                        <h3 class="filter-title">Color</h3>
                        <div class="color-selector">
                            <input type="radio" class="btn-check" name="color" id="col-navy" value="Navy" <?php echo $selectedColor === 'Navy' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="color-dot color-navy" for="col-navy"></label>
                            <input type="radio" class="btn-check" name="color" id="col-white" value="White" <?php echo $selectedColor === 'White' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="color-dot color-white" for="col-white"></label>
                        </div>
                    </div>
                </form>
            </aside>

            <!-- Main Products Side -->
            <main class="col-lg-9">
                <div class="d-flex flex-wrap align-items-center justify-content-between pb-4 border-bottom border-light mb-4">
                    <div>
                        <h1 class="h3 text-uppercase tracking-wider text-navy mb-1 fw-bold"><?php echo $categoryName; ?></h1>
                        <span class="text-muted" style="font-size: 0.85rem;">Showing <?php echo count($filteredProducts); ?> items</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-3 mt-sm-0">
                        <select class="form-select form-select-sm border-navy shadow-none" id="sort-select"
                                style="border-radius: 0; min-width: 160px; font-size: 0.8rem; padding: 6px 12px;"
                                onchange="location.href = updateQueryStringParameter(window.location.href, 'sort', this.value)">
                            <option value="default" <?php echo $sort === 'default' ? 'selected' : ''; ?>>Featured</option>
                            <option value="price-asc" <?php echo $sort === 'price-asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price-desc" <?php echo $sort === 'price-desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <div class="row g-4">
                    <?php if (count($filteredProducts) > 0): ?>
                        <?php foreach ($filteredProducts as $prod): ?>
                            <?php render_product_card($prod, 'col-6 col-md-6 col-lg-4'); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <h3 class="h5 text-uppercase tracking-wide text-navy">No products found</h3>
                            <a href="shop.php" class="btn btn-navy mt-3">Reset All Filters</a>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileFilters" aria-labelledby="mobileFiltersLabel" style="width: 300px;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileFiltersLabel">Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form method="GET" action="shop.php">
                <?php if (!empty($selectedCategory)): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($selectedCategory); ?>"><?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Subcategory</label>
                    <select name="subcategory" class="form-select" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="Shirts" <?php echo $selectedSubcategory === 'Shirts' ? 'selected' : ''; ?>>Shirts</option>
                        <option value="Pants" <?php echo $selectedSubcategory === 'Pants' ? 'selected' : ''; ?>>Pants</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Max Price</label>
                    <input type="range" name="price_max" class="form-range" min="50" max="200" value="<?php echo $priceMax; ?>" onchange="this.form.submit()">
                </div>
            </form>
        </div>
    </div>

    <script>
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
    </script>

<?php include 'includes/footer.php'; ?>