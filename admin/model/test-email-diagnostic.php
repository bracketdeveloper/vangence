<?php
/**
 * SMTP diagnostic tool for Vangence order emails.
 *
 * Usage (replace YOUR_KEY before running, then delete this file when done):
 *   https://www.vangence.com/admin/model/test-email-diagnostic.php?key=YOUR_KEY
 *   https://www.vangence.com/admin/model/test-email-diagnostic.php?key=YOUR_KEY&to=you@example.com
 */
declare(strict_types=1);

$requiredKey = 'vangence-mail-test-2026';
if (($_GET['key'] ?? '') !== $requiredKey) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Forbidden. Append ?key={$requiredKey} to run this diagnostic.\n";
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

$vendorAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require $vendorAutoload;
}

require_once __DIR__ . '/functions.php';

function diagLine(string $label, string $value, bool $ok = true): string
{
    $color = $ok ? '#1e7e34' : '#c0392b';
    $status = $ok ? 'OK' : 'FAIL';
    return '<tr><td style="padding:8px 12px;border-bottom:1px solid #eee;font-weight:600;">'
            . htmlspecialchars($label)
            . '</td><td style="padding:8px 12px;border-bottom:1px solid #eee;">'
            . htmlspecialchars($value)
            . '</td><td style="padding:8px 12px;border-bottom:1px solid #eee;color:'
            . $color
            . ';font-weight:bold;">'
            . $status
            . '</td></tr>';
}

$env = getMailEnv();
$checks = [];
$checks[] = diagLine('.env file', file_exists(__DIR__ . '/.env') ? 'Found at admin/model/.env' : 'Missing', file_exists(__DIR__ . '/.env'));
$checks[] = diagLine('PHPMailer', class_exists('\PHPMailer\PHPMailer\PHPMailer') ? 'Loaded' : 'Not loaded', class_exists('\PHPMailer\PHPMailer\PHPMailer'));
$checks[] = diagLine('MAIL_HOST', $env['MAIL_HOST'] ?? '(missing)', !empty($env['MAIL_HOST']));
$checks[] = diagLine('MAIL_PORT', (string) ($env['MAIL_PORT'] ?? '(missing)'), !empty($env['MAIL_PORT']));
$checks[] = diagLine('MAIL_ENCRYPTION', $env['MAIL_ENCRYPTION'] ?? '(missing)', !empty($env['MAIL_ENCRYPTION']));
$checks[] = diagLine('MAIL_USERNAME', $env['MAIL_USERNAME'] ?? '(missing)', !empty($env['MAIL_USERNAME']));
$checks[] = diagLine('MAIL_PASSWORD', empty($env['MAIL_PASSWORD']) ? '(missing)' : str_repeat('*', min(12, strlen($env['MAIL_PASSWORD']))), !empty($env['MAIL_PASSWORD']));
$checks[] = diagLine('MAIL_ADMIN', getAdminEmail($env), filter_var(getAdminEmail($env), FILTER_VALIDATE_EMAIL) !== false);
$checks[] = diagLine('SITE_URL', getSiteUrl($env), filter_var(getSiteUrl($env), FILTER_VALIDATE_URL) !== false);

$testRecipient = trim($_GET['to'] ?? ($env['MAIL_USERNAME'] ?? ''));
if (!filter_var($testRecipient, FILTER_VALIDATE_EMAIL)) {
    $testRecipient = $env['MAIL_USERNAME'] ?? '';
}

$sendResult = null;
$sendError = '';
$attemptLog = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['send']) && $_GET['send'] === '1') {
    $profiles = [
            [
                    'label' => 'Primary (.env settings)',
                    'env' => $env,
            ],
            [
                    'label' => 'Fallback TLS :587',
                    'env' => array_merge($env, ['MAIL_PORT' => '587', 'MAIL_ENCRYPTION' => 'tls']),
            ],
            [
                    'label' => 'Fallback SSL :465',
                    'env' => array_merge($env, ['MAIL_PORT' => '465', 'MAIL_ENCRYPTION' => 'ssl']),
            ],
    ];

    $seen = [];
    foreach ($profiles as $profile) {
        $signature = ($profile['env']['MAIL_PORT'] ?? '') . '|' . ($profile['env']['MAIL_ENCRYPTION'] ?? '');
        if (isset($seen[$signature])) {
            continue;
        }
        $seen[$signature] = true;

        try {
            $mail = createConfiguredMailer($profile['env']);
            $mail->SMTPDebug = 2;
            $debugOutput = '';
            $mail->Debugoutput = static function ($str) use (&$debugOutput) {
                $debugOutput .= $str;
            };
            $mail->addAddress($testRecipient, 'Vangence Diagnostic');
            $mail->Subject = 'Vangence SMTP Test — ' . date('Y-m-d H:i:s');
            $mail->Body = '<p>If you received this, SMTP is working for Vangence order emails.</p>';
            $mail->AltBody = 'If you received this, SMTP is working for Vangence order emails.';
            $mail->send();

            $sendResult = true;
            $attemptLog[] = [
                    'label' => $profile['label'],
                    'ok' => true,
                    'detail' => trim($debugOutput),
            ];
            break;
        } catch (Throwable $e) {
            $detail = ($e instanceof \PHPMailer\PHPMailer\Exception && isset($mail))
                    ? $mail->ErrorInfo
                    : $e->getMessage();
            $attemptLog[] = [
                    'label' => $profile['label'],
                    'ok' => false,
                    'detail' => trim($debugOutput . "\n" . $detail),
            ];
            $sendError = $detail;
        }
    }

    if ($sendResult !== true) {
        $sendResult = false;
    }
}

// ===================== TEMPLATE A/B TEST =====================
// Sends the REAL order-confirmation HTML (header, footer, items table,
// tracking button) through the SAME sendEmailWithFallback() function
// production order/status emails use — with dummy data, no DB writes.
// This isolates whether the plain test message and the real template
// are treated differently by something downstream (spam filter, content
// scanner) even though both use the identical SMTP account and code path.
$templateSendResult = null;
$templateSendError = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['send']) && $_GET['send'] === 'template') {
    $dummyOrder = [
            'order_number'   => 'ORD_DIAGTEST' . time(),
            'created_at'     => date('Y-m-d H:i:s'),
            'payment_method' => 'cod',
            'order_status'   => 'pending',
            'subtotal'       => 3200.00,
            'shipping_cost'  => 250.00,
            'total_amount'   => 3450.00,
            'first_name'     => 'Diagnostic',
            'last_name'      => 'Test',
            'address'        => '123 Test Street',
            'city'           => 'Lahore',
            'state'          => 'Punjab',
            'phone'          => '0300-0000000',
            'email'          => $testRecipient,
    ];
    $dummyItems = [
            [
                    'product_name' => 'Sample Product',
                    'size' => 'M',
                    'color' => 'Black',
                    'quantity' => 1,
                    'line_total' => 3200.00,
            ],
    ];

    try {
        $summaryHtml = buildOrderEmailSummaryHtml($dummyOrder, $dummyItems, $env);
        $trackingUrl = getOrderConfirmationUrl($dummyOrder['order_number'], $env);
        $supportEmail = getSupportEmail($env);

        $inner = "
            <h2 style=\"color:#1a2b49; font-size:18px; margin-top:0;\">Thank you for your order, " . htmlspecialchars($dummyOrder['first_name']) . "!</h2>
            <p style=\"color:#555; font-size:14px; line-height:1.7;\">
                This is the diagnostic tool sending the REAL production order-confirmation template (dummy data, no database writes) to isolate template-vs-content delivery issues.
            </p>
            {$summaryHtml}
            " . getEmailButtonHtml($trackingUrl, 'View Your Order') . "
            <p style=\"font-size:13px; color:#777; line-height:1.6;\">
                Need assistance? Email us at <a href=\"mailto:{$supportEmail}\" style=\"color:#1a2b49;\">{$supportEmail}</a>.
            </p>";

        $templateSendResult = sendEmailWithFallback(
                $testRecipient,
                $dummyOrder['first_name'] . ' ' . $dummyOrder['last_name'],
                'Order Confirmed — ' . $dummyOrder['order_number'] . ' | Vangence',
                getEmailWrapper($inner, $env)
        );
    } catch (Throwable $e) {
        $templateSendResult = false;
        $templateSendError = $e->getMessage();
    }
}

// ===================== LITE TEMPLATE A/B TEST =====================
// A deliberately plain-looking version of the order confirmation: no
// logo banner, no colored button (a plain text link instead), minimal
// inline styling, no big monetary "PKR" total. Tests whether the
// *design pattern* (marketing-style banner + CTA button) is what's
// tripping the relay's content filter, independent of the SMTP path.
$liteSendResult = null;
$liteSendError = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['send']) && $_GET['send'] === 'lite') {
    $orderNumber = 'ORD_DIAGTEST' . time();
    $trackingUrl = getOrderConfirmationUrl($orderNumber, $env);
    $supportEmail = getSupportEmail($env);

    $liteBody = "
        <div style=\"font-family:Arial,sans-serif;font-size:14px;color:#222;max-width:600px;\">
            <p>Hi Diagnostic,</p>
            <p>Thank you for your order. Your order number is {$orderNumber}.</p>
            <p>Order status: pending. Total: PKR 3450.</p>
            <p>You can check your order status here: <a href=\"" . htmlspecialchars($trackingUrl) . "\">" . htmlspecialchars($trackingUrl) . "</a></p>
            <p>Questions? Contact {$supportEmail}.</p>
            <p>— Vangence</p>
        </div>";

    try {
        $liteSendResult = sendEmailWithFallback(
                $testRecipient,
                'Diagnostic Test',
                'Order Confirmed — ' . $orderNumber . ' | Vangence',
                $liteBody
        );
    } catch (Throwable $e) {
        $liteSendResult = false;
        $liteSendError = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vangence Email Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 24px; color: #222; }
        .card { max-width: 900px; margin: 0 auto 20px; background: #fff; border: 1px solid #e3e3e3; border-radius: 8px; padding: 24px; }
        h1 { margin-top: 0; color: #1a2b49; }
        table { width: 100%; border-collapse: collapse; }
        pre { background: #111; color: #d7ffd7; padding: 16px; border-radius: 6px; overflow: auto; font-size: 12px; white-space: pre-wrap; }
        .btn { display: inline-block; background: #1a2b49; color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 4px; font-weight: bold; }
        .warn { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 12px 14px; border-radius: 6px; margin-bottom: 16px; }
        .ok { color: #1e7e34; font-weight: bold; }
        .fail { color: #c0392b; font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <h1>Vangence Email Diagnostic</h1>
    <div class="warn">
        <strong>Important:</strong> If Roundcube/webmail login fails for <code>orders@vangence.com</code>, SMTP from PHP will also fail.
        Reset the mailbox password in cPanel first, update <code>admin/model/.env</code>, then run this test again.
        Delete this file after email is working.
    </div>

    <h2>Environment Checks</h2>
    <table>
        <tr>
            <th style="text-align:left;padding:8px 12px;border-bottom:2px solid #ddd;">Check</th>
            <th style="text-align:left;padding:8px 12px;border-bottom:2px solid #ddd;">Value</th>
            <th style="text-align:left;padding:8px 12px;border-bottom:2px solid #ddd;">Status</th>
        </tr>
        <?= implode('', $checks) ?>
    </table>
</div>

<div class="card">
    <h2>Send Test Email</h2>
    <p>Recipient: <strong><?= htmlspecialchars($testRecipient) ?></strong></p>
    <p>Optional override: add <code>&amp;to=your@gmail.com</code> to the URL.</p>
    <p>
        <a class="btn" href="?key=<?= urlencode($requiredKey) ?>&amp;send=1&amp;to=<?= urlencode($testRecipient) ?>">Run SMTP Send Test</a>
    </p>

    <?php if ($sendResult === true): ?>
        <p class="ok">Test email sent successfully. Check inbox and spam folder.</p>
    <?php elseif ($sendResult === false): ?>
        <p class="fail">Test email failed.</p>
        <?php if ($sendError !== ''): ?>
            <p><strong>Last error:</strong> <?= htmlspecialchars($sendError) ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php foreach ($attemptLog as $attempt): ?>
        <h3><?= htmlspecialchars($attempt['label']) ?> — <?= $attempt['ok'] ? 'SUCCESS' : 'FAILED' ?></h3>
        <?php if ($attempt['detail'] !== ''): ?>
            <pre><?= htmlspecialchars($attempt['detail']) ?></pre>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<div class="card">
    <h2>A/B Test: Real Order Template</h2>
    <p>
        This sends the exact same branded HTML used for real order confirmations (header, footer, items table,
        tracking button) through the exact same <code>sendEmailWithFallback()</code> function production orders use —
        with dummy data, no database writes. If the plain test above arrives but this one doesn't, the template's
        content (not your SMTP config) is what's getting filtered downstream.
    </p>
    <p>
        <a class="btn" href="?key=<?= urlencode($requiredKey) ?>&amp;send=template&amp;to=<?= urlencode($testRecipient) ?>">Send Real Template Test</a>
    </p>

    <?php if ($templateSendResult === true): ?>
        <p class="ok">Template test email sent successfully. Check inbox and spam folder — and compare against the plain test above.</p>
    <?php elseif ($templateSendResult === false): ?>
        <p class="fail">Template test email failed to send.</p>
        <?php if ($templateSendError !== ''): ?>
            <p><strong>Error:</strong> <?= htmlspecialchars($templateSendError) ?></p>
        <?php endif; ?>
        <p style="font-size:12px;color:#888;">Check your PHP error log for the "sendEmail ERROR" / SMTP trace line for full detail.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h2>A/B Test: Lite Template (no banner, no button)</h2>
    <p>
        Same code path again, but with a plain-text-style body: no logo banner, no colored button, minimal styling
        — just a link. If this arrives while the full template doesn't, the fix is redesigning the template to look
        less like a marketing email while keeping it recognizably Vangence.
    </p>
    <p>
        <a class="btn" href="?key=<?= urlencode($requiredKey) ?>&amp;send=lite&amp;to=<?= urlencode($testRecipient) ?>">Send Lite Template Test</a>
    </p>

    <?php if ($liteSendResult === true): ?>
        <p class="ok">Lite test email sent successfully. Check inbox and spam folder.</p>
    <?php elseif ($liteSendResult === false): ?>
        <p class="fail">Lite test email failed to send.</p>
        <?php if ($liteSendError !== ''): ?>
            <p><strong>Error:</strong> <?= htmlspecialchars($liteSendError) ?></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>