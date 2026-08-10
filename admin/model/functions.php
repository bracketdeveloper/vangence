<?php
// Load Composer's autoloader from the single admin/vendor/ folder (one
// level up from model/). That folder contains PHPMailer; the old
// model/vendor/ copy was deleted to avoid confusion. Without this autoloader
// PHPMailer is never loaded and emails silently never send — even though
// orders still succeed.
$vendorAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require $vendorAutoload;
}

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
function getAllProductsforAdmin($conn)
{
    $query = "SELECT p.*, c.category AS category_name 
              FROM `products` p 
              LEFT JOIN `categories` c ON p.category_id = c.category_id 
              ORDER BY p.`created_at` DESC";
    return runSelectQuery($conn, $query);
}
function getAllProducts($conn)
{
    $query = "SELECT p.*, c.category AS category_name 
              FROM `products` p 
              LEFT JOIN `categories` c ON p.category_id = c.category_id 
              WHERE p.`is_active` = 1
              ORDER BY p.`created_at` DESC";
    return runSelectQuery($conn, $query);
}
function getProductByIdforAdmin($conn, $productId)
{
    $query = "SELECT p.*, c.category AS category_name 
              FROM `products` p 
              LEFT JOIN `categories` c ON p.category_id = c.category_id 
              WHERE p.`product_id` = '$productId'";
    return runSelectQuery($conn, $query);
}
function getProductById($conn, $productId)
{
    $query = "SELECT p.*, c.category AS category_name 
              FROM `products` p 
              LEFT JOIN `categories` c ON p.category_id = c.category_id 
              WHERE p.`product_id` = '$productId' AND p.`is_active` = 1";
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
    WHERE p.is_active = 1
    ORDER BY p.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getMaxPrice($conn, $selectedId = 0) {
    if ($selectedId <= 0) {
        $query = "SELECT MAX(selling_price) AS max_price FROM products WHERE is_active = 1";
    } else {
        $allCategoryIds = getAllChildCategoryIds($conn, $selectedId);
        $idList = implode(',', array_map('intval', $allCategoryIds));

        $query = "SELECT MAX(selling_price) AS max_price FROM products WHERE category_id IN ($idList) AND is_active = 1";
    }

    $result = runSelectQuery($conn, $query);

    return ($result && isset($result[0]['max_price']) && $result[0]['max_price'] !== null)
            ? floatval($result[0]['max_price'])
            : 200.00;
}

function getMinPrice($conn, $selectedId = 0) {
    if ($selectedId <= 0) {
        $query = "SELECT MIN(selling_price) AS min_price FROM products WHERE is_active = 1";
    } else {
        $allCategoryIds = getAllChildCategoryIds($conn, $selectedId);
        $idList = implode(',', array_map('intval', $allCategoryIds));

        $query = "SELECT MIN(selling_price) AS min_price FROM products WHERE category_id IN ($idList) AND is_active = 1";
    }

    $result = runSelectQuery($conn, $query);

    return ($result && isset($result[0]['min_price']) && $result[0]['min_price'] !== null)
            ? floatval($result[0]['min_price'])
            : 0.00;
}

function getAvailableSizesForCategory($conn, $selectedId = 0) {
    if ($selectedId > 0) {
        $allCategoryIds = getAllChildCategoryIds($conn, $selectedId);
        $idList = implode(',', array_map('intval', $allCategoryIds));
        $query = "SELECT sizes FROM products WHERE category_id IN ($idList) AND sizes IS NOT NULL AND sizes != '' AND is_active = 1";
    } else {
        $query = "SELECT sizes FROM products WHERE sizes IS NOT NULL AND sizes != '' AND is_active = 1";
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
        $query = "SELECT colors FROM products WHERE category_id IN ($idList) AND colors IS NOT NULL AND colors != '' AND is_active = 1";
    } else {
        $query = "SELECT colors FROM products WHERE colors IS NOT NULL AND colors != '' AND is_active = 1";
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

function getSaleById($conn, $saleId) {
    $saleId = (int)$saleId;
    $query = "SELECT * FROM sales WHERE `sale_id` = $saleId LIMIT 1";
    $result = $conn->query($query);
    if (!$result || $result->num_rows === 0) {
        return null;
    }
    return $result->fetch_assoc();
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

    // Decode sizes and colors for the Add to Cart button / quick-add modal
    $sizesDecoded = json_decode($product['sizes'], true);
    $colorsDecoded = json_decode($product['colors'], true);
    $sizes = is_array($sizesDecoded) ? array_values($sizesDecoded) : [];
    $colors = is_array($colorsDecoded) ? array_values($colorsDecoded) : [];
    $firstSize = !empty($sizes) ? $sizes[0] : 'N/A';
    $firstColor = !empty($colors) ? $colors[0] : 'N/A';

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
                    <a href="product.php?product_id=<?php echo $product['product_id']; ?>" class="text-decoration-none text-navy">
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </a>
                </h3>
                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top border-light">
                    <span class="product-card-price text-navy fw-semibold">
                        PKR <?php echo number_format($product['selling_price'], 2); ?>
                    </span>
                    <button class="btn btn-navy btn-xs btn-add-to-cart text-uppercase"
                            data-id="<?php echo $product['product_id']; ?>"
                            data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                            data-price="<?php echo $product['selling_price']; ?>"
                            data-image="<?php echo $displayImage; ?>"
                            data-size="<?php echo htmlspecialchars($firstSize); ?>"
                            data-color="<?php echo htmlspecialchars($firstColor); ?>"
                            data-sizes='<?php echo htmlspecialchars(json_encode($sizes), ENT_QUOTES, "UTF-8"); ?>'
                            data-colors='<?php echo htmlspecialchars(json_encode($colors), ENT_QUOTES, "UTF-8"); ?>'
                            style="font-size: 0.75rem; letter-spacing: 0.5px; padding: 6px 12px;">
                        <i class="fas fa-cart-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
}
function getHeroSection($conn)
{
    $query = "
        SELECT content_data 
        FROM site_content 
        WHERE page_identifier = 'home' 
        AND section_identifier = 'hero_banner' 
        LIMIT 1
    ";

    $result = runSelectQuery($conn, $query);

    if (!empty($result)) {
        // Decode the JSON string back into an associative array
        return json_decode($result[0]['content_data'], true);
    }

    return null;
}
function getCollectionSection($conn)
{
    $query = "
        SELECT content_data 
        FROM site_content 
        WHERE page_identifier = 'home' 
        AND section_identifier = 'collection_section' 
        LIMIT 1
    ";

    $result = runSelectQuery($conn, $query);

    if (!empty($result)) {
        return json_decode($result[0]['content_data'], true);
    }

    return null;
}

function getProductSection($conn)
{
    $query = "
        SELECT content_data 
        FROM site_content 
        WHERE page_identifier = 'home' 
        AND section_identifier = 'product_section' 
        LIMIT 1
    ";

    $result = runSelectQuery($conn, $query);

    if (!empty($result)) {
        return json_decode($result[0]['content_data'], true);
    }

    return null;
}

function getPhilosophySection($conn)
{
    $query = "
        SELECT content_data 
        FROM site_content 
        WHERE page_identifier = 'home' 
        AND section_identifier = 'philosophy_section' 
        LIMIT 1
    ";

    $result = runSelectQuery($conn, $query);

    if (!empty($result)) {
        return json_decode($result[0]['content_data'], true);
    }

    return null;
}

function getAboutSection($conn)
{
    $query = "
        SELECT content_data 
        FROM site_content 
        WHERE page_identifier = 'home' 
        AND section_identifier = 'about_section' 
        LIMIT 1
    ";

    $result = runSelectQuery($conn, $query);

    if (!empty($result)) {
        return json_decode($result[0]['content_data'], true);
    }

    return null;
}

function getContactSection($conn)
{
    $query = "
        SELECT content_data 
        FROM site_content 
        WHERE page_identifier = 'home' 
        AND section_identifier = 'contact_section' 
        LIMIT 1
    ";

    $result = runSelectQuery($conn, $query);

    if (!empty($result)) {
        return json_decode($result[0]['content_data'], true);
    }

    return null;
}

// ===================== ORDERS (ADMIN) =====================
function getAllOrdersForAdmin($conn)
{
    $query = "SELECT * FROM `orders` ORDER BY `created_at` DESC";
    return runSelectQuery($conn, $query);
}

function getOrderByIdForAdmin($conn, $orderId)
{
    $orderId = mysqli_real_escape_string($conn, $orderId);
    $query = "SELECT * FROM `orders` WHERE `order_id` = '$orderId'";
    return runSelectQuery($conn, $query);
}

function getOrderItemsByOrderId($conn, $orderId)
{
    $orderId = mysqli_real_escape_string($conn, $orderId);
    $query = "SELECT * FROM `order_items` WHERE `order_id` = '$orderId'";
    return runSelectQuery($conn, $query);
}

function updateOrderStatus($conn, $orderId, $status)
{
    $orderId = mysqli_real_escape_string($conn, $orderId);
    $status = mysqli_real_escape_string($conn, $status);
    $query = "UPDATE `orders` SET `order_status` = '$status' WHERE `order_id` = '$orderId'";
    return $conn->query($query);
}

// ===================== EMAIL NOTIFICATIONS =====================

function getMailEnv()
{
    static $env = null;
    if ($env !== null) {
        return $env;
    }

    $envPath = __DIR__ . '/.env';
    $env = file_exists($envPath) ? parse_ini_file($envPath, false, INI_SCANNER_RAW) : [];
    if (!is_array($env)) {
        $env = [];
    }

    return $env;
}

function getSiteUrl(array $env = null)
{
    $env = $env ?? getMailEnv();
    $url = trim($env['SITE_URL'] ?? 'https://www.vangence.com');

    return rtrim($url, '/');
}

function getSupportEmail(array $env = null)
{
    $env = $env ?? getMailEnv();

    return $env['SUPPORT_EMAIL'] ?? ($env['MAIL_USERNAME'] ?? 'concierge@vangence.com');
}

function getAdminEmail(array $env = null)
{
    $env = $env ?? getMailEnv();

    return $env['MAIL_ADMIN'] ?? 'admin@vangence.com';
}

function getOrderConfirmationUrl($orderNumber, array $env = null)
{
    return getSiteUrl($env) . '/order-confirmation.php?order=' . urlencode($orderNumber);
}

function getAdminOrderUrl($orderId, array $env = null)
{
    return getSiteUrl($env) . '/admin/order-details.php?order_id=' . urlencode((string) $orderId);
}

function buildOrderItemsTableHtml(array $items)
{
    $itemsHtml = '';
    foreach ($items as $item) {
        $itemsHtml .= "
        <tr>
            <td style=\"padding:12px 0; border-bottom:1px solid #eee;\">
                <strong style=\"color:#1a2b49;\">" . htmlspecialchars($item['product_name']) . "</strong><br>
                <span style=\"font-size:12px; color:#888;\">Size: " . htmlspecialchars($item['size']) . " &nbsp;|&nbsp; Color: " . htmlspecialchars($item['color']) . " &nbsp;|&nbsp; Qty: " . (int) $item['quantity'] . "</span>
            </td>
            <td style=\"padding:12px 0; border-bottom:1px solid #eee; text-align:right; white-space:nowrap; vertical-align:top;\">
                PKR " . number_format((float) $item['line_total'], 2) . "
            </td>
        </tr>";
    }

    return $itemsHtml;
}

function getPaymentMethodLabel($paymentMethod)
{
    return $paymentMethod === 'cod' ? 'Cash on Delivery' : 'Credit / Debit Card';
}

function getEmailButtonHtml($url, $label)
{
    return "
        <p style=\"text-align:center; margin:28px 0;\">
            <a href=\"" . htmlspecialchars($url) . "\"
               style=\"display:inline-block; background:#1a2b49; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:4px; font-size:14px; font-weight:bold; letter-spacing:0.5px;\">
                {$label}
            </a>
        </p>";
}

function getEmailWrapper($innerHtml, array $env = null)
{
    $env = $env ?? getMailEnv();
    $siteUrl = getSiteUrl($env);
    $supportEmail = getSupportEmail($env);
    $year = date('Y');

    return "
    <div style=\"font-family: Arial, Helvetica, sans-serif; max-width:620px; margin:auto; border:1px solid #e8e8e8; background:#ffffff;\">
        <div style=\"background:#1a2b49; padding:28px 24px; text-align:center;\">
            <a href=\"{$siteUrl}\" style=\"text-decoration:none;\">
                <h1 style=\"color:#ffffff; letter-spacing:4px; font-size:22px; margin:0; text-transform:uppercase; font-weight:600;\">Vangence</h1>
                <p style=\"color:rgba(255,255,255,0.75); font-size:11px; margin:8px 0 0; letter-spacing:2px; text-transform:uppercase;\">Premium Atelier</p>
            </a>
        </div>
        <div style=\"padding:32px 28px;\">
            {$innerHtml}
        </div>
        <div style=\"background:#f7f7f7; padding:20px 24px; text-align:center; font-size:12px; color:#888; line-height:1.7; border-top:1px solid #eee;\">
            &copy; {$year} Vangence. All rights reserved.<br>
            <a href=\"{$siteUrl}\" style=\"color:#1a2b49; text-decoration:none;\">www.vangence.com</a><br>
            Questions? Contact us at <a href=\"mailto:{$supportEmail}\" style=\"color:#1a2b49;\">{$supportEmail}</a>
        </div>
    </div>";
}

function createConfiguredMailer(array $env)
{
    if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
        throw new RuntimeException('PHPMailer is not loaded. Ensure admin/vendor/ is deployed on the server.');
    }

    $mailHost = trim($env['MAIL_HOST'] ?? '');
    $mailUsername = trim($env['MAIL_USERNAME'] ?? '');
    $mailPassword = $env['MAIL_PASSWORD'] ?? '';
    $mailPort = isset($env['MAIL_PORT']) ? (int) $env['MAIL_PORT'] : 465;
    $mailFromName = $env['MAIL_FROM_NAME'] ?? 'Vangence';
    $encryption = strtolower(trim($env['MAIL_ENCRYPTION'] ?? ($mailPort === 587 ? 'tls' : 'ssl')));

    if ($mailHost === '' || $mailUsername === '' || $mailPassword === '') {
        throw new RuntimeException('Missing SMTP configuration in admin/model/.env');
    }

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $mailHost;
    $mail->SMTPAuth = true;
    $mail->Username = $mailUsername;
    $mail->Password = $mailPassword;
    $mail->Port = $mailPort;
    $mail->Timeout = 20;
    $mail->CharSet = 'UTF-8';

    if ($encryption === 'tls') {
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } elseif ($encryption === 'ssl') {
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = false;
        $mail->SMTPAutoTLS = false;
    }

    $mail->setFrom($mailUsername, $mailFromName);

    return $mail;
}

function sendEmail($to, $toName, $subject, $htmlBody, $replyToEmail = null, $replyToName = null)
{
    $env = getMailEnv();
    $to = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', trim($to));

    if ($to === '') {
        error_log('sendEmail: empty recipient address');
        return false;
    }

    try {
        $mail = createConfiguredMailer($env);
        $mail->addAddress($to, $toName);
        $mail->addReplyTo($replyToEmail ?: getSupportEmail($env), $replyToName ?: 'Vangence Support');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = trim(preg_replace('/\s+/', ' ', strip_tags($htmlBody)));
        $mail->addCustomHeader('X-Mailer', 'Vangence-Orders');
        $mail->send();

        error_log('Email sent to: ' . $to . ' | Subject: ' . $subject);
        return true;
    } catch (\Throwable $e) {
        $detail = ($e instanceof \PHPMailer\PHPMailer\Exception && isset($mail))
            ? $mail->ErrorInfo
            : $e->getMessage();
        error_log('sendEmail ERROR for ' . $to . ': ' . $detail);
        return false;
    }
}

function sendEmailWithFallback($to, $toName, $subject, $htmlBody, $replyToEmail = null, $replyToName = null)
{
    if (sendEmail($to, $toName, $subject, $htmlBody, $replyToEmail, $replyToName)) {
        return true;
    }

    $env = getMailEnv();
    $currentPort = isset($env['MAIL_PORT']) ? (int) $env['MAIL_PORT'] : 465;
    $currentEncryption = strtolower(trim($env['MAIL_ENCRYPTION'] ?? ($currentPort === 587 ? 'tls' : 'ssl')));

    $fallbackProfiles = [
        ['port' => 587, 'encryption' => 'tls'],
        ['port' => 465, 'encryption' => 'ssl'],
    ];

    foreach ($fallbackProfiles as $profile) {
        if ($currentPort === $profile['port'] && $currentEncryption === $profile['encryption']) {
            continue;
        }

        $fallbackEnv = $env;
        $fallbackEnv['MAIL_PORT'] = (string) $profile['port'];
        $fallbackEnv['MAIL_ENCRYPTION'] = $profile['encryption'];

        try {
            $mail = createConfiguredMailer($fallbackEnv);
            $mail->addAddress($to, $toName);
            $mail->addReplyTo($replyToEmail ?: getSupportEmail($env), $replyToName ?: 'Vangence Support');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = trim(preg_replace('/\s+/', ' ', strip_tags($htmlBody)));
            $mail->addCustomHeader('X-Mailer', 'Vangence-Orders');
            $mail->send();

            error_log('Email sent via fallback SMTP (port ' . $profile['port'] . '/' . $profile['encryption'] . ') to: ' . $to);
            return true;
        } catch (\Throwable $e) {
            $detail = ($e instanceof \PHPMailer\PHPMailer\Exception && isset($mail))
                ? $mail->ErrorInfo
                : $e->getMessage();
            error_log('sendEmail fallback ERROR (' . $profile['port'] . '/' . $profile['encryption'] . ') for ' . $to . ': ' . $detail);
        }
    }

    return false;
}

function buildOrderEmailSummaryHtml(array $o, array $items, array $env = null)
{
    $env = $env ?? getMailEnv();
    $itemsHtml = buildOrderItemsTableHtml($items);
    $paymentLabel = getPaymentMethodLabel($o['payment_method']);
    $orderDate = date('d M Y, h:i A', strtotime($o['created_at']));
    $statusLabel = strtoupper(htmlspecialchars($o['order_status'] ?? 'pending'));

    return "
        <table style=\"width:100%; margin:0 0 20px; font-size:13px; color:#555; border-collapse:collapse;\">
            <tr><td style=\"padding:6px 0;\">Order Number</td><td style=\"text-align:right; font-weight:bold; color:#1a2b49;\">" . htmlspecialchars($o['order_number']) . "</td></tr>
            <tr><td style=\"padding:6px 0;\">Order Date</td><td style=\"text-align:right;\">{$orderDate}</td></tr>
            <tr><td style=\"padding:6px 0;\">Payment Method</td><td style=\"text-align:right;\">{$paymentLabel}</td></tr>
            <tr><td style=\"padding:6px 0;\">Status</td><td style=\"text-align:right; text-transform:uppercase; font-weight:600;\">{$statusLabel}</td></tr>
        </table>

        <h3 style=\"color:#1a2b49; font-size:14px; margin:24px 0 10px; text-transform:uppercase; letter-spacing:1px;\">Items Ordered</h3>
        <table style=\"width:100%; border-collapse:collapse; font-size:13px; color:#333;\">
            {$itemsHtml}
        </table>

        <table style=\"width:100%; margin-top:18px; font-size:13px; color:#555;\">
            <tr><td style=\"padding:4px 0;\">Subtotal</td><td style=\"text-align:right; padding:4px 0;\">PKR " . number_format((float) $o['subtotal'], 2) . "</td></tr>
            <tr><td style=\"padding:4px 0;\">Shipping</td><td style=\"text-align:right; padding:4px 0;\">PKR " . number_format((float) $o['shipping_cost'], 2) . "</td></tr>
            <tr>
                <td style=\"font-weight:bold; padding-top:10px; border-top:1px solid #ddd;\">Total</td>
                <td style=\"text-align:right; font-weight:bold; padding-top:10px; border-top:1px solid #ddd; color:#1a2b49; font-size:15px;\">PKR " . number_format((float) $o['total_amount'], 2) . "</td>
            </tr>
        </table>

        <h3 style=\"color:#1a2b49; font-size:14px; margin:28px 0 8px; text-transform:uppercase; letter-spacing:1px;\">Shipping Address</h3>
        <p style=\"color:#555; font-size:13px; line-height:1.7; margin:0; background:#fafafa; padding:14px 16px; border-radius:4px;\">
            " . htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) . "<br>
            " . htmlspecialchars($o['address']) . "<br>
            " . htmlspecialchars($o['city'] . ', ' . $o['state']) . "<br>
            Phone: " . htmlspecialchars($o['phone']) . "<br>
            Email: " . htmlspecialchars($o['email']) . "
        </p>";
}

function sendAdminNewOrderNotificationEmail($conn, $orderId)
{
    $orderResult = getOrderByIdForAdmin($conn, $orderId);
    if (empty($orderResult)) {
        return false;
    }

    $env = getMailEnv();
    $adminEmail = getAdminEmail($env);
    $o = $orderResult[0];
    $items = getOrderItemsByOrderId($conn, $orderId);
    $summaryHtml = buildOrderEmailSummaryHtml($o, $items, $env);
    $adminOrderUrl = getAdminOrderUrl($orderId, $env);
    $customerName = htmlspecialchars($o['first_name'] . ' ' . $o['last_name']);

    $inner = "
        <h2 style=\"color:#1a2b49; font-size:18px; margin-top:0;\">New Order Received</h2>
        <p style=\"color:#555; font-size:14px; line-height:1.6;\">
            A new order has been placed on <strong>Vangence</strong>. Please review and process it in the admin panel.
        </p>
        <p style=\"color:#555; font-size:14px;\">
            Customer: <strong>{$customerName}</strong><br>
            Order: <strong>" . htmlspecialchars($o['order_number']) . "</strong>
        </p>
        {$summaryHtml}
        " . getEmailButtonHtml($adminOrderUrl, 'View Order in Admin') . "
        <p style=\"font-size:12px; color:#999; text-align:center; margin:0;\">
            Admin link: <a href=\"" . htmlspecialchars($adminOrderUrl) . "\" style=\"color:#1a2b49;\">" . htmlspecialchars($adminOrderUrl) . "</a>
        </p>";

    return sendEmailWithFallback(
        $adminEmail,
        'Vangence Admin',
        'New Order: ' . $o['order_number'] . ' — PKR ' . number_format((float) $o['total_amount'], 2),
        getEmailWrapper($inner, $env)
    );
}

function sendOrderConfirmationEmail($conn, $orderId)
{
    $orderResult = getOrderByIdForAdmin($conn, $orderId);
    if (empty($orderResult)) {
        return false;
    }

    $env = getMailEnv();
    $o = $orderResult[0];
    $items = getOrderItemsByOrderId($conn, $orderId);
    $summaryHtml = buildOrderEmailSummaryHtml($o, $items, $env);
    $trackingUrl = getOrderConfirmationUrl($o['order_number'], $env);
    $supportEmail = getSupportEmail($env);

    $inner = "
        <h2 style=\"color:#1a2b49; font-size:18px; margin-top:0;\">Thank you for your order, " . htmlspecialchars($o['first_name']) . "!</h2>
        <p style=\"color:#555; font-size:14px; line-height:1.7;\">
            We have received your order and our team is preparing it with care. You will receive another email when your order status changes or when it ships.
        </p>
        <p style=\"color:#555; font-size:14px; line-height:1.7;\">
            If you selected Cash on Delivery, please keep the exact amount ready at the time of delivery.
        </p>
        {$summaryHtml}
        " . getEmailButtonHtml($trackingUrl, 'View Your Order') . "
        <p style=\"font-size:13px; color:#777; line-height:1.6; margin-top:24px;\">
            You can bookmark your order page to check status anytime:<br>
            <a href=\"" . htmlspecialchars($trackingUrl) . "\" style=\"color:#1a2b49;\">" . htmlspecialchars($trackingUrl) . "</a>
        </p>
        <p style=\"font-size:13px; color:#777; line-height:1.6;\">
            Need assistance? Email us at <a href=\"mailto:{$supportEmail}\" style=\"color:#1a2b49;\">{$supportEmail}</a> and include your order number <strong>" . htmlspecialchars($o['order_number']) . "</strong>.
        </p>";

    $customerSent = sendEmailWithFallback(
        $o['email'],
        $o['first_name'] . ' ' . $o['last_name'],
        'Order Confirmed — ' . $o['order_number'] . ' | Vangence',
        getEmailWrapper($inner, $env)
    );

    $adminSent = sendAdminNewOrderNotificationEmail($conn, $orderId);
    error_log('New order emails for ' . $o['order_number'] . ' — customer: ' . ($customerSent ? 'SUCCESS' : 'FAILED') . ', admin: ' . ($adminSent ? 'SUCCESS' : 'FAILED'));

    return $customerSent;
}

function sendOrderStatusUpdateEmail($conn, $orderId)
{
    $orderResult = getOrderByIdForAdmin($conn, $orderId);
    if (empty($orderResult)) {
        error_log('Order not found for status email: ' . $orderId);
        return false;
    }

    $env = getMailEnv();
    $o = $orderResult[0];
    $trackingUrl = getOrderConfirmationUrl($o['order_number'], $env);
    $supportEmail = getSupportEmail($env);

    $statusMessages = [
        'pending'    => 'Your order is awaiting confirmation. We will notify you as soon as it moves to processing.',
        'processing' => 'Great news — your order is now being prepared in our atelier and will ship soon.',
        'shipped'    => 'Your order is on its way. You will receive delivery updates from our courier partner shortly.',
        'delivered'  => 'Your order has been delivered. We hope you enjoy your Vangence pieces. Thank you for shopping with us.',
        'cancelled'  => 'Your order has been cancelled. If this was unexpected or you need help placing a new order, please contact us.',
    ];

    $statusMessage = $statusMessages[$o['order_status']] ?? 'Your order status has been updated.';
    $statusLabel = strtoupper(htmlspecialchars($o['order_status']));

    $inner = "
        <h2 style=\"color:#1a2b49; font-size:18px; margin-top:0;\">Your Order Has Been Updated</h2>
        <p style=\"color:#555; font-size:14px; line-height:1.7;\">
            Hi " . htmlspecialchars($o['first_name']) . ",
        </p>
        <p style=\"color:#555; font-size:14px; line-height:1.7;\">
            The status of your order <strong>" . htmlspecialchars($o['order_number']) . "</strong> has changed.
        </p>
        <p style=\"text-align:center; margin:20px 0;\">
            <span style=\"display:inline-block; background:#1a2b49; color:#ffffff; padding:8px 22px; border-radius:4px; font-size:13px; font-weight:bold; letter-spacing:1px;\">
                {$statusLabel}
            </span>
        </p>
        <p style=\"color:#555; font-size:14px; line-height:1.7;\">
            {$statusMessage}
        </p>
        " . getEmailButtonHtml($trackingUrl, 'Track Your Order') . "
        <p style=\"font-size:13px; color:#777; line-height:1.6;\">
            Questions about this update? Contact <a href=\"mailto:{$supportEmail}\" style=\"color:#1a2b49;\">{$supportEmail}</a> and reference order <strong>" . htmlspecialchars($o['order_number']) . "</strong>.
        </p>";

    return sendEmailWithFallback(
        $o['email'],
        $o['first_name'] . ' ' . $o['last_name'],
        'Order Update — ' . $statusLabel . ' | ' . $o['order_number'],
        getEmailWrapper($inner, $env)
    );
}
?>
