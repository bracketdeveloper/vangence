<?php
require_once("admin/model/functions.php");
// Extract filter parameters
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';
$selectedId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$selectedSize = isset($_GET['size']) ? trim($_GET['size']) : '';
$selectedColor = isset($_GET['color']) ? trim($_GET['color']) : '';
$realMaxPrice = getMaxPrice($conn, $selectedId);
$realMinPrice = getMinPrice($conn, $selectedId);
$priceMax = isset($_GET['price_max']) ? floatval($_GET['price_max']) : $realMaxPrice;
$priceMin = isset($_GET['price_min']) ? floatval($_GET['price_min']) : $realMinPrice;
$availableSizes = getAvailableSizesForCategory($conn, $selectedId);
$availableColors = getAvailableColorsForCategory($conn, $selectedId);
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
// 1. Fetch products (either hierarchy-based or all)
if ($selectedId > 0) {
    $products = getProductsByHierarchy($conn, $selectedId);
} else {
    $products = getAllProducts($conn);
}
// 2. Filter products array
$filteredProducts = array_filter($products, function($prod) use ($selectedSize, $selectedColor, $priceMax, $priceMin, $searchQuery) {
    if ($prod['selling_price'] < $priceMin || $prod['selling_price'] > $priceMax) {
        return false;
    }
    $prodSizes = json_decode($prod['sizes'], true);
    if (!empty($selectedSize) && (!is_array($prodSizes) || !in_array($selectedSize, $prodSizes))) {
        return false;
    }
    $prodColors = json_decode($prod['colors'], true);
    if (!empty($selectedColor) && (!is_array($prodColors) || !in_array($selectedColor, $prodColors))) {
        return false;
    }
    if (!empty($searchQuery)) {
        $nameMatch = strpos(strtolower($prod['product_name']), strtolower($searchQuery)) !== false;
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
        return $a['selling_price'] <=> $b['selling_price'];
    });
} elseif ($sort === 'price-desc') {
    usort($filteredProducts, function($a, $b) {
        return $b['selling_price'] <=> $a['selling_price'];
    });
}
// Generate category display name
$categoryName = !empty($selectedCategory) ? ucfirst($selectedCategory) . ' Collection' : 'All Products';
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
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo $categoryName; ?>
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
                    <?php if ($selectedId > 0): ?>
                        <input type="hidden" name="id" value="<?php echo $selectedId; ?>">
                    <?php endif; ?>
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    <?php if (!empty($searchQuery)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h5 text-uppercase tracking-wider m-0 fw-bold">Filters</h2>
                        <a href="shop.php" class="text-decoration-underline text-muted" style="font-size: 0.8rem;">Reset All</a>
                    </div>
                    <!-- Price Filter -->
                    <div class="filter-group">
                        <h3 class="filter-title">Max Price</h3>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span style="font-size: 0.85rem;">Up to:</span>
                            <span class="fw-bold text-navy" id="price-val">$<?php echo number_format($priceMax, 2); ?></span>
                        </div>
                        <input type="range" class="form-range" min="<?php echo ceil($realMinPrice); ?>" max="<?php echo ceil($realMaxPrice); ?>" step="10" name="price_max" id="price-slider"
                               value="<?php echo $priceMax; ?>"
                               oninput="document.getElementById('price-val').innerText = '$' + parseFloat(this.value).toFixed(2)"
                               onchange="this.form.submit()">
                    </div>
                    <!-- Sizes Filter -->
                    <div class="filter-group">
                        <h3 class="filter-title">Size</h3>
                        <div class="size-grid">
                            <?php

                            foreach ($availableSizes as $sz):
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
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach ($availableColors as $col):
                                $colId = strtolower(str_replace(' ', '-', $col));
                                ?>
                                <div class="d-flex flex-column align-items-center">
                                    <input type="radio" class="btn-check" name="color" id="col-<?php echo $colId; ?>"
                                           value="<?php echo $col; ?>" <?php echo $selectedColor === $col ? 'checked' : ''; ?>
                                           onchange="this.form.submit()">

                                    <label class="color-box <?php echo ($selectedColor === $col) ? 'active' : ''; ?>"
                                           for="col-<?php echo $colId; ?>"
                                           style="background-color: <?php echo $col; ?>; margin-right: 0;"
                                           title="<?php echo $col; ?>"></label>

                                    <span style="font-size: 0.75rem; margin-top: 5px;"><?php echo $col; ?></span>
                                </div>
                            <?php endforeach; ?>
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
                    <button class="btn btn-outline-navy d-lg-none mb-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters">Filters</button>
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
                <?php if ($selectedId > 0): ?><input type="hidden" name="id" value="<?php echo $selectedId; ?>"><?php endif; ?>
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label mb-0">Max Price</label>
                        <span class="fw-bold text-navy" id="mobile-price-val">$<?php echo number_format($priceMax, 2); ?></span>
                    </div>
                    <input type="range" name="price_max" class="form-range" min="<?php echo ceil($realMinPrice); ?>" max="<?php echo ceil($realMaxPrice); ?>" value="<?php echo $priceMax; ?>"
                           oninput="document.getElementById('mobile-price-val').innerText = '$' + parseFloat(this.value).toFixed(2)"
                           onchange="this.form.submit()">
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Size</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php
                        foreach ($availableSizes as $sz): ?>
                            <a href="?category=<?php echo urlencode($selectedCategory); ?>&id=<?php echo $selectedId; ?>&size=<?php echo urlencode($sz); ?>&price_max=<?php echo $priceMax; ?>&color=<?php echo urlencode($selectedColor); ?>"
                               class="btn btn-sm <?php echo $selectedSize === $sz ? 'btn-navy' : 'btn-outline-secondary'; ?>"><?php echo $sz; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Color</label>
                    <div class="d-flex gap-2">
                        <?php foreach ($availableColors as $col): ?>
                            <input type="radio" class="btn-check" name="color" id="mobile-col-<?php echo $col; ?>"
                                   value="<?php echo $col; ?>" <?php echo $selectedColor === $col ? 'checked' : ''; ?>
                                   onchange="this.form.submit()">
                            <label class="btn btn-sm <?php echo $selectedColor === $col ? 'btn-navy' : 'btn-outline-secondary'; ?>"
                                   for="mobile-col-<?php echo $col; ?>">
                                <?php echo $col; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </form>
        </div>
    </div><script>
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