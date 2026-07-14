<?php
require __DIR__ . '/vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorSVG;

require_once "db_connections.php";

function getAllCategories($conn)
{
    $query = "
        SELECT 
        c.*,
        p.category AS parent_name
        FROM categories c
        LEFT JOIN categories p 
            ON c.parent_id = p.category_id
        ORDER BY 
            CASE 
                WHEN c.parent_id IS NULL THEN c.category_id 
                ELSE c.parent_id 
            END,
            c.parent_id IS NOT NULL,
            c.category;
    ";

    return runSelectQuery($conn, $query);
}

function getCategoryById($conn, $categoryId)
{
    $query = "
        SELECT 
            c.*,
            p.category AS parent_name
        FROM categories c
        LEFT JOIN categories p 
            ON c.parent_id = p.category_id
        WHERE c.category_id = '$categoryId'
    ";

    return runSelectQuery($conn, $query);
}

function getAllProducts($conn)
{
    $query = "SELECT p.*, c.category AS category_name 
              FROM `products` p 
              LEFT JOIN `categories` c ON p.category_id = c.category_id 
              ORDER BY p.`created_at` DESC";
    return runSelectQuery($conn, $query);
}

function getProductById($conn, $productId)
{
    $query = "SELECT p.*, c.category AS category_name 
              FROM `products` p 
              LEFT JOIN `categories` c ON p.category_id = c.category_id 
              WHERE p.`product_id` = '$productId'";
    return runSelectQuery($conn, $query);
}
function getProductByName($conn, $productName)
{
    $query = "SELECT p.*, c.category AS category_name 
              FROM `products` p 
              LEFT JOIN `categories` c ON p.category_id = c.category_id 
              WHERE p.`product_name` = '$productName'";
    return runSelectQuery($conn, $query);
}
function getProductsByHierarchy($conn, $category_id)
{
    // This query finds the parent, its children, and grandchildren recursively,
    // then joins the products table to get all items in that tree.
    $query = "WITH RECURSIVE CategoryTree AS (
        SELECT category_id FROM categories WHERE category_id = ?
        UNION ALL
        SELECT c.category_id FROM categories c
        INNER JOIN CategoryTree ct ON c.parent_id = ct.category_id
    )
    SELECT p.*, c.category AS category_name 
    FROM products p
    JOIN CategoryTree ct ON p.category_id = ct.category_id
    JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function getMaxPrice($conn, $selectedId = 0) {
    if ($selectedId <= 0) {
        $query = "SELECT MAX(selling_price) AS max_price FROM products";
    } else {
        // Get all category IDs including the parent and all descendants
        $allCategoryIds = getAllChildCategoryIds($conn, $selectedId);
        $idList = implode(',', array_map('intval', $allCategoryIds));

        $query = "SELECT MAX(selling_price) AS max_price FROM products WHERE category_id IN ($idList)";
    }

    $result = runSelectQuery($conn, $query);

    return ($result && isset($result[0]['max_price']) && $result[0]['max_price'] !== null)
            ? floatval($result[0]['max_price'])
            : 200.00;
}
function getMinPrice($conn, $selectedId = 0) {
    if ($selectedId <= 0) {
        $query = "SELECT MIN(selling_price) AS min_price FROM products";
    } else {
        // Use the same recursive helper function created for getMaxPrice
        $allCategoryIds = getAllChildCategoryIds($conn, $selectedId);
        $idList = implode(',', array_map('intval', $allCategoryIds));

        $query = "SELECT MIN(selling_price) AS min_price FROM products WHERE category_id IN ($idList)";
    }

    $result = runSelectQuery($conn, $query);

    // Default to 0.00 if no products found
    return ($result && isset($result[0]['min_price']) && $result[0]['min_price'] !== null)
            ? floatval($result[0]['min_price'])
            : 0.00;
}
function getAvailableSizesForCategory($conn, $selectedId = 0) {
    if ($selectedId > 0) {
        $allCategoryIds = getAllChildCategoryIds($conn, $selectedId);
        $idList = implode(',', array_map('intval', $allCategoryIds));
        $query = "SELECT sizes FROM products WHERE category_id IN ($idList) AND sizes IS NOT NULL AND sizes != ''";
    } else {
        $query = "SELECT sizes FROM products WHERE sizes IS NOT NULL AND sizes != ''";
    }

    $result = runSelectQuery($conn, $query);
    $sizes = [];
    if ($result) {
        foreach ($result as $row) {
            $decoded = json_decode($row['sizes'], true);
            if (is_array($decoded)) {
                foreach ($decoded as $s) { $sizes[$s] = true; }
            }
        }
    }
    return array_keys($sizes);
}

function getAvailableColorsForCategory($conn, $selectedId = 0) {
    if ($selectedId > 0) {
        $allCategoryIds = getAllChildCategoryIds($conn, $selectedId);
        $idList = implode(',', array_map('intval', $allCategoryIds));
        $query = "SELECT colors FROM products WHERE category_id IN ($idList) AND colors IS NOT NULL AND colors != ''";
    } else {
        $query = "SELECT colors FROM products WHERE colors IS NOT NULL AND colors != ''";
    }

    $result = runSelectQuery($conn, $query);
    $colors = [];
    if ($result) {
        foreach ($result as $row) {
            $decoded = json_decode($row['colors'], true);
            if (is_array($decoded)) {
                foreach ($decoded as $c) { $colors[$c] = true; }
            }
        }
    }
    return array_keys($colors);
}
// Ensure you have this helper function to find all sub-categories
function getAllChildCategoryIds($conn, $parentId) {
    $ids = [$parentId];
    $query = "SELECT category_id FROM categories WHERE parent_id = " . intval($parentId);
    $children = runSelectQuery($conn, $query);

    if ($children) {
        foreach ($children as $child) {
            $ids = array_merge($ids, getAllChildCategoryIds($conn, $child['category_id']));
        }
    }
    return $ids;
}
function getCategoryIdByName($conn, $categoryName) {
    $stmt = $conn->prepare("SELECT category_id FROM categories WHERE category = ? LIMIT 1");
    $stmt->bind_param("s", $categoryName);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['category_id'] : null;
}

function getAllUsers($conn)
{
    $query = "SELECT * FROM `users`";
    return runSelectQuery($conn, $query);
}

function runSelectQuery($conn, $query)
{
    $result = mysqli_query($conn, $query);

    if (!$result) {
        return [];
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

function deleteUnusedProductImages($folder, $dbImages)
{
    $allImages = glob($folder . "*.{jpg,jpeg,png,webp,gif}", GLOB_BRACE);

    foreach ($allImages as $filePath) {
        $fileName = basename($filePath);
        if (!in_array($fileName, $dbImages)) {
            unlink($filePath);
        }
    }
}

function getProductImagesToDelete($conn)
{
    $productDetails = getAllProducts($conn);
    $images = array();
    foreach ($productDetails as $product) {
        $decoded = json_decode($product['image'], true);
        if (is_array($decoded)) {
            $images = array_merge($images, $decoded);
        }
    }
    deleteUnusedProductImages("../uploads/", $images);
}

function showAlret($message)
{
    echo "<script>alert('$message');</script>";
}

function getSpecificUserById($conn, $userId)
{
    $query = "SELECT * FROM `users` WHERE `user_id` = '$userId'";
    $result = mysqli_query($conn, $query);
    echo mysqli_error($conn);
    $data = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        {
            $data[] = $row;
        }
    }
    return $data;
}

function getAllSales($conn)
{
    $query = "SELECT * FROM `sales` ORDER BY `sale_id` DESC ";
    return runSelectQuery($conn, $query);
}

function getSaleById($conn, $saleId)
{
    $query = "SELECT * FROM `sales` WHERE `sale_id` = '$saleId'";
    return runSelectQuery($conn, $query);
}

function generateProductBarcode()
{
    // 12 digits for EAN13 (last digit becomes checksum)
    return str_pad(rand(0, 999999999999), 12, "0", STR_PAD_LEFT);
}

function generateBarcodeImage($barcodeNumber)
{
//    require 'vendor/autoload.php';
//
//    $generator = new BarcodeGeneratorSVG();
//    $barcode = $generator->getBarcode($barcodeNumber, $generator::TYPE_EAN_13, true);
//    $path = "../barcodes/" . $barcodeNumber . ".svg";
//
//    file_put_contents($path, $barcode);
}
function render_product_card($product, $colClass = 'col-6 col-md-4 col-lg-3') {
    // Decode the image JSON string to get the first image
    $images = json_decode($product['image'], true);
    $displayImage = (is_array($images) && count($images) > 0) ? $images[0] : 'default.jpg';

    // Decode sizes and colors for the Add to Cart button
    $sizes = json_decode($product['sizes'], true);
    $colors = json_decode($product['colors'], true);
    $firstSize = is_array($sizes) ? $sizes[0] : 'N/A';
    $firstColor = is_array($colors) ? $colors[0] : 'N/A';

    ?>
    <div class="<?php echo $colClass; ?> mb-4 d-flex">
        <div class="product-card w-100 d-flex flex-column">
            <div class="product-card-img-wrapper position-relative overflow-hidden bg-light">
                <a href="product.php?product_id=<?php echo $product['product_id']; ?>" class="d-block w-100 h-100">
                    <img src="admin/uploads/<?php echo $displayImage; ?>"
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                         class="product-card-img img-fluid w-100 h-100 object-fit-cover"
                         loading="lazy">
                </a>
            </div>
            <div class="product-card-info p-3 d-flex flex-column flex-grow-1">
                <span class="product-card-category mb-1 text-uppercase tracking-widest text-muted" style="font-size: 0.7rem; font-weight: 500;">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </span>
                <h3 class="product-card-title h6 mb-2 flex-grow-1">
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-navy">
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </a>
                </h3>
                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top border-light">
                    <span class="product-card-price text-navy fw-semibold">
                        $<?php echo number_format($product['selling_price'], 2); ?>
                    </span>
                    <button class="btn btn-navy btn-sm btn-add-to-cart text-uppercase"
                            data-id="<?php echo $product['id']; ?>"
                            data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                            data-price="<?php echo $product['selling_price']; ?>"
                            data-image="<?php echo $displayImage; ?>"
                            data-size="<?php echo $firstSize; ?>"
                            data-color="<?php echo $firstColor; ?>"
                            style="font-size: 0.75rem; letter-spacing: 0.5px; padding: 6px 12px;">
                        Add To Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>


