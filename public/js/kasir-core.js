/* ============================================================
   kasir-core.js — Sistem Popup & Interaksi Global Kasir
\============================================================ */

const overlay = document.getElementById("popup-overlay");
let activePopup = null;

function openPopup(id) {
  if (activePopup) activePopup.classList.remove("is-open");

  const el = document.getElementById(id);
  if (!el) return;

  // Khusus panel notifikasi toggle on/off
  if (id === "popup-notif-panel") {
    const isOpen = el.classList.contains("is-open");
    if (isOpen) {
      el.classList.remove("is-open");
      document.removeEventListener("click", closeNotifOnOutsideClick);
    } else {
      el.classList.add("is-open");
      setTimeout(() => {
        document.addEventListener("click", closeNotifOnOutsideClick);
      }, 0);
    }
    return;
  }

  el.classList.add("is-open");
  if (overlay) overlay.classList.add("is-open");
  activePopup = el;

  const notifPanel = document.getElementById("popup-notif-panel");
  if (notifPanel) notifPanel.classList.remove("is-open");
}

function closePopup() {
  if (activePopup) {
    activePopup.classList.remove("is-open");
    activePopup = null;
  }
  if (overlay) overlay.classList.remove("is-open");
}

function closeNotifOnOutsideClick(e) {
  const panel = document.getElementById("popup-notif-panel");
  const btn = e.target.closest('[data-open="popup-notif-panel"]');
  const isCloseBtn = e.target.closest('[data-close]');
  if (panel && !panel.contains(e.target) && !btn) {
    if ((!panel.contains(e.target) && !btn) || (panel.contains(e.target) && isCloseBtn)) {
      panel.classList.remove("is-open");
      document.removeEventListener("click", closeNotifOnOutsideClick);
    }
  }
}

// Daftarkan fungsi ke window object agar bisa dipanggil file JS lain
window._openPopup = openPopup;
window._closePopup = closePopup;

document.addEventListener("DOMContentLoaded", function () {
  // Listener klik tombol pembuka popup berdasarkan atribut [data-open]
  document.addEventListener("click", function (e) {
    const trigger = e.target.closest("[data-open]");
    if (trigger) {
      e.preventDefault();
      const popupId = trigger.getAttribute("data-open");
      openPopup(popupId);
    }
  });

  // Listener klik tombol penutup universal [data-close] atau .btn-batal
  document.addEventListener("click", function (e) {
    if (e.target.closest("[data-close]") || e.target.closest(".btn-batal")) {
      e.preventDefault();
      closePopup();
    }
  });

  // Klik overlay untuk menutup popup
  overlay?.addEventListener("click", closePopup);
});