(function () {
  // ── 1. Xác định prefix đường dẫn ──────────────────────────────────────────
  // Nếu đang ở thư mục /pages/ thì prefix = "./"  (cùng cấp)
  // Nếu đang ở root thì prefix = "./pages/"
  const isInPages = location.pathname.includes("/pages/");
  const prefix = isInPages ? "./" : "./pages/";

  // ── 2. Inject CSS dropdown (inline để không cần file riêng) ───────────────
  const style = document.createElement("style");
  style.textContent = `
    /* Wrapper bọc button + dropdown */
    .account-wrapper {
      position: relative;
      display: inline-flex;
      align-items: center;
    }

    /* Button Tài khoản */
    .account-wrapper .account {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      background: none;
      border: none;
      font: inherit;
      color: inherit;
      padding: 6px 10px;
      border-radius: 8px;
      transition: background 0.18s;
      text-decoration: none;
    }
    .account-wrapper .account:hover {
      background: rgba(255,255,255,0.08);
    }

    /* Mũi tên xoay khi mở */
    .account-wrapper .account .icon-char {
      display: inline-block;
      transition: transform 0.22s;
    }
    .account-wrapper.open .account .icon-char {
      transform: rotate(180deg);
    }

    /* Dropdown panel */
    .account-dropdown {
      display: none;
      position: absolute;
      top: calc(100% + 10px);
      right: 0;
      min-width: 170px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.13), 0 2px 8px rgba(0,0,0,0.07);
      overflow: hidden;
      z-index: 9999;
      animation: dropdownFadeIn 0.18s ease;
    }
    .account-wrapper.open .account-dropdown {
      display: block;
    }

    @keyframes dropdownFadeIn {
      from { opacity: 0; transform: translateY(-6px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* Item trong dropdown */
    .account-dropdown a,
    .account-dropdown button {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      padding: 12px 18px;
      font-size: 0.92rem;
      color: #1a2e1a;
      text-decoration: none;
      background: none;
      border: none;
      font: inherit;
      cursor: pointer;
      transition: background 0.15s, color 0.15s;
      text-align: left;
    }
    .account-dropdown a:hover,
    .account-dropdown button:hover {
      background: #f3f8f4;
      color: #1e4d3a;
    }
    .account-dropdown .dd-divider {
      height: 1px;
      background: #e8ede8;
      margin: 4px 0;
    }
    .account-dropdown .dd-user-info {
      padding: 12px 18px 8px;
      font-size: 0.82rem;
      color: #6b7a6b;
      border-bottom: 1px solid #e8ede8;
    }
    .account-dropdown .dd-user-info strong {
      display: block;
      font-size: 0.95rem;
      color: #1a2e1a;
      margin-bottom: 2px;
    }
    .account-dropdown .dd-logout {
      color: #c0392b !important;
    }
    .account-dropdown .dd-logout:hover {
      background: #fff5f5 !important;
      color: #c0392b !important;
    }
  `;
  document.head.appendChild(style);

  // ── 3. Upgrade mỗi .account button thành wrapper + dropdown ───────────────
  function buildDropdown() {
    // Lấy user đã đăng nhập (nếu có)
    let user = null;
    try {
      user = JSON.parse(localStorage.getItem("yl_user"));
    } catch {}

    document
      .querySelectorAll(
        ".header-action .account, .header-action button.account",
      )
      .forEach(function (btn) {
        // Tránh khởi tạo 2 lần
        if (btn.closest(".account-wrapper")) return;

        // Tạo wrapper
        const wrapper = document.createElement("div");
        wrapper.className = "account-wrapper";
        btn.parentNode.insertBefore(wrapper, btn);
        wrapper.appendChild(btn);

        // Đảm bảo btn là button (không phải <a>)
        // Giữ nguyên nội dung gốc, chỉ đảm bảo có icon-char
        if (!btn.querySelector(".icon-char")) {
          const chevron = document.createElement("i");
          chevron.className = "icon-char";
          btn.appendChild(chevron);
        }

        // Tạo dropdown
        const dd = document.createElement("div");
        dd.className = "account-dropdown";

        if (user) {
          // Đã đăng nhập
          dd.innerHTML = `
          <div class="dd-user-info">
            <strong>${escHtml(user.name || "Thành viên")}</strong>
            ${escHtml(user.email || "")}
          </div>
          <a href="${prefix}profile.html">
            <span>👤</span> Tài khoản của tôi
          </a>
          <a href="${prefix}orders.html">
            <span>📦</span> Đơn hàng
          </a>
          <div class="dd-divider"></div>
          <button class="dd-logout" id="yl-logout-btn">
            <span>🚪</span> Đăng xuất
          </button>
        `;
        } else {
          // Chưa đăng nhập
          dd.innerHTML = `
          <a href="${prefix}login.html">
            <span>👤</span> Đăng nhập
          </a>
          <a href="${prefix}dangky.html">
            <span>🛡</span> Đăng ký
          </a>
        `;
        }

        wrapper.appendChild(dd);

        // Toggle mở/đóng
        btn.addEventListener("click", function (e) {
          e.stopPropagation();
          wrapper.classList.toggle("open");
        });

        // Đăng xuất
        const logoutBtn = dd.querySelector("#yl-logout-btn");
        if (logoutBtn) {
          logoutBtn.addEventListener("click", function () {
            localStorage.removeItem("yl_user");
            location.reload();
          });
        }
      });

    // Click ngoài thì đóng tất cả
    document.addEventListener("click", function () {
      document.querySelectorAll(".account-wrapper.open").forEach(function (w) {
        w.classList.remove("open");
      });
    });
  }

  function escHtml(str) {
    return String(str).replace(/[&<>"']/g, function (c) {
      return {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
      }[c];
    });
  }

  // Chạy sau khi DOM sẵn sàng
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", buildDropdown);
  } else {
    buildDropdown();
  }
})();
