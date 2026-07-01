-- ============================================================
--  YourLife Coffee — Schema (Bản chính thức)
--  Kết hợp tốt nhất từ 2 bản trước
--  Engine : MySQL 8.0+  |  Charset: utf8mb4
--  Cách dùng:
--    mysql -u root -p < schema.sql
--    hoặc import qua phpMyAdmin
-- ============================================================

CREATE DATABASE IF NOT EXISTS yourlife_coffee
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE yourlife_coffee;

-- ============================================================
-- 1. CATEGORIES — Danh mục sản phẩm
-- Lấy từ: bản bạn (slug + INDEX) + bản tôi (icon + sortorder)
-- ============================================================
CREATE TABLE categories (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100) NOT NULL,
  slug       VARCHAR(100) NOT NULL UNIQUE,  -- "ca-phe", "tra-sua" dùng filter menu.php
  icon       VARCHAR(10)  NOT NULL DEFAULT '☕',
  sortorder  TINYINT      NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  INDEX idx_slug (slug)
) ENGINE=InnoDB;

INSERT INTO categories (name, slug, icon, sortorder) VALUES
  ('Cà Phê',       'ca-phe',       '☕', 1),
  ('Trà',          'tra',          '🍵', 2),
  ('Trà Sữa',      'tra-sua',      '🧋', 3),
  ('Đồ Ăn',        'do-an',        '🥐', 4);

-- ============================================================
-- 2. PRODUCTS — Sản phẩm
-- Lấy từ: bản bạn (slug, INDEX đầy đủ) + bản tôi (is_active rõ ràng hơn)
-- ============================================================
CREATE TABLE products (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  categoryid  INT UNSIGNED  NOT NULL,
  name        VARCHAR(150)  NOT NULL,
  slug        VARCHAR(150)  NOT NULL UNIQUE,   -- dùng cho URL đẹp sau này
  description TEXT          DEFAULT NULL,
  price       INT UNSIGNED  NOT NULL,           -- VNĐ, ví dụ: 52000
  image       VARCHAR(255)  DEFAULT NULL,       -- "assets/images/menu/trasua.avif"
  badge       VARCHAR(50)   DEFAULT NULL,       -- 'Bán chạy' | 'Mới' | 'Đặc biệt' | NULL
  soldcount   INT UNSIGNED  NOT NULL DEFAULT 0, -- ORDER BY soldcount DESC ở trang chủ
  isactive    TINYINT(1)    NOT NULL DEFAULT 1, -- 1: hiện | 0: ẩn (admin toggle)
  createdat   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_category (categoryid),
  INDEX idx_sold     (soldcount DESC),          -- tối ưu query sản phẩm bán chạy
  INDEX idx_active   (isactive),
  CONSTRAINT fk_product_category
    FOREIGN KEY (categoryid) REFERENCES categories(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO products (categoryid, name, slug, description, price, image, badge, soldcount) VALUES
  -- Trà Sữa (categoryid = 3)
  (3, 'Trà Sữa Trân Châu Đen',
      'tra-sua-tran-chau-den',
      'Trà sữa đậm đà với trân châu đen dẻo ngon, hương vị ngọt ngào khó quên.',
      52000, 'assets/images/menu/trasuatranchauden.avif', 'Bán chạy', 2100),

  (3, 'Trà Sữa Matcha Trân Châu',
      'tra-sua-matcha-tran-chau',
      'Matcha cao cấp pha sữa tươi cùng trân châu đen thơm dẻo, béo ngậy.',
      59000, 'assets/images/menu/trasua.avif', 'Mới', 1800),

  (3, 'Trà Sữa Khoai Môn',
      'tra-sua-khoai-mon',
      'Vị khoai môn béo thơm hoà quyện trong trà sữa mịn mượt, tuyệt vời.',
      55000, 'assets/images/menu/trasuakhoaimon.avif', 'Bán chạy', 1500),

  (3, 'Trà Sữa Oolong Sữa Tươi',
      'tra-sua-oolong-sua-tuoi',
      'Trà ô long thượng hạng kết hợp sữa tươi nguyên chất, hương thơm dịu nhẹ.',
      55000, 'assets/images/menu/trasua.avif', NULL, 890),

  (3, 'Trà Sữa Dứa Thơm',
      'tra-sua-dua-thom',
      'Trà sữa thanh mát vị dứa tươi, thơm ngọt tự nhiên.',
      49000, 'assets/images/menu/trasua.avif', 'Mới', 320),

  -- Cà Phê (categoryid = 1)
  (1, 'Cà Phê Sữa Đá',
      'ca-phe-sua-da',
      'Cà phê phin truyền thống kết hợp sữa đặc thơm ngon, đậm đà hương vị Việt.',
      35000, 'assets/images/menu/cafesuada.avif', 'Bán chạy', 1250),

  (1, 'Bạc Xỉu',
      'bac-xiu',
      'Cà phê sữa nhẹ nhàng, lượng sữa nhiều hơn cà phê, thích hợp cho người mới.',
      35000, 'assets/images/menu/bacxiu.avif', NULL, 980),

  (1, 'Cà Phê Đen Đá',
      'ca-phe-den-da',
      'Cà phê phin nguyên chất không pha sữa, đậm đà và thơm nồng.',
      29000, 'assets/images/menu/cafesuada.avif', NULL, 760),

  (1, 'Cà Phê Trứng',
      'ca-phe-trung',
      'Cà phê trứng đặc trưng Hà Nội, béo ngậy, thơm lừng, hương vị độc đáo.',
      45000, 'assets/images/menu/cafesuada.avif', 'Đặc biệt', 150),

  -- Trà (categoryid = 2)
  (2, 'Trà Đào Cam Sả',
      'tra-dao-cam-sa',
      'Trà đào thanh mát kết hợp cam tươi và sả thơm, giải nhiệt cực kỳ.',
      49000, 'assets/images/menu/tradaocamsa.avif', 'Bán chạy', 1100),

  (2, 'Trà Vải Lychee',
      'tra-vai-lychee',
      'Trà vải ngọt thanh với hương lychee tự nhiên, thức uống mùa hè lý tưởng.',
      45000, 'assets/images/menu/tradaocamsa.avif', NULL, 170),

  (2, 'Trà Chanh Leo',
      'tra-chanh-leo',
      'Trà chanh leo chua ngọt thanh mát, vị nhiệt đới đặc trưng.',
      42000, 'assets/images/menu/tradaocamsa.avif', NULL, 220),

  -- Đồ Ăn (categoryid = 4)
  (4, 'Bánh Croissant Bơ',
      'banh-croissant-bo',
      'Bánh croissant bơ Pháp giòn xốp, thơm béo, ăn kèm cà phê cực hợp.',
      35000, 'assets/images/menu/banh.avif', NULL, 90),

  (4, 'Bánh Tiramisu',
      'banh-tiramisu',
      'Bánh tiramisu Ý với lớp mascarpone mịn màng, hương cà phê đặc trưng.',
      55000, 'assets/images/menu/banh.avif', 'Mới', 60);

-- ============================================================
-- 3. USERS — Người dùng
-- Lấy từ: bản bạn (resettoken/resetexpires cho forgot-password)
--        + bản tôi (gender cho trang đăng ký)
-- ============================================================
CREATE TABLE users (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name         VARCHAR(100) NOT NULL,
  email        VARCHAR(150) NOT NULL UNIQUE,
  phone        VARCHAR(20)  DEFAULT NULL,
  gender       VARCHAR(10)  DEFAULT NULL,       -- 'male' | 'female' | 'other'
  passwordhash VARCHAR(255) NOT NULL,            -- password_hash() — KHÔNG lưu plain text
  role         ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  resettoken   VARCHAR(64)  DEFAULT NULL,        -- dùng cho forgot-password.php
  resetexpires DATETIME     DEFAULT NULL,        -- token hết hạn sau bao lâu
  createdat    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_email (email),
  INDEX idx_role  (role)
) ENGINE=InnoDB;

-- ============================================================
-- 4. PROMOTIONS — Khuyến mãi / Tin tức
-- Lấy từ: bản bạn + bản tôi (thêm isfeatured để phân biệt
--         promo nổi bật trang chủ vs danh sách trang khuyenmai)
-- ============================================================
CREATE TABLE promotions (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title       VARCHAR(200) NOT NULL,
  excerpt     TEXT         DEFAULT NULL,         -- mô tả ngắn hiển thị trên card
  image       VARCHAR(255) DEFAULT NULL,
  tag         VARCHAR(50)  DEFAULT NULL,         -- 'khuyen-mai' | 'tin-tuc' | 'san-pham-moi' | 'thanh-vien'
  isfeatured  TINYINT(1)   NOT NULL DEFAULT 0,   -- 1: hiển thị promo-card lớn ở trang chủ
  isactive    TINYINT(1)   NOT NULL DEFAULT 1,
  createdat   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_featured (isfeatured),
  INDEX idx_active   (isactive, createdat DESC)
) ENGINE=InnoDB;

INSERT INTO promotions (title, excerpt, image, tag, isfeatured) VALUES
  ('Mua 2 Cà Phê Tặng 1 Miễn Phí',
   'Chỉ hôm nay — Mua 2 ly cà phê bất kỳ, tặng ngay 1 ly miễn phí. Áp dụng tại quầy.',
   'assets/images/khuyenmai/cafe1.avif', 'khuyen-mai', 1),

  ('Giảm 30% Trà Sữa Khi Đặt Online',
   'Ưu đãi mùa hè — Giảm ngay 30% toàn bộ dòng trà sữa khi đặt qua website.',
   'assets/images/khuyenmai/trada.avif', 'khuyen-mai', 1),

  ('Ra Mắt Trà Vải Lychee',
   'Thức uống mới nhất của YourLife — Trà vải ngọt thanh với hương lychee tự nhiên.',
   'assets/images/khuyenmai/trada1.avif', 'san-pham-moi', 0),

  ('Ưu Đãi Thành Viên Thân Thiết',
   'Tích điểm mỗi đơn hàng, đổi quà hấp dẫn. Đăng ký thành viên ngay hôm nay.',
   'assets/images/khuyenmai/phacaphe.avif', 'thanh-vien', 0),

  ('Khai Trương Chi Nhánh Quận 7',
   'YourLife Coffee chính thức mở thêm chi nhánh tại Quận 7. Ghé thăm để nhận ưu đãi khai trương.',
   'assets/images/khuyenmai/cafe_.avif', 'tin-tuc', 0);

-- ============================================================
-- 5. ORDERS — Đơn hàng
-- Lấy từ: bản bạn (ordercode, status tiếng Anh chuẩn hơn)
--        + bản tôi (shippingfee mặc định 20000)
-- ============================================================
CREATE TABLE orders (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  userid         INT UNSIGNED DEFAULT NULL,      -- NULL nếu đặt không cần đăng nhập
  ordercode      VARCHAR(20)  NOT NULL UNIQUE,   -- VD: "YL20240628001" — hiển thị cho khách
  fullname       VARCHAR(100) NOT NULL,
  phone          VARCHAR(20)  NOT NULL,
  address        TEXT         DEFAULT NULL,       -- NULL nếu pickup
  note           TEXT         DEFAULT NULL,
  deliverymethod ENUM('delivery','pickup') NOT NULL DEFAULT 'delivery',
  subtotal       INT UNSIGNED NOT NULL,
  shippingfee    INT UNSIGNED NOT NULL DEFAULT 20000,
  total          INT UNSIGNED NOT NULL,
  status         ENUM('pending','confirmed','delivering','delivered','cancelled')
                 NOT NULL DEFAULT 'pending',
  createdat      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_user    (userid),
  INDEX idx_status  (status),
  INDEX idx_created (createdat DESC),
  CONSTRAINT fk_order_user
    FOREIGN KEY (userid) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 6. ORDER ITEMS — Chi tiết đơn hàng
-- Lấy từ: bản bạn (cấu trúc tốt)
-- Lưu lại name + price + image để lịch sử không bị ảnh hưởng
-- khi admin sửa/xóa sản phẩm về sau
-- ============================================================
CREATE TABLE orderitems (
  id        INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  orderid   INT UNSIGNED     NOT NULL,
  productid INT UNSIGNED     DEFAULT NULL,       -- NULL nếu sản phẩm bị xóa sau này
  name      VARCHAR(150)     NOT NULL,           -- lưu lại tên lúc đặt
  price     INT UNSIGNED     NOT NULL,           -- lưu lại giá lúc đặt
  quantity  SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  image     VARCHAR(255)     DEFAULT NULL,       -- lưu lại ảnh lúc đặt
  PRIMARY KEY (id),
  INDEX idx_order (orderid),
  CONSTRAINT fk_item_order
    FOREIGN KEY (orderid)   REFERENCES orders(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_item_product
    FOREIGN KEY (productid) REFERENCES products(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 7. CONTACTS — Liên hệ
-- ============================================================
CREATE TABLE contacts (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name      VARCHAR(100) NOT NULL,
  email     VARCHAR(150) NOT NULL,
  subject   VARCHAR(200) DEFAULT NULL,
  message   TEXT         NOT NULL,
  isread    TINYINT(1)   NOT NULL DEFAULT 0,     -- admin đánh dấu đã đọc
  createdat DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_read (isread)
) ENGINE=InnoDB;

-- ============================================================
-- 8. SUBSCRIBERS — Đăng ký nhận tin
-- ============================================================
CREATE TABLE subscribers (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email     VARCHAR(150) NOT NULL UNIQUE,
  createdat DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ============================================================
-- 9. TÀI KHOẢN MẶC ĐỊNH
--    Admin : admin@yourlife.vn  / Admin@123
--    User  : user@yourlife.vn   / User@123
--
--    Hash tạo bằng: password_hash('Admin@123', PASSWORD_DEFAULT)
--    Để tạo hash mới chạy file tools/generate_hash.php
-- ============================================================
INSERT INTO users (name, email, phone, gender, passwordhash, role) VALUES
  ('Admin YourLife', 'admin@yourlife.vn', '0900000000', NULL,
   '$2y$12$RIVGWBiR.vW7U5a7Vn6neeghWvfWyFmL9k5vK7W.b8e9sLn/x1TOi', 'admin'),

  ('Nguyễn Văn A',  'user@yourlife.vn',  '0987654321', 'male',
   '$2y$12$RIVGWBiR.vW7U5a7Vn6neeghWvfWyFmL9k5vK7W.b8e9sLn/x1TOi', 'customer');

-- ============================================================
-- 10. DỮ LIỆU MẪU — Đơn hàng test cho trang lịch sử
-- ============================================================
INSERT INTO orders
  (userid, ordercode, fullname, phone, address, deliverymethod, subtotal, shippingfee, total, status)
VALUES
  (2, 'YL20240628001', 'Nguyễn Văn A', '0987654321',
   '123 Lê Lợi, Quận 1, TP.HCM', 'delivery', 111000, 20000, 131000, 'delivered'),

  (2, 'YL20240628002', 'Nguyễn Văn A', '0987654321',
   '123 Lê Lợi, Quận 1, TP.HCM', 'delivery',  52000, 20000,  72000, 'pending');

INSERT INTO orderitems (orderid, productid, name, price, quantity, image) VALUES
  (1, 1, 'Trà Sữa Trân Châu Đen',    52000, 1, 'assets/images/menu/trasuatranchauden.avif'),
  (1, 2, 'Trà Sữa Matcha Trân Châu', 59000, 1, 'assets/images/menu/trasua.avif'),
  (2, 1, 'Trà Sữa Trân Châu Đen',    52000, 1, 'assets/images/menu/trasuatranchauden.avif');
