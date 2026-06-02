/* ============================================================
   penjualan-kasir.js — Logika Halaman Riwayat Penjualan Kasir
   Hanya untuk Search, Filter, dan Popup Detail Transaksi
============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  const openFn = window._openPopup;
  const tabelPenjualan = document.getElementById("tabel-penjualan");

  /* 1. REAL-TIME SEARCH TRANSAKSI */
  const searchInput = document.querySelector(".search-wrapper input");
  searchInput?.addEventListener("input", function () {
    const keyword = this.value.toLowerCase().trim();
    document.querySelectorAll("#tabel-penjualan tbody tr").forEach((row) => {
      row.style.display = row.textContent.toLowerCase().includes(keyword) ? "" : "none";
    });
  });

  /* 2. HANDLER DETAIL TRANSAKSI (MEMBUKA POPUP) */
  function bukaDetailTransaksi(dataset) {
// Ambil elemen HTML (mengantisipasi jika ada perbedaan ID antara versi admin/kasir)
    const elId = document.getElementById("detail-id-transaksi") || document.getElementById("detail-trx-id");
    const elWaktu = document.getElementById("detail-trx-waktu");
    const elItems = document.getElementById("detail-trx-items");
    const elPayment = document.getElementById("detail-trx-payment");
    const elNominal = document.getElementById("detail-trx-nominal");
    const elStatus = document.getElementById("detail-trx-status");

    // Isi data ke dalam modal popup jika elemennya ada di penjualan.blade.php
    if (elId) elId.textContent = dataset.id || "#TRX-0000";
    if (elWaktu) elWaktu.textContent = dataset.waktu || "-";
    if (elItems) elItems.textContent = dataset.items || "-";
    if (elPayment) elPayment.textContent = dataset.payment || "-";
    if (elNominal) elNominal.textContent = dataset.nominal || "-";
    if (elStatus) elStatus.textContent = dataset.status || "-";

    // Buka popup detail menggunakan fungsi dari kasir-core.js
    if (typeof openPopup === "function") {
      openPopup("popup-detail-transaksi");
    } else if (typeof openFn === "function") {
      openFn("popup-detail-transaksi");
    } else {
      console.error("Fungsi pembuka popup tidak ditemukan. Pastikan kasir-core.js sudah di-load.");
    }
  }

  // Klik baris pada tabel desktop
  document.addEventListener("click", function (e) {
    const detailBtn = e.target.closest(".view-btn, .trx-card-detail-btn");
    
    if (detailBtn) {
      e.preventDefault();
      const row = detailBtn.closest("tr");
      const card = detailBtn.closest(".trx-card");
      let dataTransaksi = {};

      if (row) {
        // Mode Desktop: Ambil teks langsung dari kolom-kolom tabel (td)
        const cells = row.querySelectorAll("td");
        dataTransaksi.id = cells[1]?.textContent.trim();
        dataTransaksi.waktu = cells[2]?.textContent.trim();
        dataTransaksi.items = cells[3]?.textContent.trim();
        dataTransaksi.payment = cells[4]?.textContent.trim();
        dataTransaksi.nominal = cells[5]?.textContent.trim();
        dataTransaksi.status = cells[6]?.textContent.trim();
      } else if (card) {
        // Mode Mobile: Ambil teks dari elemen di dalam card
        dataTransaksi.id = card.querySelector(".trx-card-id")?.textContent.trim();
        dataTransaksi.waktu = card.querySelector(".trx-card-date")?.textContent.trim();
        dataTransaksi.items = card.querySelector(".trx-card-items")?.textContent.trim();
        dataTransaksi.payment = card.querySelector(".trx-card-payment")?.textContent.trim();
        dataTransaksi.nominal = card.querySelector(".trx-card-amount")?.textContent.trim();
        dataTransaksi.status = card.querySelector(".status-badge")?.textContent.trim();
      }

      // Kirim data ke fungsi pembuka popup
      bukaDetailTransaksi(dataTransaksi);
    }
  });

    /* ============================================================
     3. EXPORT BUTTON PLACEHOLDER
     Menangani klik pada tombol export data
     ============================================================ */
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