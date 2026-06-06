/* ============================================================
   menu.js — Logika khusus halaman Manajemen Menu
   Dipanggil SETELAH admin.js
   Berisi:
   1. Tab filter kategori (All / Makanan / Minuman / Cemilan)
      → filter card mobile + baris tabel desktop
   2. Tombol hapus → isi nama ke popup konfirmasi, buka popup
   3. Proses AJAX (Tambah, Edit, Hapus) ke Backend Laravel
   4. Search real-time
   5. Preview Foto
   6. Paginasi multi-device
============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  // Ambil CSRF Token dari meta tag head HTML Laravel
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  /* ============================================================
     1. TAB FILTER KATEGORI
     Klik tab → update active state + filter card + filter tabel
  ============================================================ */
  document.querySelectorAll(".category-tabs .tab[data-kategori]").forEach((btn) => {
    btn.addEventListener("click", function () {
      const kategoriTerpilih = this.dataset.kategori;

      document.querySelectorAll(".category-tabs .tab[data-kategori]").forEach((b) => {
        if (b.dataset.kategori === kategoriTerpilih) {
          b.classList.add("active");
        } else {
          b.classList.remove("active");
        }
      });

      // Filter card list (mobile)
      document.querySelectorAll(".menu-card[data-kategori]").forEach((card) => {
        const cardKategori = card.dataset.kategori;
        if (kategoriTerpilih === "all") {
          card.style.display = "";
        } else {
          card.style.display = cardKategori === kategoriTerpilih ? "" : "none";
        }
      });

      // Filter baris tabel (desktop)
      document.querySelectorAll("#tabel-menu tbody tr[data-kategori]").forEach((row) => {
        const rowKategori = row.dataset.kategori;
        if (kategoriTerpilih === "all") {
          row.style.display = "";
        } else {
          row.style.display = rowKategori === kategoriTerpilih ? "" : "none";
        }
      });

      currentPage = 1; // Reset ke halaman 1 setiap kali ganti kategori
      updateTampilanDanPaginasi();
    });
  });

  /* ============================================================
     2. TOMBOL HAPUS MENU
  ============================================================ */
  document.addEventListener("click", function (e) {
    const hapusBtn = e.target.closest(".btn-hapus-menu");
    if (!hapusBtn) return;

    e.preventDefault();
    const id   = hapusBtn.dataset.id;
    const nama = hapusBtn.dataset.nama || "menu ini";

    // Set data ke dalam elemen popup konfirmasi hapus
    const idEl   = document.getElementById("popup-hapus-db-id");
    const namaEl = document.getElementById("popup-hapus-nama-menu");

    if (idEl)   idEl.value       = id;
    if (namaEl) namaEl.textContent = nama;

    // Buka popup konfirmasi hapus via shared popup system
    if (typeof window._openPopup === "function") {
      window._openPopup("popup-hapus-menu");
    } else {
      document.getElementById("popup-hapus-menu")?.classList.add("is-open");
    }
  });

  /* ============================================================
     3. PROSES AJAX — ABSOLUTE BACKEND CONNECTORS
  ============================================================ */

  // A. PROSES AJAX: SIMPAN (TAMBAH MENU)
  document.getElementById("popup-btn-tambah-simpan")?.addEventListener("click", function (e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append("name",        document.getElementById("popup-tambah-nama")?.value      || "");
    formData.append("category_id", document.getElementById("popup-tambah-kategori")?.value  || "");
    formData.append("stock",       document.getElementById("popup-tambah-stok")?.value      || "");
    formData.append("price",       document.getElementById("popup-tambah-harga")?.value     || "");

    // Ambil status tersedia (checkbox)
    const tersediaInput = document.getElementById("popup-tambah-tersedia");
    formData.append("is_available", tersediaInput && tersediaInput.checked ? "1" : "0");

    // Lampirkan foto jika ada
    const fotoInput = document.getElementById("popup-tambah-input-foto");
    if (fotoInput && fotoInput.files[0]) {
      formData.append("image", fotoInput.files[0]);
    }

    const currentToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch("/admin/menu/store", {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": currentToken,
        "Accept": "application/json"
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        alert("Gagal menambahkan menu: " + (data.message || "Periksa kembali inputan Anda."));
      }
    })
    .catch(err => console.error("Error:", err));
  });

  // B. PROSES AJAX: UPDATE (EDIT MENU)
  document.getElementById("popup-btn-edit-simpan")?.addEventListener("click", function (e) {
    e.preventDefault();
    const dbId = document.getElementById("popup-edit-id")?.value;

    const formData = new FormData();
    formData.append("name",        document.getElementById("popup-edit-nama")?.value     || "");
    formData.append("category_id", document.getElementById("popup-edit-kategori")?.value || "");
    formData.append("stock",       document.getElementById("popup-edit-stok")?.value     || "");
    formData.append("price",       document.getElementById("popup-edit-harga")?.value    || "");

    // Method spoofing Laravel untuk PUT
    formData.append("_method", "PUT");

    // Status tersedia (checkbox)
    const tersediaEdit = document.getElementById("popup-edit-tersedia");
    formData.append("is_available", tersediaEdit && tersediaEdit.checked ? "1" : "0");

    // Lampirkan foto baru jika diganti
    const fotoInput = document.getElementById("popup-edit-input-foto");
    if (fotoInput && fotoInput.files[0]) {
      formData.append("image", fotoInput.files[0]);
    }

    fetch(`/admin/menu/${dbId}/update`, {
      method: "POST", // Tetap POST, dibantu _method PUT di atas
      headers: {
        "X-CSRF-TOKEN": csrfToken,
        "Accept": "application/json"
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        alert("Gagal memperbarui menu: " + (data.message || "Periksa kembali inputan Anda."));
      }
    })
    .catch(err => console.error("Error:", err));
  });

  // C. KONFIRMASI HAPUS — tombol "Ya, Hapus"
  document.getElementById("btn-confirm-hapus")?.addEventListener("click", function () {
    const dbId = document.getElementById("popup-hapus-db-id")?.value;
    const currentToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/admin/menu/${dbId}`, {
      method: "DELETE",
      headers: {
        "X-CSRF-TOKEN": currentToken,
        "Content-Type": "application/json",
        "Accept": "application/json"
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        alert("Gagal menghapus menu.");
      }
      window._closePopup?.();
    })
    .catch(err => console.error("Error:", err));
  });

  /* ============================================================
     4. SEARCH REAL-TIME
     Filter card mobile + baris tabel desktop bersamaan
  ============================================================ */
  const searchInputs = document.querySelectorAll(".search-wrapper input");

  searchInputs.forEach((input) => {
    input.addEventListener("input", function () {
      const keyword = this.value.toLowerCase().trim();

      // Sync keyword ke semua search input (mobile + desktop)
      searchInputs.forEach((other) => {
        if (other !== this) other.value = this.value;
      });

      // Filter card mobile
      document.querySelectorAll(".menu-card").forEach((card) => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(keyword) ? "" : "none";
      });

      // Filter baris tabel desktop
      document.querySelectorAll("#tabel-menu tbody tr").forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? "" : "none";
      });

      currentPage = 1; // Reset ke halaman 1 setiap kali mengetik pencarian baru
      updateTampilanDanPaginasi();
    });
  });

  /* ============================================================
     5. TOMBOL TAMBAH & EDIT MENU
  ============================================================ */
  document.getElementById('btn-tambah-menu')?.addEventListener('click', function (e) {
    if (window.innerWidth <= 480) {
      // Mobile: navigasi ke halaman terpisah
      window.location.href = '/admin/menu/tambah';
    } else {
      // Desktop: buka popup
      e.preventDefault();

      // Reset semua field form tambah sebelum dibuka
      document.getElementById("popup-tambah-nama")?.value      != null && (document.getElementById("popup-tambah-nama").value     = "");
      document.getElementById("popup-tambah-kategori")?.value  != null && (document.getElementById("popup-tambah-kategori").value  = "");
      document.getElementById("popup-tambah-stok")?.value      != null && (document.getElementById("popup-tambah-stok").value      = "");
      document.getElementById("popup-tambah-harga")?.value     != null && (document.getElementById("popup-tambah-harga").value     = "");

      const previewFoto = document.getElementById('popup-tambah-preview');
      if (previewFoto) previewFoto.src = "";

      const uploadArea = document.getElementById('popup-tambah-upload-area');
      if (uploadArea) uploadArea.classList.remove('has-foto');

      window._openPopup?.('popup-tambah-menu');
    }
  });

  document.addEventListener('click', function (e) {
    const editBtn = e.target.closest('.btn-edit-menu');
    if (!editBtn) return;

    e.preventDefault();

    const dataMenu = {
      dbId     : editBtn.dataset.id,
      nama     : editBtn.dataset.nama,
      kategori : editBtn.dataset.kategori,
      stok     : editBtn.dataset.stok,
      harga    : editBtn.dataset.harga,
      foto     : editBtn.dataset.foto,
      tersedia : editBtn.dataset.tersedia
    };

    if (window.innerWidth <= 480) {
      // Mobile: navigasi ke halaman edit dengan ID
      window.location.href = `/admin/menu/${dataMenu.dbId}/edit`;
    } else {
      // Desktop: pre-fill popup edit lalu buka
      prefillEditPopup(dataMenu);
      if (typeof window._openPopup === "function") {
        window._openPopup('popup-edit-menu');
      } else {
        document.getElementById('popup-edit-menu')?.classList.add('active');
      }
    }
  });

  // Isi form di popup edit menu (desktop only)
  function prefillEditPopup(dataMenu) {
    const set = (id, val) => {
      const el = document.getElementById(id);
      if (el) el.value = val || '';
    };

    set('popup-edit-id',       dataMenu.dbId);
    set('popup-edit-nama',     dataMenu.nama);
    set('popup-edit-stok',     dataMenu.stok);
    set('popup-edit-harga',    dataMenu.harga);

    // Select kategori — cocokkan value
    const selectEl = document.getElementById('popup-edit-kategori');
    if (selectEl && dataMenu.kategori) {
      const opt = [...selectEl.options].find(
        o => o.value.toLowerCase() === dataMenu.kategori.toLowerCase()
      );
      if (opt) opt.selected = true;
    }

    // Toggle tersedia
    const toggleTersedia = document.getElementById('popup-edit-tersedia');
    if (toggleTersedia) {
      toggleTersedia.checked = (dataMenu.tersedia == 1 || dataMenu.tersedia === "true");
    }

    // Set foto eksisting atau placeholder
    const imgEl = document.getElementById('popup-edit-foto-img');
    if (imgEl) {
      imgEl.src = dataMenu.foto ? `/storage/${dataMenu.foto}` : 'https://via.placeholder.com/300x200';
    }
  }

  /* ============================================================
     6. HANDLER IMAGE PREVIEW & ACTIONS
  ============================================================ */
  /* Upload foto popup tambah */
  const popupTambahInputFoto  = document.getElementById('popup-tambah-input-foto');
  const popupTambahUploadArea = document.getElementById('popup-tambah-upload-area');
  const popupTambahPreview    = document.getElementById('popup-tambah-preview');

  popupTambahUploadArea?.addEventListener('click', () => popupTambahInputFoto?.click());

  popupTambahInputFoto?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      if (popupTambahPreview) {
        popupTambahPreview.src = e.target.result;
        popupTambahUploadArea.classList.add('has-foto');
      }
    };
    reader.readAsDataURL(file);
  });

  /* Ganti foto popup edit */
  const popupEditInputFoto = document.getElementById('popup-edit-input-foto');
  const popupEditFotoImg   = document.getElementById('popup-edit-foto-img');

  document.getElementById('popup-edit-btn-ganti-foto')?.addEventListener('click', () => {
    popupEditInputFoto?.click();
  });

  popupEditInputFoto?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { if (popupEditFotoImg) popupEditFotoImg.src = e.target.result; };
    reader.readAsDataURL(file);
  });

  /* Hapus foto popup edit */
  document.getElementById('popup-edit-btn-hapus-foto')?.addEventListener('click', () => {
    if (popupEditFotoImg)   popupEditFotoImg.src  = 'https://via.placeholder.com/300x200';
    if (popupEditInputFoto) popupEditInputFoto.value = '';
  });

  /* ============================================================
     7. TOGGLE STATUS KETERSEDIAAN MENU (dari tabel desktop)
        Toggle langsung kirim AJAX ke backend untuk update status
  ============================================================ */
  document.getElementById("tabel-menu")?.addEventListener("change", function (e) {
    if (!e.target.classList.contains("toggle-status-menu")) return;

    const checkbox   = e.target;
    const menuId     = checkbox.dataset.id;
    const isTersedia = checkbox.checked ? 1 : 0;
    const currentToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/admin/menu/${menuId}/toggle-status`, {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": currentToken,
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({ is_available: isTersedia })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        if (typeof window.showToast === "function") {
          window.showToast(`Status menu berhasil diperbarui ✓`);
        }
      } else {
        // Kembalikan posisi toggle jika gagal
        checkbox.checked = !checkbox.checked;
        alert("Gagal memperbarui status menu.");
      }
    })
    .catch(err => {
      console.error("Error:", err);
      checkbox.checked = !checkbox.checked;
    });
  });

  // ============================================================
  // FUNGSI UTAMA: FILTER DAN PAGINASI MULTI-DEVICE
  // ============================================================
  let currentPage = 1;
  const itemsPerPage = 8;

  function updateTampilanDanPaginasi() {
    // 1. Ambil kriteria filter aktif (Tab + Search Keyword)
    const tabAktif = document.querySelector(".category-tabs .tab.active");
    const filterKategori = tabAktif ? tabAktif.dataset.kategori : "all";

    const searchInput = document.querySelector(".search-wrapper input");
    const keyword = searchInput ? searchInput.value.toLowerCase().trim() : "";

    // ------------------------------------------------------------
    // A. PROSES UNTUK DESKTOP (TABEL TR)
    // ------------------------------------------------------------
    const rows = document.querySelectorAll("#tabel-menu tbody tr");
    let matchedRows = [];

    rows.forEach((row) => {
      const rowKategori = row.dataset.kategori;
      const text        = row.textContent.toLowerCase();

      const cocokKategori = (filterKategori === "all" || rowKategori === filterKategori);
      const cocokSearch   = text.includes(keyword);

      if (cocokKategori && cocokSearch) {
        matchedRows.push(row);
      } else {
        row.style.display = "none";
      }
    });

    // Hitung total halaman desktop
    const totalRows      = matchedRows.length;
    const totalPagesRows = Math.ceil(totalRows / itemsPerPage) || 1;

    if (currentPage > totalPagesRows) currentPage = totalPagesRows;

    const startIdx = (currentPage - 1) * itemsPerPage;
    const endIdx   = startIdx + itemsPerPage;

    matchedRows.forEach((row, index) => {
      row.style.display = (index >= startIdx && index < endIdx) ? "" : "none";
    });

    // Update Info & Tombol Paginasi Desktop
    const infoDesktop = document.querySelector(".content-footer .data-info");
    if (infoDesktop) {
      const displayStart = totalRows === 0 ? 0 : startIdx + 1;
      const displayEnd   = Math.min(endIdx, totalRows);
      infoDesktop.textContent = `Menampilkan ${displayStart} sampai ${displayEnd} dari ${totalRows} menu`;
    }
    renderPaginationButtons(document.querySelector(".content-footer .pagination"), totalPagesRows);

    // ------------------------------------------------------------
    // B. PROSES UNTUK MOBILE (CARD LIST)
    // ------------------------------------------------------------
    const cards = document.querySelectorAll(".menu-card");
    let matchedCards = [];

    cards.forEach((card) => {
      const cardKategori = card.dataset.kategori;
      const text         = card.textContent.toLowerCase();

      const cocokKategori = (filterKategori === "all" || cardKategori === filterKategori);
      const cocokSearch   = text.includes(keyword);

      if (cocokKategori && cocokSearch) {
        matchedCards.push(card);
      } else {
        card.style.display = "none";
      }
    });

    // Hitung total halaman mobile
    const totalCards      = matchedCards.length;
    const totalPagesCards = Math.ceil(totalCards / itemsPerPage) || 1;

    matchedCards.forEach((card, index) => {
      card.style.display = (index >= startIdx && index < endIdx) ? "" : "none";
    });

    // Update Info & Tombol Paginasi Mobile
    const infoMobile = document.querySelector(".content-footer-mobile .data-info");
    if (infoMobile) {
      const displayStart = totalCards === 0 ? 0 : startIdx + 1;
      const displayEnd   = Math.min(endIdx, totalCards);
      infoMobile.textContent = `Menampilkan ${displayStart} sampai ${displayEnd} dari ${totalCards} menu`;
    }
    renderPaginationButtons(document.querySelector(".content-footer-mobile .pagination"), totalPagesCards);
  }

  // ============================================================
  // FUNGSI GENERATOR TOMBOL PAGINASI DINAMIS
  // ============================================================
  function renderPaginationButtons(container, totalPages) {
    if (!container) return;
    container.innerHTML = "";

    // 1. Tombol Sebelumnya
    const prevBtn = document.createElement("button");
    prevBtn.className = `page-link ${currentPage === 1 ? "disabled" : ""}`;
    prevBtn.textContent = "Sebelumnya";
    prevBtn.disabled = currentPage === 1;
    prevBtn.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        updateTampilanDanPaginasi();
      }
    });
    container.appendChild(prevBtn);

    // 2. Angka-angka Halaman
    for (let i = 1; i <= totalPages; i++) {
      const pageBtn = document.createElement("button");
      pageBtn.className = `page-number ${currentPage === i ? "active" : ""}`;
      pageBtn.textContent = i;
      pageBtn.addEventListener("click", () => {
        currentPage = i;
        updateTampilanDanPaginasi();
      });
      container.appendChild(pageBtn);
    }

    // 3. Tombol Selanjutnya
    const nextBtn = document.createElement("button");
    nextBtn.className = `page-link ${currentPage === totalPages ? "disabled" : ""}`;
    nextBtn.textContent = "Selanjutnya";
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.addEventListener("click", () => {
      if (currentPage < totalPages) {
        currentPage++;
        updateTampilanDanPaginasi();
      }
    });
    container.appendChild(nextBtn);
  }

  // Jalankan fungsi pertama kali saat halaman berhasil dimuat
  updateTampilanDanPaginasi();

  /* ============================================================
     8. INTERAKSI POP-UP EXPORT DATA MENU
  ============================================================ */
  const btnExport    = document.getElementById('btnExport');
  const closeExportBtn = document.getElementById('closeExportBtn');

  // A. Membuka Popup Export & Menghitung Otomatis Jumlah Menu yang Tampil
  if (btnExport) {
    btnExport.addEventListener('click', function (e) {
      e.preventDefault();

      // Hitung baris tabel yang saat ini tidak disembunyikan
      const visibleRows = document.querySelectorAll("#tabel-menu tbody tr:not([style*='display: none'])");
      const count = visibleRows.length;

      const countEl = document.getElementById('exportRangeCount');
      if (countEl) countEl.textContent = `(${count} Menu)`;

      if (typeof window._openPopup === "function") {
        window._openPopup?.('popupExport');
      } else if (typeof window.triggerGlobalOpen === "function") {
        window.triggerGlobalOpen?.('popupExport');
      }
    });
  }

  // B. Menutup Popup Export via Tombol Silang
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

      const selectedOpt = document.querySelector('.export-option.selected');
      const fmtType     = selectedOpt ? selectedOpt.dataset.fmt : 'xlsx';

      const rentangOpt  = document.querySelector('input[name="exportRange"]:checked');
      const rentangNilai = rentangOpt ? rentangOpt.value : 'current';

      if (typeof window._closePopup === "function") {
        window._closePopup?.('popupExport');
      } else if (typeof window.triggerGlobalClose === "function") {
        window.triggerGlobalClose?.('popupExport');
      }

      if (typeof window.showToast === "function") {
        window.showToast(`Data (${rentangNilai}) berhasil diunduh sebagai ${fmtType.toUpperCase()} ✓`);
      }
    });
  }

  /* ============================================================
     CLOSE POPUP — tombol batal / data-close
  ============================================================ */
  document.querySelectorAll('[data-close], .btn-batal').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      window._closePopup?.();
    });
  });
});