/**
 * helper.js – YourLife Coffee
 * Logic giỏ hàng dùng chung cho tất cả trang (trang chủ, menu, ...)
 */
(function () {
  const CART_KEY = 'coffeeCart';

  // ── Đọc / ghi localStorage ────────────────────────────────────────────────
  function getCart() {
    try {
      return JSON.parse(localStorage.getItem(CART_KEY) || '[]');
    } catch {
      return [];
    }
  }

  function saveCart(items) {
    localStorage.setItem(CART_KEY, JSON.stringify(items));
  }

  // ── Format tiền ───────────────────────────────────────────────────────────
  function formatCurrency(value) {
    return Number(value).toLocaleString('vi-VN') + 'đ';
  }

  // ── Đọc giá từ card (text ".product-price" hoặc data-price) ──────────────
  function parsePrice(card) {
    if (card.dataset.price) return Number(card.dataset.price) || 0;
    const text = card.querySelector('.product-price')?.textContent || '';
    return Number(text.replace(/[^0-9]/g, '')) || 0;
  }

  // ── Cập nhật badge số lượng trên icon giỏ hàng ───────────────────────────
  function updateCartBadge() {
    const items = getCart();
    const total = items.reduce((s, i) => s + (i.quantity || 1), 0);
    document.querySelectorAll('#cartCount, .cart-count').forEach((el) => {
      el.textContent = total;
      el.classList.toggle('hidden', total === 0);
    });
  }

  // ── Thêm sản phẩm vào giỏ ────────────────────────────────────────────────
  function addToCart(card, button) {
    const name =
      card.dataset.name ||
      card.querySelector('.product-name')?.textContent?.trim() ||
      'Sản phẩm';
    const price = parsePrice(card);
    const image = card.querySelector('img')?.src || '';

    const items = getCart();
    const existing = items.find((i) => i.name === name);
    if (existing) {
      existing.quantity += 1;
    } else {
      items.push({ name, price, quantity: 1, image });
    }
    saveCart(items);
    updateCartBadge();

    // Feedback nút
    if (button) {
      const original = button.innerHTML;
      button.disabled = true;
      button.classList.add('btn-added');
      button.innerHTML = '✓ Đã thêm';
      setTimeout(() => {
        button.disabled = false;
        button.classList.remove('btn-added');
        button.innerHTML = original;
      }, 1500);
    }
  }

  // ── Gắn sự kiện cho tất cả .btn-add trên trang ───────────────────────────
  function initAddToCartButtons() {
    document.querySelectorAll('.btn-add').forEach((button) => {
      // Tránh gắn 2 lần
      if (button.dataset.cartInit) return;
      button.dataset.cartInit = '1';

      button.addEventListener('click', () => {
        const card = button.closest('.product-card');
        if (card) addToCart(card, button);
      });
    });
  }

  // ── Expose ra window để menu.html vẫn dùng được nếu cần ──────────────────
  window.YLCart = {
    getCart,
    saveCart,
    formatCurrency,
    parsePrice,
    updateCartBadge,
    addToCart,
  };

  // ── Chạy sau khi DOM sẵn sàng ────────────────────────────────────────────
  function init() {
    updateCartBadge();
    initAddToCartButtons();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
