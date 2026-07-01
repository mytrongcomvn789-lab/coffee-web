<?php
// ============================================================
//  includes/auth.php — Kiểm tra phân quyền
// ============================================================

require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Yêu cầu đăng nhập — dùng cho: cart, checkout, lichsu
// Gọi requireLogin() ở đầu trang cần bảo vệ
function requireLogin(string $redirectTo = '../pages/login.php'): void {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect($redirectTo);
    }
}

// Yêu cầu quyền Admin — dùng cho: tất cả trang trong admin/
// Gọi requireAdmin() ở đầu trang admin
function requireAdmin(string $redirectTo = '../pages/login.php'): void {
    if (!isLoggedIn()) {
        redirect($redirectTo);
    }
    if (!isAdmin()) {
        redirect('../index.php');
    }
}
