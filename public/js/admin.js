/* ============================================================
   admin.js — Shared Logic (semua halaman pakai ini)
   Berisi:
   1. Drawer (hamburger menu mobile)
   3. Notif panel (dropdown)
   4. Active nav highlight (deteksi halaman aktif dari URL)
   5. Pagination (shared behavior)

   PERUBAHAN dari versi SPA:
   - switchPage() dihapus, navigasi pakai <a href> biasa
   - Tidak ada page-section show/hide
   - Active nav item ditentukan dari window.location.pathname
============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  /* ============================================================
     2. DRAWER — hamburger menu mobile
     Buka: klik #hamburger-btn
     Tutup: klik overlay, klik link di drawer, tekan Escape
  ============================================================ */
  const hamburgerBtn = document.getElementById("hamburger-btn");
  const drawer = document.getElementById("drawer");
  const drawerOverlay = document.getElementById("drawer-overlay");

  function openDrawer() {
    drawer?.classList.add("is-open");
    drawerOverlay?.classList.add("is-open");
    hamburgerBtn?.classList.add("is-open");
    document.body.style.overflow = "hidden";
  }

  function closeDrawer() {
    drawer?.classList.remove("is-open");
    drawerOverlay?.classList.remove("is-open");
    hamburgerBtn?.classList.remove("is-open");
    document.body.style.overflow = "";
  }

  hamburgerBtn?.addEventListener("click", () => {
    drawer?.classList.contains("is-open") ? closeDrawer() : openDrawer();
  });

  drawerOverlay?.addEventListener("click", closeDrawer);

  // Expose ke global supaya popup system bisa pakai
  window._closeDrawer = closeDrawer;

  /* ============================================================
     4. PAGINATION — shared behavior
     Klik nomor halaman → update active state
  ============================================================ */
  document.querySelectorAll(".pagination").forEach((pag) => {
    pag.querySelectorAll(".page-number").forEach((btn) => {
      btn.addEventListener("click", function () {
        pag
          .querySelectorAll(".page-number")
          .forEach((b) => b.classList.remove("active"));
        this.classList.add("active");
      });
    });
  });
});
