<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// ─────────────────────────────────────────────────────────────
// Hàm tiện ích
// ─────────────────────────────────────────────────────────────

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function productImage(?string $path): string
{
    $path = trim((string) $path);
    if ($path === '') {
        return '../assets/images/menu/cafesuada.avif';
    }
    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }
    return '../' . ltrim($path, '/');
}

// ─────────────────────────────────────────────────────────────
// Quản lý giỏ hàng từ SESSION
// ─────────────────────────────────────────────────────────────

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartStatus = null;
$cartMessage = '';
$pageError = null;

// ─────────────────────────────────────────────────────────────
// Xử lý POST: Thêm, xóa, cập nhật sản phẩm
// ─────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['cart_action'] ?? '';
    
    if ($action === 'add') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if ($productId > 0 && $quantity > 0) {
            // Kiểm tra sản phẩm có tồn tại không
            try {
                $stmt = $pdo->prepare('SELECT id, name, price, image FROM products WHERE id = :id LIMIT 1');
                $stmt->execute(['id' => $productId]);
                $product = $stmt->fetch();
                
                if ($product) {
                    $found = false;
                    // Cập nhật số lượng nếu sản phẩm đã có trong giỏ
                    foreach ($_SESSION['cart'] as &$item) {
                        if ($item['id'] == $productId) {
                            $item['quantity'] += $quantity;
                            $found = true;
                            break;
                        }
                    }
                    
                    // Thêm sản phẩm mới
                    if (!$found) {
                        $_SESSION['cart'][] = [
                            'id' => $product['id'],
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'image' => $product['image'],
                            'quantity' => $quantity,
                        ];
                    }
                    
                    $cartStatus = 'success';
                    $cartMessage = 'Sản phẩm đã được thêm vào giỏ hàng.';
                } else {
                    $cartStatus = 'error';
                    $cartMessage = 'Sản phẩm không tồn tại.';
                }
            } catch (PDOException $ex) {
                $cartStatus = 'error';
                $cartMessage = 'Lỗi khi thêm sản phẩm.';
            }
        }
    } 
    elseif ($action === 'remove') {
        $productId = (int)($_POST['product_id'] ?? 0);
        if ($productId > 0) {
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($productId) {
                return $item['id'] != $productId;
            });
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            $cartStatus = 'success';
            $cartMessage = 'Sản phẩm đã được xóa khỏi giỏ hàng.';
        }
    }
    elseif ($action === 'update') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if ($productId > 0) {
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $productId) {
                    if ($quantity > 0) {
                        $item['quantity'] = $quantity;
                        $cartStatus = 'success';
                        $cartMessage = 'Số lượng sản phẩm đã được cập nhật.';
                    } else {
                        $cartStatus = 'error';
                        $cartMessage = 'Số lượng không hợp lệ.';
                    }
                    break;
                }
            }
        }
    }
    elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        $cartStatus = 'success';
        $cartMessage = 'Giỏ hàng đã được xóa tất cả.';
    }
}

// ─────────────────────────────────────────────────────────────
// Tính tổng tiền
// ─────────────────────────────────────────────────────────────

$totalPrice = 0;
$totalQuantity = 0;

foreach ($_SESSION['cart'] as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
    $totalQuantity += $item['quantity'];
}
?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>YourLife Coffee - Giỏ hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/pages/cart.css" />
  </head>
  <body>
    <!-- HEADER -->
    <header class="header">
      <a href="../index.php" class="header-brand">
        <div class="brand-icon">
          <img src="../assets/images/trangchu/logo.jpg" alt="YourLife Coffee" />
        </div>
        <span class="brand-name">YourLife</span>
      </a>
      <nav class="navbar">
        <ul class="nav">
          <li class="nav-item">
            <a href="../index.php" class="nav-link">Trang chủ</a>
          </li>
          <li class="nav-item">
            <a href="./menu.html" class="nav-link">Menu</a>
          </li>
          <li class="nav-item">
            <a href="./khuyenmai.php" class="nav-link">Khuyến mãi</a>
          </li>
          <li class="nav-item">
            <a href="./lichsu.html" class="nav-link">Lịch sử</a>
          </li>
          <li class="nav-item">
            <a href="./lienhe.php" class="nav-link">Liên hệ</a>
          </li>
        </ul>
      </nav>
      <div class="header-action">
        <a href="./cart.php" class="cart"><i class="icon-cart"></i></a>
        <button class="account">
          <i class="icon-user"></i>
          <span>Tài khoản</span>
          <i class="icon-char"></i>
        </button>
      </div>
    </header>

    <main class="cart-page">
      <div class="cart-shell">
        <div class="cart-shell-header"></div>
        
        <!-- Thông báo -->
        <?php if ($cartStatus === 'success'): ?>
          <div class="alert alert-success" style="margin: 20px 20px 0;">
            <strong>Thành công!</strong> <?php echo e($cartMessage); ?>
          </div>
        <?php elseif ($cartStatus === 'error'): ?>
          <div class="alert alert-error" style="margin: 20px 20px 0;">
            <strong>Lỗi!</strong> <?php echo e($cartMessage); ?>
          </div>
        <?php endif; ?>

        <div class="cart-container" id="cartContainer">
          <section class="cart-main" id="cartMain">
            <?php if (empty($_SESSION['cart'])): ?>
              <div class="cart-empty" id="cartEmpty">
                <h1>Giỏ hàng</h1>
                <span class="cart-empty-icon">🛒</span>
                <h2>Giỏ hàng trống</h2>
                <p>Chưa có sản phẩm nào trong giỏ của bạn.</p>
                <a href="./menu.html" class="btn-primary cart-empty-btn">Xem Menu</a>
              </div>
            <?php else: ?>
              <div class="cart-items" id="cartItems">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                  <div class="cart-item">
                    <img src="<?php echo e(productImage($item['image'])); ?>" alt="<?php echo e($item['name']); ?>" />
                    <div class="cart-item-meta">
                      <h4><?php echo e($item['name']); ?></h4>
                      <span class="price"><?php echo formatPrice($item['price']); ?></span>
                    </div>
                    <div class="cart-item-actions">
                      <div class="cart-qty-controls">
                        <form method="POST" style="display: contents;">
                          <input type="hidden" name="cart_action" value="update">
                          <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                          <input type="hidden" name="quantity" value="<?php echo max(1, $item['quantity'] - 1); ?>">
                          <button type="submit" class="cart-qty-btn">−</button>
                        </form>
                        
                        <span class="cart-qty-value"><?php echo $item['quantity']; ?></span>
                        
                        <form method="POST" style="display: contents;">
                          <input type="hidden" name="cart_action" value="update">
                          <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                          <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                          <button type="submit" class="cart-qty-btn">+</button>
                        </form>
                      </div>

                      <form method="POST" style="display: flex; align-items: center;">
                        <input type="hidden" name="cart_action" value="remove">
                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" class="cart-remove">Xóa</button>
                      </form>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </section>

          <?php if (!empty($_SESSION['cart'])): ?>
            <aside class="cart-summary-box" id="cartSummaryBox">
              <h3>Tóm tắt đơn hàng</h3>
              <div class="summary-list">
                <div id="summaryItems" class="summary-items">
                  <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="summary-item">
                      <span><?php echo e($item['name']); ?> • <?php echo $item['quantity']; ?></span>
                      <strong><?php echo formatPrice($item['price'] * $item['quantity']); ?></strong>
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="cart-summary-line">
                  <span>Thành tiền</span>
                  <strong id="summaryPrice"><?php echo formatPrice($totalPrice); ?></strong>
                </div>
              </div>
              <div class="cart-summary-total">
                <span>Tổng Tiền</span>
                <strong id="summaryTotal"><?php echo formatPrice($totalPrice); ?></strong>
              </div>
              <a href="./checkout.html" class="btn-checkout" id="checkoutBtn">
                Tiến hành đặt hàng →
              </a>
              <a href="./menu.html" class="cart-continue-link">
                ← Tiếp tục mua sắm
              </a>
              <form method="POST" style="width: 100%;">
                <input type="hidden" name="cart_action" value="clear">
                <button type="submit" class="btn-outline btn-full" id="clearCartBtn">
                  Xóa tất cả
                </button>
              </form>
            </aside>
          <?php endif; ?>
        </div>
      </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
      <div class="footer-main">
        <div class="footer-brand">
          <div class="footer-brand-icon">
            <img src="../assets/images/trangchu/logo.jpg" alt="YourLife" />
          </div>
          <span class="footer-brand-name">YourLife</span>
        </div>
        <p class="footer-tagline">
          Mang đến hương vị cà phê & trà chất lượng cao nhất Việt Nam.
        </p>
      </div>
      <div class="footer-link">
        <h3 class="footer-heading">Liên kết nhanh</h3>
        <ul>
          <li><a href="../index.php">Trang chủ</a></li>
          <li><a href="./menu.html">Menu</a></li>
          <li><a href="./khuyenmai.php">Khuyến mãi</a></li>
          <li><a href="./lichsu.html">Lịch sử</a></li>
          <li><a href="./lienhe.php">Liên hệ</a></li>
        </ul>
      </div>
      <div class="footer-link">
        <h3 class="footer-heading">Thông tin cửa hàng</h3>
        <div class="footer-info-list">
          <div class="footer-info-item">
            <span class="fi-icon">📍</span>
            <span>123 Nguyễn Huệ, Q.1, TP.HCM</span>
          </div>
          <div class="footer-info-item">
            <span class="fi-icon">📞</span>
            <span>1800 6996</span>
          </div>
          <div class="footer-info-item">
            <span class="fi-icon">✉️</span>
            <span>info@yourlife.com.vn</span>
          </div>
          <div class="footer-info-item">
            <span class="fi-icon">⏰</span>
            <span>07:00 – 22:00 hằng ngày</span>
          </div>
        </div>
      </div>
      <div class="footer-contact">
        <h3 class="footer-heading">Kết nối với chúng tôi</h3>
        <p class="footer-contact-desc">
          Theo dõi YourLife trên mạng xã hội để nhận thông tin khuyến mãi mới nhất.
        </p>
        <div class="footer-social">
          <a href="https://www.facebook.com/?locale=vi_VN" target="_blank" class="social-btn">Facebook</a>
          <a href="https://id.zalo.me/account?continue=https%3A%2F%2Fchat.zalo.me%2F" target="_blank" class="social-btn">Zalo</a>
          <a href="https://www.instagram.com/" target="_blank" class="social-btn">Instagram</a>
        </div>
        <a href="" class="footer-admin">Trang quản trị</a>
      </div>
      <div class="footer-bottom">
        <p class="footer-copyright">
          © 2026 YourLife. Tất cả quyền được bảo lưu.
        </p>
      </div>
    </footer>

    <script src="../assets/js/account-dropdown.js"></script>
  </body>
</html>
