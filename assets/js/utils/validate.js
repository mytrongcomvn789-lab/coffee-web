function togglePass(id, btn) {
  const input = document.getElementById(id);
  if (input.type === "password") {
    input.type = "text";
    btn.textContent = "🙈";
  } else {
    input.type = "password";
    btn.textContent = "👁";
  }
}

/* ──────────────────────────────────────────
 *  LOGIN (login.html)
 * ────────────────────────────────────────── */
function handleLogin(e) {
  e.preventDefault();
  const btn = document.getElementById("login-btn");
  const errEl = document.getElementById("login-error");
  const email = document.getElementById("login-email").value;
  const pass = document.getElementById("login-pass").value;

  btn.querySelector(".btn-text").style.display = "none";
  btn.querySelector(".btn-loader").style.display = "inline";
  btn.disabled = true;
  errEl.style.display = "none";

  // Demo: chỉ chấp nhận user@demo.com / 123456
  setTimeout(() => {
    if (email === "user@demo.com" && pass === "123456") {
      localStorage.setItem(
        "yl_user",
        JSON.stringify({ name: "Thành viên", email }),
      );
      window.location.href = "../index.html";
    } else {
      errEl.style.display = "flex";
      btn.querySelector(".btn-text").style.display = "inline";
      btn.querySelector(".btn-loader").style.display = "none";
      btn.disabled = false;
    }
  }, 1200);
}

/* ──────────────────────────────────────────
 *  ĐĂNG KÝ (dangky.html)
 * ────────────────────────────────────────── */
function checkPassStrength(val) {
  const fill = document.getElementById("strength-fill");
  const label = document.getElementById("strength-label");
  if (!fill || !label) return;

  let score = 0;
  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { w: "0%", color: "", text: "" },
    { w: "25%", color: "#e05252", text: "Rất yếu" },
    { w: "50%", color: "#f59e0b", text: "Yếu" },
    { w: "75%", color: "#3b82f6", text: "Tốt" },
    { w: "100%", color: "#10b981", text: "Mạnh" },
  ];
  const lvl = val.length === 0 ? levels[0] : levels[score] || levels[1];
  fill.style.width = lvl.w;
  fill.style.backgroundColor = lvl.color;
  label.textContent = lvl.text;
  label.style.color = lvl.color;
}

function checkConfirm() {
  const pass = document.getElementById("reg-pass");
  const conf = document.getElementById("reg-confirm");
  const hint = document.getElementById("confirm-hint");
  if (!pass || !conf || !hint) return;

  if (!conf.value) {
    hint.textContent = "";
    return;
  }

  if (pass.value === conf.value) {
    hint.textContent = "✅ Mật khẩu khớp";
    hint.style.color = "#10b981";
  } else {
    hint.textContent = "❌ Mật khẩu không khớp";
    hint.style.color = "#e05252";
  }
}

// Email live validate (đăng ký)
document.addEventListener("DOMContentLoaded", () => {
  const regEmail = document.getElementById("reg-email");
  if (regEmail) {
    regEmail.addEventListener("blur", function () {
      const hint = document.getElementById("email-hint");
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (this.value && !re.test(this.value)) {
        hint.textContent = "❌ Email không hợp lệ";
        hint.style.color = "#e05252";
      } else {
        hint.textContent = "";
      }
    });
  }
});

function handleRegister(e) {
  e.preventDefault();
  const btn = document.getElementById("reg-btn");
  const errEl = document.getElementById("reg-error");
  const pass = document.getElementById("reg-pass").value;
  const conf = document.getElementById("reg-confirm").value;
  const terms = document.getElementById("reg-terms")
    ? document.getElementById("reg-terms").checked
    : true;

  errEl.style.display = "none";

  if (pass !== conf) {
    errEl.textContent = "⚠️ Mật khẩu xác nhận không khớp.";
    errEl.style.display = "flex";
    return;
  }
  if (pass.length < 8) {
    errEl.textContent = "⚠️ Mật khẩu phải có ít nhất 8 ký tự.";
    errEl.style.display = "flex";
    return;
  }
  if (!terms) {
    errEl.textContent = "⚠️ Vui lòng đồng ý với điều khoản sử dụng.";
    errEl.style.display = "flex";
    return;
  }

  btn.querySelector(".btn-text").style.display = "none";
  btn.querySelector(".btn-loader").style.display = "inline";
  btn.disabled = true;

  setTimeout(() => {
    document.getElementById("register-form").style.display = "none";
    document.getElementById("reg-success").style.display = "block";
  }, 1400);
}

/* ──────────────────────────────────────────
 *  KHUYẾN MÃI (khuyenmai.html)
 * ────────────────────────────────────────── */
document.addEventListener("DOMContentLoaded", () => {
  const filterBtns = document.querySelectorAll(".filter-btn");
  const allCards = document.querySelectorAll(".news-card[data-tag]");

  if (filterBtns.length) {
    filterBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        filterBtns.forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");
        const filter = btn.dataset.filter;
        allCards.forEach((card) => {
          card.style.display =
            filter === "all" || card.dataset.tag === filter ? "" : "none";
        });
      });
    });
  }
});

function handleSubscribe(e) {
  e.preventDefault();
  document.getElementById("cta-success").style.display = "block";
  e.target.reset();
}

/* ──────────────────────────────────────────
 *  LIÊN HỆ (lienhe.html)
 * ────────────────────────────────────────── */
function handleContactSubmit(e) {
  e.preventDefault();
  document.getElementById("contact-form").style.display = "none";
  document.getElementById("form-success").style.display = "block";
}

function resetForm() {
  document.getElementById("contact-form").style.display = "";
  document.getElementById("form-success").style.display = "none";
  document.getElementById("contact-form").reset();
}
