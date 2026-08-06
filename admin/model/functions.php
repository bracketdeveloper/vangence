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

// Low-level sender. Both template functions below call this — SMTP setup
// lives here ONLY, so it never gets duplicated.
//
// IMPORTANT: this requires PHPMailer. If the vendor/ folder is not present on
// the server (composer install was never run there), PHPMailer won't be loaded
// and NO email will be sent — the order itself still succeeds, which is why
// you see "order placed but no email". Run this once on the server:
//     composer require phpmailer/phpmailer
// As a safety net, if PHPMailer is genuinely unavailable we fall back to
// PHP's built-in mail() so at least something goes out instead of silence.
function sendEmail($to, $toName, $subject, $htmlBody)
{
    // Load credentials from .env — keeps secrets out of version control.
    // INI_SCANNER_RAW stops parse_ini_file from mangling values that contain
    // special characters (e.g. the @ in the mailbox password).
    $envPath = __DIR__ . '/.env';
    $env = file_exists($envPath) ? parse_ini_file($envPath, false, INI_SCANNER_RAW) : [];

    $mailHost     = isset($env['MAIL_HOST'])     ? trim($env['MAIL_HOST'])     : '';
    $mailUsername = isset($env['MAIL_USERNAME']) ? trim($env['MAIL_USERNAME']) : '';
    $mailPassword = isset($env['MAIL_PASSWORD']) ? trim($env['MAIL_PASSWORD']) : '';
    $mailPort     = isset($env['MAIL_PORT'])     ? intval($env['MAIL_PORT'])   : 587;
    $mailFromName = isset($env['MAIL_FROM_NAME']) ? trim($env['MAIL_FROM_NAME']) : '';

    // ---- Preferred path: PHPMailer over SMTP ----
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $mailHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $mailUsername;
            $mail->Password   = $mailPassword;
            $mail->Port       = $mailPort;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            // Let PHPMailer upgrade plain connections to TLS automatically if the
            // server advertises it — prevents silent handshake failures on port 587.
            $mail->SMTPAutoTLS = true;
            $mail->CharSet    = 'UTF-8';
            $mail->Encoding   = 'base64';

            $mail->setFrom($mailUsername, $mailFromName);
            $mail->addAddress($to, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            // Plain-text fallback for email clients that don't render HTML.
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            return true;
        } catch (\Throwable $e) {
            // Log the full reason so you can actually see why it failed instead
            // of guessing. Check your PHP error log.
            error_log('sendEmail SMTP failed: ' . $e->getMessage() . ' | Mailer Error: ' . (isset($mail) ? $mail->ErrorInfo : ''));
            // Fall through to the mail() fallback below — don't give up silently.
        }
    } else {
        error_log('sendEmail: PHPMailer is not available (vendor/autoload.php missing or composer install not run). Falling back to mail().');
    }

    // ---- Fallback path: PHP built-in mail() ----
    // Used only if PHPMailer isn't installed OR SMTP threw. Requires a working
    // MTA (sendmail/postfix) on the server. Returns true/false; we log the
    // outcome either way so you can trace what happened.
    $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . ($mailFromName !== '' ? $mailFromName . ' <' . $mailUsername . '>' : $mailUsername),
    ];
    $ok = @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
    if (!$ok) {
        error_log('sendEmail mail() fallback also failed for: ' . $to);
    }
    return $ok;
}

// Shared email header/footer wrapper so both templates look consistent.
function getEmailWrapper($innerHtml)
{
    return "
    <div style=\"font-family: Arial, Helvetica, sans-serif; max-width:600px; margin:auto; border:1px solid #eee;\">
        <div style=\"background:#1a2b49; padding:24px; text-align:center;\">
            <h1 style=\"color:#ffffff; letter-spacing:3px; font-size:20px; margin:0; text-transform:uppercase;\">Vangence</h1>
        </div>
        <div style=\"padding:28px 24px;\">
            {$innerHtml}
        </div>
        <div style=\"background:#f7f7f7; padding:16px; text-align:center; font-size:12px; color:#999;\">
            &copy; " . date('Y') . " Vangence. All rights reserved.<br>
            This is an automated message, please do not reply directly to this email.
        </div>
    </div>";
}

// Sends the full order-confirmation email — called right after an order is placed.
function sendOrderConfirmationEmail($conn, $orderId)
{
    $orderResult = getOrderByIdForAdmin($conn, $orderId);
    if (empty($orderResult)) {
        return false;
    }
    $o = $orderResult[0];
    $items = getOrderItemsByOrderId($conn, $orderId);

    $itemsHtml = '';
    foreach ($items as $item) {
        $itemsHtml .= "
        <tr>
            <td style=\"padding:10px 0; border-bottom:1px solid #eee;\">
                " . htmlspecialchars($item['product_name']) . "<br>
                <span style=\"font-size:12px; color:#888;\">Size: " . htmlspecialchars($item['size']) . " | Color: " . htmlspecialchars($item['color']) . " | Qty: " . (int) $item['quantity'] . "</span>
            </td>
            <td style=\"padding:10px 0; border-bottom:1px solid #eee; text-align:right; white-space:nowrap;\">
                PKR " . number_format($item['line_total'], 2) . "
            </td>
        </tr>";
    }

    $paymentLabel = $o['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Credit Card';

    $inner = "
        <h2 style=\"color:#1a2b49; font-size:18px; margin-top:0;\">Thank you for your order, " . htmlspecialchars($o['first_name']) . "!</h2>
        <p style=\"color:#555; font-size:14px; line-height:1.5;\">We've received your order and it's being processed. Here's a summary:</p>

        <table style=\"width:100%; margin:16px 0; font-size:13px; color:#555;\">
            <tr><td>Order Number</td><td style=\"text-align:right; font-weight:bold; color:#1a2b49;\">" . htmlspecialchars($o['order_number']) . "</td></tr>
            <tr><td>Order Date</td><td style=\"text-align:right;\">" . date('d M Y, h:i A', strtotime($o['created_at'])) . "</td></tr>
            <tr><td>Payment Method</td><td style=\"text-align:right;\">{$paymentLabel}</td></tr>
        </table>

        <table style=\"width:100%; border-collapse:collapse; font-size:13px; color:#333;\">
            {$itemsHtml}
        </table>

        <table style=\"width:100%; margin-top:16px; font-size:13px; color:#555;\">
            <tr><td style=\"padding:4px 0;\">Subtotal</td><td style=\"text-align:right; padding:4px 0;\">PKR " . number_format($o['subtotal'], 2) . "</td></tr>
            <tr><td style=\"padding:4px 0;\">Shipping</td><td style=\"text-align:right; padding:4px 0;\">PKR " . number_format($o['shipping_cost'], 2) . "</td></tr>
            <tr>
                <td style=\"font-weight:bold; padding-top:10px; border-top:1px solid #ddd;\">Total</td>
                <td style=\"text-align:right; font-weight:bold; padding-top:10px; border-top:1px solid #ddd; color:#1a2b49;\">PKR " . number_format($o['total_amount'], 2) . "</td>
            </tr>
        </table>

        <h3 style=\"color:#1a2b49; font-size:15px; margin-top:28px; margin-bottom:6px;\">Shipping To</h3>
        <p style=\"color:#555; font-size:13px; line-height:1.6; margin:0;\">
            " . htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) . "<br>
            " . htmlspecialchars($o['address']) . "<br>
            " . htmlspecialchars($o['city'] . ', ' . $o['state']) . "<br>
            " . htmlspecialchars($o['phone']) . "
        </p>
    ";

    return sendEmail(
            $o['email'],
            $o['first_name'] . ' ' . $o['last_name'],
            "Order Confirmed \u2014 " . $o['order_number'],
            getEmailWrapper($inner)
    );
}

// Sends a short status-update email — called whenever the admin changes order_status.
function sendOrderStatusUpdateEmail($conn, $orderId)
{
    $orderResult = getOrderByIdForAdmin($conn, $orderId);
    if (empty($orderResult)) {
        return false;
    }
    $o = $orderResult[0];

    $statusMessages = [
            'pending'    => 'Your order is pending confirmation.',
            'processing' => 'Your order is being processed and prepared for shipment.',
            'shipped'    => 'Your order is on its way!',
            'delivered'  => 'Your order has been delivered. We hope you enjoy it!',
            'cancelled'  => 'Your order has been cancelled.',
    ];
    $statusMessage = $statusMessages[$o['order_status']] ?? 'Your order status has been updated.';

    $inner = "
        <h2 style=\"color:#1a2b49; font-size:18px; margin-top:0;\">Order Update</h2>
        <p style=\"color:#555; font-size:14px; line-height:1.5;\">
            Hi " . htmlspecialchars($o['first_name']) . ", your order <strong>" . htmlspecialchars($o['order_number']) . "</strong> status has changed to:
        </p>
        <p style=\"display:inline-block; background:#1a2b49; color:#ffffff; padding:6px 18px; border-radius:4px; text-transform:uppercase; font-size:13px; letter-spacing:1px; margin:8px 0;\">
            " . htmlspecialchars($o['order_status']) . "
        </p>
        <p style=\"color:#555; font-size:14px; line-height:1.5; margin-top:16px;\">{$statusMessage}</p>
    ";

    return sendEmail(
            $o['email'],
            $o['first_name'] . ' ' . $o['last_name'],
            "Order " . $o['order_number'] . " \u2014 Status Update",
            getEmailWrapper($inner)
    );
}
?>
