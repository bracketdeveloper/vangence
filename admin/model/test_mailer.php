<?php

require_once __DIR__ . '/functions.php';

if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "PHPMailer is loaded correctly. Emails will work.";
} else {
    echo "PHPMailer is NOT available. Check that vendor/phpmailer/phpmailer/src/PHPMailer.php exists at your project root.";
}
