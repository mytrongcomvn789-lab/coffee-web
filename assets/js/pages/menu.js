const categoryLinks = document.querySelectorAll('.menu-category-link');
const productGrid = document.querySelector('.product-grid');
const productCards = Array.from(document.querySelectorAll('.product-card'));
const resultsText = document.querySelector('.menu-results');
const searchInput = document.getElementById('product-search');
const sortSelect = document.getElementById('sort');
let currentCategory = 'all';
let currentQuery = '';
let currentSort = 'popular';
const originalOrder = productCards.slice();

function parsePrice(card) {
  const priceText = card.querySelector('.product-price')?.textContent || '';
  return Number(priceText.replace(/[^0-9]/g, '')) || 0;
}

function sortVisibleCards(visibleCards, sortType) {
  const sortedCards = visibleCards.slice();
  if (sortType === 'low-high') {
    sortedCards.sort((a, b) => parsePrice(a) - parsePrice(b));
  } else if (sortType === 'high-low') {
    sortedCards.sort((a, b) => parsePrice(b) - parsePrice(a));
  } else if (sortType === 'newest') {
    sortedCards.sort((a, b) => originalOrder.indexOf(a) - originalOrder.indexOf(b));
  }
  sortedCards.forEach((card) => productGrid.appendChild(card));
}

function updateProducts() {
  const normalizedQuery = currentQuery.trim().toLowerCase();
  const visibleCards = [];
  productCards.forEach((card) => {
    const cardCategory = card.dataset.category;
    const cardName = card.dataset.name.toLowerCase();
    const matchesCategory = currentCategory === 'all' || cardCategory === currentCategory;
    const matchesSearch = !normalizedQuery || cardName.includes(normalizedQuery);
    const shouldShow = matchesCategory && matchesSearch;
    card.style.display = shouldShow ? '' : 'none';
    if (shouldShow) visibleCards.push(card);
  });
  resultsText.textContent = `Hiển thị ${visibleCards.length} sản phẩm`;
  sortVisibleCards(visibleCards, currentSort);
}

categoryLinks.forEach((link) => {
  link.addEventListener('click', (event) => {
    event.preventDefault();
    categoryLinks.forEach((item) => item.classList.remove('active'));
    link.classList.add('active');
    currentCategory = link.dataset.category;
    updateProducts();
  });
});

searchInput.addEventListener('input', () => {
  currentQuery = searchInput.value;
  updateProducts();
});

sortSelect.addEventListener('change', () => {
  currentSort = sortSelect.value;
  updateProducts();
});

const cartCountElem = document.getElementById('cartCount');
let cartItems = JSON.parse(localStorage.getItem('coffeeCart') || '[]');

function saveCart() {
  localStorage.setItem('coffeeCart', JSON.stringify(cartItems));
}

function updateCartCount() {
  const totalQty = cartItems.reduce((count, item) => count + item.quantity, 0);
  cartCountElem.textContent = totalQty;
  if (totalQty > 0) {
    cartCountElem.classList.remove('hidden');
  } else {
    cartCountElem.classList.add('hidden');
  }
}

function addProductToCart(card, button) {
  const name = card.dataset.name;
  const price = parsePrice(card);
  const image = card.querySelector('img')?.src || '';
  const existingItem = cartItems.find((item) => item.name === name);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cartItems.push({ name, price, quantity: 1, image });
  }

  saveCart();
  updateCartCount();

  if (button) {
    const originalHTML = button.innerHTML;
    button.disabled = true;
    button.classList.add('btn-added');
    button.innerHTML = '✓ Đã thêm';
    setTimeout(() => {
      button.classList.remove('btn-added');
      button.innerHTML = originalHTML;
      button.disabled = false;
    }, 1500);
  }
}

document.querySelectorAll('.btn-add').forEach((button) => {
  button.addEventListener('click', () => {
    const card = button.closest('.product-card');
    if (card) {
      addProductToCart(card, button);
    }
  });
});

updateCartCount();
updateProducts();
