/* ============================================================
   penjualan.js — Logika halaman Detail Penjualan (Admin)
   Dipanggil SETELAH admin.js

   Cara kerja filter:
   - Tab status & tipe ada di DUA tempat: mobile + desktop
   - Klik tab mana saja → sync ke tab pasangannya + applyAllFilters()
   - State tersimpan di filterState = { status, tipe }
   - applyAllFilters() menggabungkan: search keyword + status + tipe

   Data dibaca dari data-* attribute di <tr> dan .trx-card:
     data-kode, data-waktu, data-tipe, data-payment,
     data-total, data-subtotal, data-pajak, data-status, data-items
   
   Format data-items:
     "QTYx NAMA|HARGA_SATUAN|CATATAN;;..."
============================================================ */

document.addEventListener("DOMContentLoaded", function () {

  /* ============================================================
     STATE FILTER GLOBAL
  ============================================================ */
  const filterState = {
    status: "semua",
    tipe:   "semua",
  };

  /* ============================================================
     1. TAB FILTER — STATUS & TIPE
        Pola identik dengan karyawan.js (data-jabatan → data-filter-type/value)
        
        Seluruh tombol .tab[data-filter-type] di halaman ini
        (ada 4 grup: status-mobile, tipe-mobile, status-desktop, tipe-desktop)
        dihandle oleh satu event listener.
  ============================================================ */
  document.querySelectorAll(".tab[data-filter-type]").forEach((btn) => {
    btn.addEventListener("click", function () {
      const type  = this.dataset.filterType;   // "status" | "tipe"
      const value = this.dataset.filterValue;

      // 1. Update state
      filterState[type] = value;

      // 2. Sync semua tab dengan type yang sama ke active/inactive
      //    (termasuk salinan mobile & desktop sekaligus)
      document.querySelectorAll(`.tab[data-filter-type="${type}"]`).forEach((t) => {
        t.classList.toggle("active", t.dataset.filterValue === value);
      });

      // 3. Terapkan filter
      applyAllFilters(currentKeyword());
    });
  });

  /* ============================================================
     2. SEARCH REAL-TIME
        Ada dua input: mobile (#searchInputMobile) & desktop (#searchInputDesktop)
        Keduanya sinkron satu sama lain.
  ============================================================ */
  const searchInputs = document.querySelectorAll(
    "#searchInputMobile, #searchInputDesktop, .search-wrapper input"
  );

  searchInputs.forEach((input) => {
    input.addEventListener("input", function () {
      const keyword = this.value.toLowerCase().trim();

      // Sync semua search input supaya konsisten
      searchInputs.forEach((other) => {
        if (other !== this) other.value = this.value;
      });

      applyAllFilters(keyword);
    });
  });

  /* ============================================================
     3. TOMBOL "LIHAT DETAIL" — Isi Popup Struk dari data-* Attribute
  ============================================================ */
  document.addEventListener("click", function (e) {
    const detailBtn = e.target.closest(".btn-detail-sales");
    if (!detailBtn) return;
    e.preventDefault();

    const source = detailBtn.closest("tr[data-id]") || detailBtn.closest(".trx-card[data-id]");
    if (!source) return;

    const kode     = source.dataset.kode     || "-";
    const waktu    = source.dataset.waktu    || "-";
    const tipe     = source.dataset.tipe     || "Dine In";
    const payment  = source.dataset.payment  || "-";
    const total    = parseInt(source.dataset.total    || "0", 10);
    const subtotal = parseInt(source.dataset.subtotal || "0", 10);
    const pajak    = parseInt(source.dataset.pajak    || "0", 10);
    const status   = source.dataset.status   || "-";
    const itemsRaw = source.dataset.items    || "";

    const set = (id, val) => {
      const el = document.getElementById(id);
      if (el) el.textContent = val;
    };

    set("struk-no-trx", "#" + kode);
    set("struk-waktu",  waktu);
    set("struk-tipe",   tipe);

    // Status badge warna
    const elStatus = document.getElementById("struk-status");
    if (elStatus) {
      const s = status.toLowerCase();
      elStatus.classList.remove("lunas", "gagal", "pending");
      if (["sukses", "success", "paid", "lunas", "completed"].includes(s)) {
        elStatus.textContent = "Lunas (" + payment + ")";
        elStatus.classList.add("lunas");
      } else if (["gagal", "failed", "cancelled", "rejected"].includes(s)) {
        elStatus.textContent = status + " — " + payment;
        elStatus.classList.add("gagal");
      } else {
        elStatus.textContent = status + " — " + payment;
        elStatus.classList.add("pending");
      }
    }

    // Render item pesanan
    const itemsList = document.getElementById("struk-items-list");
    if (itemsList) {
      itemsList.innerHTML = "";
      if (itemsRaw.trim()) {
        itemsRaw.split(";;").filter(Boolean).forEach((item) => {
          const parts    = item.split("|");
          const namaQty  = (parts[0] || "").trim();
          const hargaSat = parseInt(parts[1] || "0", 10);
          const catatan  = (parts[2] || "").trim();
          const qty      = (namaQty.match(/^(\d+)x\s*/i) || [, 1])[1];
          const total    = hargaSat * parseInt(qty, 10);

          const div = document.createElement("div");
          div.className = "struk-item";
          div.innerHTML = `
            <div class="struk-item-top">
              <div>
                <p class="struk-item-nama">${namaQty}</p>
                <p class="struk-item-satuan">@ Rp ${hargaSat.toLocaleString("id-ID")},-</p>
              </div>
              <span class="struk-item-harga">Rp ${total.toLocaleString("id-ID")},-</span>
            </div>
            ${catatan ? `
            <span class="struk-item-note">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                <rect x="4" y="2" width="16" height="20" rx="2" stroke="#92400e" stroke-width="1.8"/>
                <path d="M8 7H16M8 11H16M8 15H12" stroke="#92400e" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
              ${catatan}
            </span>` : ""}
          `;
          itemsList.appendChild(div);
        });
      } else {
        itemsList.innerHTML = `<p style="text-align:center;color:#94a3b8;font-size:13px;padding:16px 0;">Detail item tidak tersedia.</p>`;
      }
    }

    set("struk-subtotal", "Rp " + subtotal.toLocaleString("id-ID") + ",-");
    set("struk-pajak",    "Rp " + pajak.toLocaleString("id-ID") + ",-");
    set("struk-total",    "Rp " + total.toLocaleString("id-ID") + ",-");

    if (typeof window._openPopup === "function") {
      window._openPopup("popup-detail-transaksi");
    } else {
      document.getElementById("popup-detail-transaksi")?.classList.add("is-open");
    }
  });

  /* ============================================================
     4. CHECK-ALL CHECKBOX (DESKTOP)
  ============================================================ */
  const checkAll = document.getElementById("check-all");
  if (checkAll) {
    checkAll.addEventListener("change", function () {
      document.querySelectorAll("#tabel-penjualan tbody input[type='checkbox']")
        .forEach(cb => { cb.checked = this.checked; });
    });
  }

  /* ============================================================
     5. EXPORT POPUP
  ============================================================ */
  // Tangkap SEMUA tombol export (mobile id berbeda, desktop pakai data-open)
  document.querySelectorAll(
    "#btnExport, #btnExportMobile, [data-open='popupExport']"
  ).forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const visibleRows = document.querySelectorAll(
        "#tabel-penjualan tbody tr:not(.filter-hidden):not([style*='display: none'])"
      );
      const countEl = document.getElementById("exportRangeCount");
      if (countEl) countEl.textContent = `(${visibleRows.length} Penjualan)`;
      if (typeof window._openPopup === "function") window._openPopup("popupExport");
    });
  });

  document.getElementById("closeExportBtn")?.addEventListener("click", function (e) {
    e.preventDefault();
    if (typeof window._closePopup === "function") window._closePopup("popupExport");
  });

  document.querySelectorAll(".export-option").forEach((opt) => {
    opt.addEventListener("click", function () {
      document.querySelectorAll(".export-option").forEach((o) => o.classList.remove("selected"));
      this.classList.add("selected");
    });
  });

  document.getElementById("btnUnduhData")?.addEventListener("click", function (e) {
    e.preventDefault();
    const fmt     = document.querySelector(".export-option.selected")?.dataset.fmt ?? "xlsx";
    const rentang = document.querySelector('input[name="exportRange"]:checked')?.value ?? "current";
    if (typeof window._closePopup === "function") window._closePopup("popupExport");
    if (typeof window.showToast   === "function") {
      window.showToast(`Data (${rentang}) berhasil diunduh sebagai ${fmt.toUpperCase()} ✓`);
    }
  });

  /* ============================================================
     6. CORE FILTER ENGINE
        Gabungkan search + filterState.status + filterState.tipe
        Dipanggil dari: tab click, search input
  ============================================================ */

  /** Ambil keyword search saat ini dari input manapun yang aktif */
  function currentKeyword() {
    const inp = document.querySelector(
      "#searchInputDesktop, #searchInputMobile, .search-wrapper input"
    );
    return inp ? inp.value.toLowerCase().trim() : "";
  }

  /**
   * Normalisasi data-status ke kategori filter.
   * Cocokkan dengan data-filter-value di HTML.
   */
  function normalizeStatus(raw) {
    const s = (raw || "").toLowerCase().trim();
    if (["sukses", "success", "paid", "lunas", "completed"].includes(s)) return "paid";
    if (["gagal", "failed", "rejected"].includes(s))                      return "failed";
    if (["cancelled", "cancel", "batal"].includes(s))                    return "cancelled";
    if (s === "pending")                                                   return "pending";
    return s;
  }

  /**
   * Normalisasi data-tipe ke kategori filter.
   * Cocokkan dengan data-filter-value di HTML.
   */
  function normalizeTipe(raw) {
    const t = (raw || "").toLowerCase().trim().replace(/[\s\-]+/g, "_");
    if (["dine_in", "dinein", "dine"].includes(t))          return "dine_in";
    if (["take_away", "takeaway", "take"].includes(t))      return "take_away";
    return t;
  }

  /** Cek apakah satu row/card lolos semua kondisi filter aktif */
  function rowPasses(el, kw) {
    // 1. Keyword search
    if (kw && !el.textContent.toLowerCase().includes(kw)) return false;

    // 2. Filter status
    if (filterState.status !== "semua") {
      if (normalizeStatus(el.dataset.status || "") !== filterState.status) return false;
    }

    // 3. Filter tipe pesanan
    if (filterState.tipe !== "semua") {
      if (normalizeTipe(el.dataset.tipe || "") !== filterState.tipe) return false;
    }

    return true;
  }

  /**
   * Terapkan semua filter ke card mobile + baris tabel desktop.
   * Tampilkan empty state jika tidak ada hasil di tabel.
   */
  function applyAllFilters(kw) {
    let visibleCards = 0;
    let visibleRows  = 0;

    // Mobile cards
    document.querySelectorAll(".trx-card").forEach((card) => {
      const show = rowPasses(card, kw);
      card.classList.toggle("filter-hidden", !show);
      if (show) visibleCards++;
    });

    // Desktop table rows (skip baris kosong tanpa data-id)
    document.querySelectorAll("#tabel-penjualan tbody tr[data-id]").forEach((row) => {
      const show = rowPasses(row, kw);
      row.classList.toggle("filter-hidden", !show);
      if (show) visibleRows++;
    });

    // Empty state di tabel desktop
    showEmptyState(visibleRows === 0);
  }

  /** Inject / tampilkan / sembunyikan baris empty state di tabel desktop */
  function showEmptyState(shouldShow) {
    const tbody = document.querySelector("#tabel-penjualan tbody");
    if (!tbody) return;

    let emptyRow = tbody.querySelector("tr.filter-empty-state");

    if (shouldShow) {
      if (!emptyRow) {
        const cols = document.querySelectorAll("#tabel-penjualan thead th").length || 10;
        emptyRow = document.createElement("tr");
        emptyRow.className = "filter-empty-state";
        emptyRow.innerHTML = `
          <td colspan="${cols}">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                 style="display:block;margin:0 auto 10px;opacity:.35;">
              <path d="M3 6h18M3 12h18M3 18h18"
                    stroke="#475569" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Tidak ada transaksi yang cocok dengan filter aktif.
          </td>
        `;
        tbody.appendChild(emptyRow);
      }
      emptyRow.style.display = "";
    } else {
      if (emptyRow) emptyRow.style.display = "none";
    }
  }

  /* ============================================================
     7. INISIASI AWAL
  ============================================================ */
  applyAllFilters("");

});