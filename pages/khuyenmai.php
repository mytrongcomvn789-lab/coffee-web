<?php
require_once __DIR__ . '/../includes/db.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function promoImage(?string $path): string
{
    $path = trim((string) $path);
    if ($path === '') {
        return '../assets/images/khuyenmai/cafe_.avif';
    }
    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }
    return '../' . ltrim($path, '/');
}

function promoTagLabel(?string $tag): string
{
    return match ($tag) {
        'khuyen-mai'   => 'Khuyến mãi',
        'tin-tuc'      => 'Tin tức',
        'san-pham-moi' => 'Sản phẩm mới',
        'thanh-vien'   => 'Thành viên',
        default        => 'Tin tức',
    };
}

$subscribeStatus = null;
$subscribeMessage = '';
$pageError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_type'] ?? '') === 'subscribe') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $subscribeStatus = 'error';
        $subscribeMessage = 'Email không hợp lệ. Vui lòng nhập lại.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO subscribers (email) VALUES (:email)');
            $stmt->execute(['email' => $email]);
            $subscribeStatus = 'success';
            $subscribeMessage = 'Đăng ký thành công. Email của bạn đã được lưu.';
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $subscribeStatus = 'success';
                $subscribeMessage = 'Email này đã đăng ký nhận ưu đãi trước đó.';
            } else {
                $subscribeStatus = 'error';
                $subscribeMessage = 'Không thể lưu email lúc này. Vui lòng thử lại sau.';
            }
        }
    }
}

try {
    $featuredStmt = $pdo->query("
        SELECT id, title, excerpt, image, tag, createdat
        FROM promotions
        WHERE isactive = 1 AND isfeatured = 1
        ORDER BY createdat DESC, id DESC
        LIMIT 2
    ");
    $featuredPromotions = $featuredStmt->fetchAll();

    $promoStmt = $pdo->query("
        SELECT id, title, excerpt, image, tag, createdat
        FROM promotions
        WHERE isactive = 1
        ORDER BY createdat DESC, id DESC
    ");
    $promotions = $promoStmt->fetchAll();
} catch (PDOException $ex) {
    $featuredPromotions = [];
    $promotions = [];
    $pageError = 'Không tải được dữ liệu khuyến mãi từ database.';
}
?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/pages/khuyenmai.css" />
    <title>Khuyến mãi – YourLife Coffee</title>
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
            <a href="./khuyenmai.php" class="nav-link active">Khuyến mãi</a>
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
        <a href="./cart.html" class="cart"><i class="icon-cart"></i></a>
        <button class="account">
          <i class="icon-user"></i><span>Tài khoản</span
          ><i class="icon-char"></i>
        </button>
      </div>
    </header>
    <main>
      <!-- PAGE HERO -->
      <section
        class="page-hero"
        style="
          background-image: linear-gradient(
            135deg,
            #0b291d 0%,
            #1e4d3a 60%,
            #2a6349 100%
          );
        "
      >
        <div class="page-hero-content">
          <p class="page-hero-label">YourLife Coffee</p>
          <h1 class="page-hero-title">Tin tức &amp; Khuyến mãi</h1>
          <p class="page-hero-desc">
            Cập nhật những ưu đãi hấp dẫn và tin tức mới nhất từ YourLife
          </p>
        </div>
      </section>

      <!-- PROMO BANNER 2 CỘT -->
      <section class="promo-section">
        <div class="promo-container">
          <?php if (!empty($featuredPromotions)): ?>
            <?php foreach ($featuredPromotions as $index => $promo): ?>
              <div class="promo-card <?= $index % 2 === 0 ? 'green' : 'brown' ?>">
                <img
                  src="<?= e(promoImage($promo['image'])) ?>"
                  alt="<?= e($promo['title']) ?>"
                  class="promo-bg"
                />
                <div class="promo-overlay"></div>
                <div class="promo-content">
                  <p class="promo-tag"><?= e(promoTagLabel($promo['tag'])) ?></p>
                  <h3 class="promo-title"><?= e($promo['title']) ?></h3>
                  <a href="./menu.html" class="<?= $index % 2 === 0 ? 'btn-promo-primary' : 'btn-promo-outline' ?>">Xem ưu đãi</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="promo-card green">
              <div class="promo-overlay"></div>
              <div class="promo-content">
                <p class="promo-tag">Thông báo</p>
                <h3 class="promo-title">Chưa có khuyến mãi nổi bật</h3>
                <a href="./menu.html" class="btn-promo-primary">Xem menu</a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- DANH SÁCH TIN TỨC / KHUYẾN MÃI -->
      <section class="news-section">
        <div class="news-container">
          <div class="news-header">
            <h2 class="news-title">Tất cả tin tức</h2>
            <div class="news-filter">
              <button class="filter-btn active" data-filter="all">Tất cả</button>
              <button class="filter-btn" data-filter="khuyen-mai">Khuyến mãi</button>
              <button class="filter-btn" data-filter="tin-tuc">Tin tức</button>
              <button class="filter-btn" data-filter="san-pham-moi">Sản phẩm mới</button>
              <button class="filter-btn" data-filter="thanh-vien">Thành viên</button>
            </div>
          </div>

          <?php if ($pageError): ?>
            <p style="padding: 16px 20px; border-radius: 12px; background: #fef2f2; color: #991b1b; margin-bottom: 24px;">
              <?= e($pageError) ?>
            </p>
          <?php endif; ?>

          <?php if (!empty($promotions)): ?>
            <div class="news-featured">
              <?php foreach (array_slice($promotions, 0, 2) as $promo): ?>
                <article class="news-card featured" data-tag="<?= e($promo['tag'] ?? 'tin-tuc') ?>">
                  <div class="news-card-img">
                    <img src="<?= e(promoImage($promo['image'])) ?>" alt="<?= e($promo['title']) ?>" />
                  </div>
                  <div class="news-card-body">
                    <div class="news-meta">
                      <span class="news-tag tag-<?= e($promo['tag'] ?? 'tin-tuc') ?>"><?= e(promoTagLabel($promo['tag'])) ?></span>
                      <span class="news-date"><?= e(date('d/m/Y', strtotime($promo['createdat']))) ?></span>
                    </div>
                    <h3 class="news-card-title"><?= e($promo['title']) ?></h3>
                    <p class="news-card-excerpt"><?= e($promo['excerpt'] ?? '') ?></p>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>

            <div class="news-grid">
              <?php foreach (array_slice($promotions, 2) as $promo): ?>
                <article class="news-card" data-tag="<?= e($promo['tag'] ?? 'tin-tuc') ?>">
                  <div class="news-card-img">
                    <img src="<?= e(promoImage($promo['image'])) ?>" alt="<?= e($promo['title']) ?>" />
                  </div>
                  <div class="news-card-body">
                    <div class="news-meta">
                      <span class="news-tag tag-<?= e($promo['tag'] ?? 'tin-tuc') ?>"><?= e(promoTagLabel($promo['tag'])) ?></span>
                      <span class="news-date"><?= e(date('d/m/Y', strtotime($promo['createdat']))) ?></span>
                    </div>
                    <h3 class="news-card-title"><?= e($promo['title']) ?></h3>
                    <p class="news-card-excerpt"><?= e($promo['excerpt'] ?? '') ?></p>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p style="padding: 24px; border-radius: 16px; background: #ffffff; color: #6b5c4e;">
              Chưa có dữ liệu khuyến mãi.
            </p>
          <?php endif; ?>
        </div>
      </section>

      <!-- CTA -->
      <section class="cta-section">
        <div class="cta-container">
          <h2 class="cta-title">Nhận ưu đãi mới nhất qua email</h2>
          <p class="cta-desc">
            Đăng ký ngay để không bỏ lỡ bất kỳ chương trình khuyến mãi nào từ
            YourLife Coffee.
          </p>
          <form class="cta-form" action="./khuyenmai.php" method="post">
            <input type="hidden" name="form_type" value="subscribe" />
            <input
              type="email"
              name="email"
              class="cta-input"
              placeholder="Nhập địa chỉ email của bạn..."
              required
            />
            <button type="submit" class="cta-btn">Đăng ký</button>
          </form>
          <?php if ($subscribeMessage): ?>
            <p class="cta-note" style="display: block; color: <?= $subscribeStatus === 'success' ? '#a8d5b8' : '#fecaca' ?>;">
              <?= e($subscribeMessage) ?>
            </p>
          <?php endif; ?>
        </div>
      </section>
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
          Mang đến hương vị cà phê &amp; trà chất lượng cao nhất Việt Nam.
        </p>
      </div>
      <div class="footer-link">
        <h3 class="footer-heading">Liên kết nhanh</h3>
        <nav>
          <ul>
            <li><a href="../index.php">Trang chủ</a></li>
            <li><a href="./menu.html">Menu</a></li>
            <li><a href="./khuyenmai.php">Khuyến mãi</a></li>
            <li><a href="./lichsu.html">Lịch sử</a></li>
            <li><a href="./lienhe.php">Liên hệ</a></li>
          </ul>
        </nav>
      </div>
      <div class="footer-info">
        <h3 class="footer-heading">Thông tin cửa hàng</h3>
        <div class="footer-info-list">
          <div class="footer-info-item">
            <span class="fi-icon">📍</span
            ><span>123 Nguyễn Huệ, Q.1, TP.HCM</span>
          </div>
          <div class="footer-info-item">
            <span class="fi-icon">📞</span><span>1800 6996</span>
          </div>
          <div class="footer-info-item">
            <span class="fi-icon">✉️</span><span>info@yourlife.com.vn</span>
          </div>
          <div class="footer-info-item">
            <span class="fi-icon">🕐</span><span>07:00 – 22:00 hàng ngày</span>
          </div>
        </div>
      </div>
      <div class="footer-contact">
        <h3 class="footer-heading">Kết nối với chúng tôi</h3>
        <p class="footer-contact-desc">
          Theo dõi YourLife trên mạng xã hội để nhận thông tin khuyến mãi mới
          nhất.
        </p>
        <div class="footer-social">
          <a
            href="https://www.facebook.com/?locale=vi_VN"
            target="_blank"
            class="social-btn"
            >Facebook</a
          >
          <a
            href="https://id.zalo.me/account?continue=https%3A%2F%2Fchat.zalo.me%2F"
            target="_blank"
            class="social-btn"
            >Zalo</a
          >
          <a
            href="https://www.instagram.com/"
            target="_blank"
            class="social-btn"
            >Instagram</a
          >
        </div>
        <a href="" class="footer-admin">Trang quản trị</a>
      </div>
      <div class="footer-bottom">
        <p class="footer-copyright">
          © 2026 YourLife. Tất cả quyền được bảo lưu.
        </p>
      </div>
    </footer>

    <script src="../assets/js/utils/validate.js"></script>
    <script src="../assets/js/account-dropdown.js"></script>
  </body>
</html>
