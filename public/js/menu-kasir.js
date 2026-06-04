/* ============================================================
   menu-kasir.js — Logika Halaman Manajemen Menu Sisi Kasir
   Hanya untuk Filter, Search, Toggle Status, & Export
============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  const openFn = window._openPopup;

  /* 1. TAB FILTER KATEGORI */
  document.querySelectorAll(".category-tabs .tab[data-kategori]").forEach((btn) => {
    btn.addEventListener("click", function () {
      document.querySelectorAll(".category-tabs .tab[data-kategori]").forEach((b) => b.classList.remove("active"));
      this.classList.add("active");

      const kategori = this.dataset.kategori;

      // Filter card mobile (jika ada)
      document.querySelectorAll(".menu-card[data-kategori]").forEach((card) => {
        card.style.display = (kategori === "all" || card.dataset.kategori === kategori) ? "" : "none";
      });

      // Filter baris tabel desktop
      document.querySelectorAll("#tabel-menu tbody tr[data-kategori]").forEach((row) => {
        row.style.display = (kategori === "all" || row.dataset.kategori === kategori) ? "" : "none";
      });
    });
  });

  /* 2. REAL-TIME SEARCH FILTER */
  const searchInput = document.querySelector(".search-wrapper input");
  searchInput?.addEventListener("input", function () {
    const keyword = this.value.toLowerCase().trim();

    document.querySelectorAll(".menu-card").forEach((card) => {
      card.style.display = card.textContent.toLowerCase().includes(keyword) ? "" : "none";
    });

    document.querySelectorAll("#tabel-menu tbody tr").forEach((row) => {
      row.style.display = row.textContent.toLowerCase().includes(keyword) ? "" : "none";
    });
  });

  /* 3. TOGGLE STATUS KETERSEDIAAN (Read-only / Update sesuai hak akses) */
  // Catatan: Jika kasir tidak boleh mengubah status, input checkbox di Blade diberi atribut 'disabled'
  document.querySelectorAll(".toggle-status-menu").forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const menuId = this.dataset.id;
      const isChecked = this.checked;
      console.log(`Menu ID ${menuId} status diubah menjadi: ${isChecked ? 'tersedia' : 'habis'}`);
      // Lakukan hit AJAX update status kasir di sini jika diperlukan
    });
  });

  /* 4. PICU POPUP EXPORT */
  const btnExport = document.getElementById("btnExport");
  const closeExportBtn = document.getElementById('closeExportBtn');
  if (btnExport) {
    btnExport.addEventListener("click", function (e) {
      e.preventDefault();
      
      // Hitung menu yang sedang tampil di tabel untuk dioper ke popup export range count
      const visibleRows = document.querySelectorAll("#tabel-menu tbody tr:not([style*='display: none'])");
      const countEl = document.getElementById("exportRangeCount");
      if (countEl) countEl.textContent = `(${visibleRows.length} Menu)`;

      if (typeof openFn === "function") openFn("popupExport");
    });
  }

  if (closeExportBtn) {
    closeExportBtn.addEventListener('click', function (e) {
      e.preventDefault();
      if (typeof window._closePopup === "function") {
        window._closePopup?.('popupExport');
      } else if (typeof window.triggerGlobalClose === "function") {
        window.triggerGlobalClose?.('popupExport');
      }
    });
  }
    // C. Memilih Opsi Format Dokumen (Excel / PDF) secara Visual
  document.querySelectorAll('.export-option').forEach(opt => {
    opt.addEventListener('click', function () {
      document.querySelectorAll('.export-option').forEach(o => o.classList.remove('selected'));
      this.classList.add('selected');
    });
  });

  // D. Aksi Tombol Konfirmasi Unduh Data
  const btnUnduhData = document.getElementById('btnUnduhData');
  if (btnUnduhData) {
    btnUnduhData.addEventListener('click', function (e) {
      e.preventDefault();
      
      // Ambil tipe format data yang dipilih (.selected)
      const selectedOpt = document.querySelector('.export-option.selected');
      const fmtType = selectedOpt ? selectedOpt.dataset.fmt : 'xlsx';
      
      // Ambil opsi rentang data radio button yang dipilih
      const rentangOpt = document.querySelector('input[name="exportRange"]:checked');
      const rentangNilai = rentangOpt ? rentangOpt.value : 'current';

      // Tutup popup setelah tombol diklik
      if (typeof window._closePopup === "function") {
        window._closePopup?.('popupExport');
      } else if (typeof window.triggerGlobalClose === "function") {
        window.triggerGlobalClose?.('popupExport');
      }
      
      // Tampilkan umpan balik Toast Sukses
      if (typeof window.showToast === "function") {
        window.showToast(`Data (${rentangNilai}) berhasil diunduh sebagai ${fmtType.toUpperCase()} ✓`);
      }
    });
  }
});