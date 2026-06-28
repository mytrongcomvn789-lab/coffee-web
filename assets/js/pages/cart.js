document.addEventListener('DOMContentLoaded', () => {
  const cartItemsWrapper = document.getElementById('cartItems');
  const cartEmpty = document.getElementById('cartEmpty');
  const cartContainer = document.getElementById('cartContainer');
  const cartSummaryBox = document.getElementById('cartSummaryBox');
  const summaryItems = document.getElementById('summaryItems');
  const summaryPrice = document.getElementById('summaryPrice');
  const summaryTotal = document.getElementById('summaryTotal');
  const checkoutBtn = document.getElementById('checkoutBtn');
  const clearCartBtn = document.getElementById('clearCartBtn');

  if (
    !cartItemsWrapper ||
    !cartEmpty ||
    !cartContainer ||
    !cartSummaryBox ||
    !summaryItems ||
    !summaryPrice ||
    !summaryTotal ||
    !checkoutBtn ||
    !clearCartBtn
  ) {
    return;
  }

  let cartItems = JSON.parse(localStorage.getItem('coffeeCart') || '[]');

  function formatCurrency(value) {
    return value.toLocaleString('vi-VN') + 'đ';
  }

  function saveCart() {
    localStorage.setItem('coffeeCart', JSON.stringify(cartItems));
  }

  function updateSummary() {
    const totalPrice = cartItems.reduce(
      (sum, item) => sum + item.price * item.quantity,
      0,
    );

    summaryPrice.textContent = formatCurrency(totalPrice);
    summaryTotal.textContent = formatCurrency(totalPrice);

    // Update summary items
    summaryItems.innerHTML = '';
    cartItems.forEach((item) => {
      const summaryItem = document.createElement('div');
      summaryItem.className = 'summary-item';
      summaryItem.innerHTML = `
        <span>${item.name} • ${item.quantity}</span>
        <strong>${formatCurrency(item.price * item.quantity)}</strong>
      `;
      summaryItems.appendChild(summaryItem);
    });
  }

  function renderCart() {
    if (!cartItems.length) {
      cartEmpty.classList.remove('hidden');
      cartItemsWrapper.classList.add('hidden');
      cartSummaryBox.classList.add('hidden');
      cartContainer.classList.add('empty-state');
      return;
    }

    cartEmpty.classList.add('hidden');
    cartItemsWrapper.classList.remove('hidden');
    cartSummaryBox.classList.remove('hidden');
    cartContainer.classList.remove('empty-state');
    cartItemsWrapper.innerHTML = '';

    cartItems.forEach((item) => {
      const itemRow = document.createElement('div');
      itemRow.className = 'cart-item';
      itemRow.innerHTML = `
        <img src="${item.image}" alt="${item.name}" />
        <div class="cart-item-meta">
          <h4>${item.name}</h4>
          <span class="price">${formatCurrency(item.price)}</span>
        </div>
        <div class="cart-item-actions">
          <div class="cart-qty-controls">
            <button type="button" class="cart-qty-btn" data-action="decrease" data-name="${item.name}">-</button>
            <span class="cart-qty-value">${item.quantity}</span>
            <button type="button" class="cart-qty-btn" data-action="increase" data-name="${item.name}">+</button>
          </div>
          <button type="button" class="cart-remove" data-name="${item.name}">Xóa</button>
        </div>
      `;

      itemRow
        .querySelector('[data-action="decrease"]')
        .addEventListener('click', () => {
          if (item.quantity > 1) {
            item.quantity -= 1;
          } else {
            cartItems = cartItems.filter((cartItem) => cartItem.name !== item.name);
          }
          saveCart();
          renderCart();
          updateSummary();
        });

      itemRow
        .querySelector('[data-action="increase"]')
        .addEventListener('click', () => {
          item.quantity += 1;
          saveCart();
          renderCart();
          updateSummary();
        });

      itemRow.querySelector('.cart-remove').addEventListener('click', () => {
        cartItems = cartItems.filter((cartItem) => cartItem.name !== item.name);
        saveCart();
        renderCart();
        updateSummary();
      });

      cartItemsWrapper.appendChild(itemRow);
    });
  }

  clearCartBtn.addEventListener('click', () => {
    cartItems = [];
    saveCart();
    renderCart();
    updateSummary();
  });

  checkoutBtn.addEventListener('click', () => {
    if (!cartItems.length) {
      return;
    }
    window.location.href = './checkout.html';
  });

  renderCart();
  updateSummary();
});
