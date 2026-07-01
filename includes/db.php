<?php
// ============================================================
//  includes/db.php — Kết nối MySQL dùng chung
//  Cách dùng ở đầu mỗi file PHP:
//    require_once __DIR__ . '/../includes/db.php';
//  Sau đó dùng $pdo để query
// ============================================================

require_once __DIR__ . '/../config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST
            . ';dbname=' . DB_NAME
            . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        die('❌ Kết nối database thất bại: ' . $e->getMessage());
    }
    die('Có lỗi xảy ra, vui lòng thử lại sau.');
}
