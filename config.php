<?php
// ============================================================
//  config.php — Cấu hình toàn dự án YourLife Coffee
//  Đặt ở thư mục gốc (cùng cấp với index.php)
//  LƯU Ý: KHÔNG commit file này lên Git nếu deploy thật
// ============================================================

// ── Thông tin kết nối Database ───────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'yourlife_coffee');  // Đúng với schema.sql
define('DB_USER',    'root');             // Mặc định XAMPP
define('DB_PASS',    '');                 // Mặc định XAMPP để trống
define('DB_CHARSET', 'utf8mb4');

// ── Cấu hình website ─────────────────────────────────────────
define('SITE_NAME', 'YourLife Coffee');
define('SITE_URL',  'http://localhost/coffee-web');

// ── Phí giao hàng mặc định ───────────────────────────────────
define('SHIPPING_FEE', 20000);

// ── Bật/tắt debug (đổi false khi nộp bài) ───────────────────
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}
