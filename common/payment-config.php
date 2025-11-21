<?php
// common/payment-config.php
define('PAYMENT_UPLOAD_DIR', realpath(__DIR__ . '/../uploads/payments') . '/');
define('PAYMENT_UPLOAD_WEB', '/uploads/payments/');
define('MAX_UPLOAD_BYTES', (int)($_ENV['UPLOAD_MAX_FILESIZE'] ?? ($_ENV['UPLOAD_MAX_FILESIZE'] ?? 5 * 1024 * 1024)));
if (!is_dir(PAYMENT_UPLOAD_DIR)) mkdir(PAYMENT_UPLOAD_DIR, 0755, true);
?>
