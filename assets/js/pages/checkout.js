// Checkout page script (extracted from pages/checkout.html)
document.addEventListener('DOMContentLoaded', () => {
  const cartItems = JSON.parse(localStorage.getItem('coffeeCart') || '[]');
  const orderItemsContainer = document.getElementById('orderItems');
  const subtotalAmount = document.getElementById('subtotalAmount');
  const shippingAmount = document.getElementById('shippingAmount');
  const totalAmount = document.getElementById('totalAmount');
  const confirmOrderBtn = document.getElementById('confirmOrderBtn');
  const checkoutForm = document.getElementById('checkoutForm');
  const deliveryButtons = document.querySelectorAll('.checkout-method');
  const shippingFee = 20000;
  let deliveryMethod = 'delivery';

  function formatCurrency(value) {
    return value.toLocaleString('vi-VN') + 'đ';
  }

  function getShippingFee() {
    return deliveryMethod === 'pickup' ? 0 : shippingFee;
  }

  function renderOrderItems() {
    orderItemsContainer.innerHTML = '';
    if (!cartItems.length) {
      orderItemsContainer.innerHTML = '<p class="empty-cart-message">Giỏ hàng đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.</p>';
      subtotalAmount.textContent = '0đ';
      shippingAmount.textContent = '0đ';
      totalAmount.textContent = '0đ';
      confirmOrderBtn.disabled = true;
      return;
    }

    let subtotal = 0;
    cartItems.forEach(item => {
      subtotal += item.price * item.quantity;
      const element = document.createElement('div');
      element.className = 'order-item';
      element.innerHTML = `
        <div class="order-item-info">
          <div class="order-item-title">${item.name}</div>
          <div class="order-item-meta">x${item.quantity} · ${formatCurrency(item.price)}</div>
        </div>
        <div class="order-item-price">${formatCurrency(item.price * item.quantity)}</div>
      `;
      orderItemsContainer.appendChild(element);
    });
    const fee = getShippingFee();
    subtotalAmount.textContent = formatCurrency(subtotal);
    shippingAmount.textContent = formatCurrency(fee);
    totalAmount.textContent = formatCurrency(subtotal + fee);
    confirmOrderBtn.disabled = false;
  }

  deliveryButtons.forEach(button => {
    button.addEventListener('click', () => {
      deliveryButtons.forEach(item => item.classList.remove('active'));
      button.classList.add('active');
      deliveryMethod = button.dataset.method;
      renderOrderItems();
    });
  });

  confirmOrderBtn.addEventListener('click', () => {
    if (!cartItems.length) return;
    if (!checkoutForm.checkValidity()) {
      checkoutForm.reportValidity();
      return;
    }

    const fullname = document.getElementById('fullname').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('address').value.trim();

    if (!fullname || !phone || !address) {
      checkoutForm.reportValidity();
      return;
    }
    // Build order object
    const subtotal = cartItems.reduce((s, it) => s + it.price * it.quantity, 0);
    const fee = getShippingFee();
    const order = {
      id: '#DH' + String(Date.now()).slice(-6),
      date: new Date().toLocaleDateString('vi-VN'),
      status: 'Chờ xác nhận',
      delivery: deliveryMethod === 'pickup' ? 'Đến lấy' : 'Giao hàng',
      customer: { fullname, phone, address, note: document.getElementById('note').value.trim() },
      items: cartItems.map(i => ({ name: i.name, quantity: i.quantity, price: i.price, image: i.image })),
      subtotal: subtotal,
      shipping: fee,
      total: subtotal + fee,
    };

    // Save to localStorage.orders (prepend)
    try {
      const existing = JSON.parse(localStorage.getItem('orders') || '[]');
      existing.unshift(order);
      localStorage.setItem('orders', JSON.stringify(existing));
    } catch (e) {
      localStorage.setItem('orders', JSON.stringify([order]));
    }

    // Clear cart and show in-page success screen
    localStorage.removeItem('coffeeCart');
    const orderSuccess = document.getElementById('orderSuccess');
    const checkoutSection = document.querySelector('.checkout-section');
    if (checkoutSection) checkoutSection.classList.add('hidden');
    if (orderSuccess) {
      orderSuccess.classList.remove('hidden');
      orderSuccess.scrollIntoView({ behavior: 'smooth' });
    } else {
      // fallback: redirect to menu
      window.location.href = '../Menu.html';
    }
  });

  renderOrderItems();
});
