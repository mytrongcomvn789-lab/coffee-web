<?php
// ============================================================
//  tools/generate_hash.php
//  Dùng để tạo password hash khi cần đổi mật khẩu mặc định
//  Truy cập: http://localhost/coffee-web/tools/generate_hash.php
//  XÓA FILE NÀY SAU KHI DÙNG XONG
// ============================================================

$passwords = ['Admin@123', 'User@123'];

foreach ($passwords as $pass) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    echo "<p><b>$pass</b><br><code>$hash</code></p>";
}

// Verify thử
echo "<hr>";
$hash = '$2y$12$RIVGWBiR.vW7U5a7Vn6neeghWvfWyFmL9k5vK7W.b8e9sLn/x1TOi';
echo password_verify('Admin@123', $hash) ? '✅ Hash hợp lệ' : '❌ Hash không khớp';
