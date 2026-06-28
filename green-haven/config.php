<?php
// ============================================================
// GREEN HAVEN - Application Configuration
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'green_haven');

define('SITE_NAME', 'Green Haven');
define('SITE_URL', 'http://localhost/green-haven');
define('SITE_TAGLINE', 'Bring Nature Home');
define('CURRENCY', '₹');
define('CURRENCY_CODE', 'INR');

define('LOW_STOCK_THRESHOLD', 10);
define('FREE_SHIPPING_ABOVE', 999);
define('DELIVERY_FEE', 50);

define('UPLOAD_PATH', __DIR__ . '/assets/images/');
define('UPLOAD_URL', SITE_URL . '/assets/images/');

// Session config
define('SESSION_LIFETIME', 86400); // 24 hours

// Timezone
date_default_timezone_set('Asia/Kolkata');
