// Render order history from localStorage.orders (array)
document.addEventListener('DOMContentLoaded', () => {
  const orderList = document.getElementById('orderList');
  // Try to read saved orders, otherwise use sample data
  let orders = [];
  try {
    orders = JSON.parse(localStorage.getItem('orders') || '[]');
  } catch (e) {
    orders = [];
  }

  if (!orders.length) {
    // sample placeholder
    orders = [
      { id: '#DH8029', date: '28/6/2026', status: 'Chờ xác nhận', items: [{name:'Trà Sữa Matcha Trân Châu', qty:1, price:59000, image:'../assets/images/menu/cafesuada.avif'}], total:79000 },
      { id: '#DH5000', date: '28/6/2026', status: 'Chờ xác nhận', items: [{name:'Trà Sữa Trân Châu Đen', qty:1, price:52000, image:'../assets/images/menu/trasua.avif'}], total:72000 },
    ];
  }

  function formatCurrency(v){ return v.toLocaleString('vi-VN') + 'đ'; }

  orders.forEach(order => {
    const card = document.createElement('div');
    card.className = 'order-card';
    card.innerHTML = `
      <div class="order-card-head">
        <div>
          <div class="order-id">${order.id}</div>
          <div class="order-meta">${order.date} · ${order.delivery || 'Giao hàng'}</div>
        </div>
        <div class="status-badge">${order.status}</div>
      </div>
      <div class="order-items">
        <img class="order-item-thumb" src="${order.items[0].image}" alt="" />
        <div class="order-item-info">
          <div class="order-item-title">${order.items[0].name} × ${order.items[0].qty}</div>
        </div>
        <div class="order-item-price">${formatCurrency(order.items[0].price)}</div>
      </div>
      <div class="order-total">Tổng cộng <span style="margin-left:12px">${formatCurrency(order.total)}</span></div>
    `;
    orderList.appendChild(card);
  });
});
