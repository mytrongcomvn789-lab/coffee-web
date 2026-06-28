/* ──────────────────────────────────────────
 *  QUÊN MẬT KHẨU (forgot-password.html)
 * ────────────────────────────────────────── */
function handleForgotPassword(e) {
  e.preventDefault();
  const btn = document.getElementById("forgot-btn");
  const errEl = document.getElementById("forgot-error");
  const emailInput = document.getElementById("forgot-email");
  const email = emailInput.value.trim();
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  errEl.style.display = "none";
  emailInput.classList.remove("input-error");

  if (!re.test(email)) {
    errEl.style.display = "block";
    emailInput.classList.add("input-error");
    return;
  }

  btn.querySelector(".btn-text").style.display = "none";
  btn.querySelector(".btn-loader").style.display = "inline";
  btn.disabled = true;

  // Demo: giả lập gọi API gửi email khôi phục
  setTimeout(() => {
    btn.querySelector(".btn-text").style.display = "inline";
    btn.querySelector(".btn-loader").style.display = "none";
    btn.disabled = false;

    document.getElementById("forgot-sent-email").textContent = email;
    document.getElementById("forgot-step-form").style.display = "none";
    document.getElementById("forgot-step-success").style.display = "block";
  }, 900);
}

function resetForgotForm() {
  document.getElementById("forgot-email").value = "";
  document.getElementById("forgot-error").style.display = "none";
  document.getElementById("forgot-step-success").style.display = "none";
  document.getElementById("forgot-step-form").style.display = "block";
}
