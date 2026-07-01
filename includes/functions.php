<?php
// ============================================================
//  includes/functions.php — Hàm PHP tiện ích dùng chung
//  Cách dùng: require_once __DIR__ . '/../includes/functions.php';
// ============================================================

// ── Format tiền VNĐ ──────────────────────────────────────────
// formatPrice(52000) → "52.000đ"
function formatPrice(int $amount): string {
    return number_format($amount, 0, ',', '.') . 'đ';
}

// ── Làm sạch input từ người dùng ─────────────────────────────
function clean(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// ── Kiểm tra đăng nhập ───────────────────────────────────────
function isLoggedIn(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['user_id']);
}

// ── Kiểm tra quyền admin ─────────────────────────────────────
function isAdmin(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// ── Redirect ─────────────────────────────────────────────────
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// ── Thông tin user hiện tại ───────────────────────────────────
function currentUser(): array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return [
        'id'   => $_SESSION['user_id']   ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'role' => $_SESSION['user_role'] ?? null,
    ];
}

// ── Tạo mã đơn hàng ──────────────────────────────────────────
// generateOrderCode() → "YL20240628001"
function generateOrderCode(): string {
    return 'YL' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}

// ── Label trạng thái đơn hàng ────────────────────────────────
function orderStatusLabel(string $status): string {
    return match($status) {
        'pending'    => 'Chờ xác nhận',
        'confirmed'  => 'Đã xác nhận',
        'delivering' => 'Đang giao',
        'delivered'  => 'Đã giao',
        'cancelled'  => 'Đã huỷ',
        default      => $status
    };
}

// ── Màu badge trạng thái đơn hàng ────────────────────────────
function orderStatusColor(string $status): string {
    return match($status) {
        'pending'    => '#f59e0b',
        'confirmed'  => '#3b82f6',
        'delivering' => '#8b5cf6',
        'delivered'  => '#10b981',
        'cancelled'  => '#ef4444',
        default      => '#6b7280'
    };
}

// ── Tạo slug từ chuỗi tiếng Việt ─────────────────────────────
// toSlug("Cà Phê Sữa Đá") → "ca-phe-sua-da"
function toSlug(string $str): string {
    $str = mb_strtolower($str, 'UTF-8');
    $map = [
        'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a',
        'ă'=>'a','ắ'=>'a','ằ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a',
        'â'=>'a','ấ'=>'a','ầ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
        'è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e',
        'ê'=>'e','ế'=>'e','ề'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
        'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
        'ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o',
        'ô'=>'o','ố'=>'o','ồ'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o',
        'ơ'=>'o','ớ'=>'o','ờ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
        'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u',
        'ư'=>'u','ứ'=>'u','ừ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
        'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
        'đ'=>'d',
    ];
    $str = strtr($str, $map);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    return preg_replace('/[\s-]+/', '-', trim($str));
}
