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
</body>
</html>
