// * main.js – YourLife Coffee
//  * Script khởi tạo chung, chạy trên MỌI trang.
//  * Load sau account-dropdown.js và helper.js.
(function () {
  // ── Active nav link ───────────────────────────────────────────────────────
  function setActiveNav() {
    const currentPath = location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('.nav-link').forEach((link) => {
      const href = (link.getAttribute('href') || '').split('/').pop();
      if (href && href === currentPath) {
        link.classList.add('active');
      }
    });
  }

  // ── Smooth scroll cho anchor #hash ───────────────────────────────────────
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener('click', function (e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (!target) return;
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });
  }

  // ── Khởi chạy ────────────────────────────────────────────────────────────
  function init() {
    setActiveNav();
    initSmoothScroll();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
