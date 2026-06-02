/* ============================================================
   penjualan.js — Logika khusus halaman Detail Penjualan
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  /* ============================================================
     1. REAL-TIME SEARCH FILTER
     Menyaring kartu (mobile) dan baris tabel (desktop) saat mengetik
     ============================================================ */
  const searchInputs = document.querySelectorAll(".search-wrapper input");

  searchInputs.forEach((input) => {
    input.addEventListener("input", function () {
      const keyword = this.value.toLowerCase().trim();

      // Filter card versi mobile
      document.querySelectorAll(".trx-card").forEach((card) => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(keyword) ? "" : "none";
      });

      // Filter baris tabel versi desktop
      document.querySelectorAll("#tabel-penjualan tbody tr").forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? "" : "none";
      });
    });
  });

  /* ============================================================
     2. TOMBOL "LIHAT DETAIL" — Mengisi Data & Membuka Popup
     Menangkap klik pada tombol detail transaksi di tabel maupun card
     ============================================================ */
  document.addEventListener("click", function (e) {
    const detailBtn = e.target.closest(".view-btn, .trx-card-detail-btn");
    if (detailBtn) {
      // Ambil data dari baris/card terdekat
      const row = detailBtn.closest("tr");
      const card = detailBtn.closest(".trx-card");

      let trxId, waktu, items, payment, nominal, status;

      if (row) {
        // Mode Desktop: Ambil teks dari kolom-kolom tabel (td)
        const cells = row.querySelectorAll("td");
        trxId = cells[1]?.textContent.trim();
        waktu = cells[2]?.textContent.trim();
        items = cells[3]?.textContent.trim();
        payment = cells[4]?.textContent.trim();
        nominal = cells[5]?.textContent.trim();
        status = cells[6]?.textContent.trim();
      } else if (card) {
        // Mode Mobile: Ambil teks dari elemen-elemen di dalam card
        trxId = card.querySelector(".trx-card-id")?.textContent.trim();
        waktu = card.querySelector(".trx-card-date")?.textContent.trim();
        items = card.querySelector(".trx-card-items")?.textContent.trim();
        payment = card.querySelector(".trx-card-payment")?.textContent.trim();
        nominal = card.querySelector(".trx-card-amount")?.textContent.trim();
        status = card.querySelector(".status-badge")?.textContent.trim();
      }

      // Suntik/Isi data yang didapat ke dalam elemen-elemen popup HTML
      const elId = document.getElementById("detail-trx-id");
      const elWaktu = document.getElementById("detail-trx-waktu");
      const elItems = document.getElementById("detail-trx-items");
      const elPayment = document.getElementById("detail-trx-payment");
      const elNominal = document.getElementById("detail-trx-nominal");
      const elStatus = document.getElementById("detail-trx-status");

      if (elId) elId.textContent = trxId || "-";
      if (elWaktu) elWaktu.textContent = waktu || "-";
      if (elItems) elItems.textContent = items || "-";
      if (elPayment) elPayment.textContent = payment || "-";
      if (elNominal) elNominal.textContent = nominal || "-";
      if (elStatus) elStatus.textContent = status || "-";

      // Panggil fungsi openPopup global yang bersumber dari popup.js
      if (typeof openPopup === "function") {
        openPopup("popup-detail-transaksi");
      }
    }
  });

  /* ============================================================
     3. EXPORT BUTTON PLACEHOLDER
     Menangani klik pada tombol export data
     ============================================================ */
  const btnExport = document.getElementById("btnExport");
  const closeExportBtn = document.getElementById('closeExportBtn');
  if (btnExport) {
    btnExport.addEventListener("click", function (e) {
      e.preventDefault();
      
      // Hitung menu yang sedang tampil di tabel untuk dioper ke popup export range count
      const visibleRows = document.querySelectorAll("#tabel-menu tbody tr:not([style*='display: none'])");
      const countEl = document.getElementById("exportRangeCount");

      if (countEl) countEl.textContent = `(${visibleRows.length} Penjualan)`;

      if (typeof window._openPopup === "function") {
        window._openPopup("popupExport");
      }
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