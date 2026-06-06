/* ============================================================
   menu.js — Logika khusus halaman Manajemen Menu
   Dipanggil SETELAH admin.js (atau popup core utama Anda)
   Berisi:
   1. Tab filter kategori (All / Makanan / Minuman / Cemilan)
      → filter card mobile + baris tabel desktop
   2. Tombol hapus → isi nama ke popup konfirmasi, buka popup
   3. Search real-time
   4. Tombol tambah & edit → pre-fill data & open popup
   5. Toggle Status → update status ketersediaan instan di tabel
============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  /* ============================================================
     1. TAB FILTER KATEGORI
     Klik tab → update active state + filter card + filter tabel
  ============================================================ */
  document
    .querySelectorAll(".category-tabs .tab[data-kategori]")
    .forEach((btn) => {
      btn.addEventListener("click", function () {
        // Update active tab
        document
          .querySelectorAll(".category-tabs .tab[data-kategori]")
          .forEach((b) => {
            b.classList.remove("active");
          });
        this.classList.add("active");

        const kategori = this.dataset.kategori; // 'all' | 'makanan' | 'minuman' | 'cemilan'

        // Filter card list (mobile)
        document
          .querySelectorAll(".menu-card[data-kategori]")
          .forEach((card) => {
            if (kategori === "all") {
              card.style.display = "";
            } else {
              card.style.display =
                card.dataset.kategori === kategori ? "" : "none";
            }
          });

        // Filter baris tabel (desktop)
        document
          .querySelectorAll("#tabel-menu tbody tr[data-kategori]")
          .forEach((row) => {
            if (kategori === "all") {
              row.style.display = "";
            } else {
              row.style.display =
                row.dataset.kategori === kategori ? "" : "none";
            }
          });
      });
    });

  /* ============================================================
     2. TOMBOL HAPUS MENU
     Klik ikon hapus → pindahkan nama menu ke text popup, lalu buka popup
  ============================================================ */
  const tabelMenu = document.getElementById("tabel-menu");

  // Handler untuk Desktop (Tabel)
  tabelMenu?.addEventListener("click", function (e) {
    const btnHapus = e.target.closest(".btn-hapus-menu");
    if (btnHapus) {
      e.preventDefault();
      const namaMenu = btnHapus.dataset.nama;

      const elNama = document.getElementById("popup-hapus-nama-menu");
      if (elNama) elNama.textContent = namaMenu;

      window._openPopup?.("popup-hapus-menu");
    }
  });

  // Handler untuk Mobile (Card)
  document.querySelectorAll(".menu-card")?.forEach((card) => {
    card.addEventListener("click", function (e) {
      const btnHapus = e.target.closest(".btn-hapus-menu");
      if (btnHapus) {
        e.preventDefault();
        e.stopPropagation();
        const namaMenu = btnHapus.dataset.nama;

        const elNama = document.getElementById("popup-hapus-nama-menu");
        if (elNama) elNama.textContent = namaMenu;

        window._openPopup?.("popup-hapus-menu");
      }
    });
  });

  /* ============================================================
     3. SEARCH FILTER (REAL-TIME)
     Mencari kata kunci pada card mobile & baris tabel desktop
  ============================================================ */
  const searchInputs = document.querySelectorAll(".search-wrapper input");

  searchInputs.forEach((input) => {
    input.addEventListener("input", function () {
      const keyword = this.value.toLowerCase().trim();

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
    });
  });

  /* ============================================================
     4. TOMBOL EDIT MENU (PRE-FILL DATA POPUP)
     Klik tombol edit → pindahkan dataset menu ke form fields popup edit
  ============================================================ */
  //tambah MENU
  const btnTambahMenu = document.getElementById("btn-tambah-menu");
  if (btnTambahMenu) {
    btnTambahMenu.addEventListener("click", function (e) {
      e.preventDefault();
      
      // Reset input form tambah sebelum dibuka agar bersih
      document.getElementById("popup-tambah-nama") ? document.getElementById("popup-tambah-nama").value = "" : null;
      document.getElementById("popup-tambah-kategori") ? document.getElementById("popup-tambah-kategori").value = "" : null;
      document.getElementById("popup-tambah-stok") ? document.getElementById("popup-tambah-stok").value = "" : null;
      document.getElementById("popup-tambah-harga") ? document.getElementById("popup-tambah-harga").value = "" : null;
      
      const previewFotoTambah = document.getElementById('popup-tambah-preview');
      if (previewFotoTambah) previewFotoTambah.src = "";
      
      const uploadAreaTambah = document.getElementById('popup-tambah-upload-area');
      if (uploadAreaTambah) uploadAreaTambah.classList.remove('has-foto');

      // Panggil core popup untuk memunculkan popup tambah menu
      window._openPopup?.("popup-tambah-menu");
    });
  }

  // Handler untuk Desktop (Tabel)
  tabelMenu?.addEventListener("click", function (e) {
    const btnEdit = e.target.closest(".btn-edit-menu");
    if (btnEdit) {
      e.preventDefault();
      prefillEditMenu(btnEdit.dataset);
    }
  });

  // Handler untuk Mobile (Card)
  document.querySelectorAll(".menu-card")?.forEach((card) => {
    card.addEventListener("click", function (e) {
      const btnEdit = e.target.closest(".btn-edit-menu");
      if (btnEdit) {
        e.preventDefault();
        e.stopPropagation();
        prefillEditMenu(btnEdit.dataset);
      }
    });
  });

  // Fungsi pembantu untuk mapping data ke field popup edit
  function prefillEditMenu(dataset) {
    if (document.getElementById("popup-edit-id")) 
      document.getElementById("popup-edit-id").value = dataset.id || "";
    if (document.getElementById("popup-edit-nama")) 
      document.getElementById("popup-edit-nama").value = dataset.nama || "";
    if (document.getElementById("popup-edit-kategori")) 
      document.getElementById("popup-edit-kategori").value = dataset.kategori || "";
    if (document.getElementById("popup-edit-stok")) 
      document.getElementById("popup-edit-stok").value = dataset.stok || "";
    if (document.getElementById("popup-edit-harga")) 
      document.getElementById("popup-edit-harga").value = dataset.harga || "";

    // Set default preview foto menu (bisa diganti asset url database nanti)
    const popupEditFotoImg = document.getElementById("popup-edit-foto-img");
    if (popupEditFotoImg) {
      popupEditFotoImg.src = "https://i.pravatar.cc/300?img=5"; 
    }

    // Buka popup edit menu
    window._openPopup?.("popup-edit-menu");
  }

  // karyawan.js — ini yang bikin mobile bisa ke halaman tambah/edit
document.getElementById('btn-tambah-menu')?.addEventListener('click', function (e) {
  if (window.innerWidth <= 480) {
    window.location.href = '/admin/menu/tambah'; // ← mobile: navigasi
  } else {
    e.preventDefault();
    window._openPopup?.('popup-tambah-menu'); // ← desktop: popup
  }
});

// Edit juga sama:
if (window.innerWidth <= 480) {
  window.location.href = `/admin/menu/${dataMenu.dbId}/edit`; // ← mobile: navigasi
} else {
  prefillEditPopup(dataMenu);
  window._openPopup('popup-edit-menu'); // ← desktop: popup
} 

  /* ============================================================
     5. TOGGLE INTERAKTIF STATUS KETERSEDIAAN MENU (TAMBAHAN KHUSUS)
     Mengubah status aktif/tidak langsung dari baris tabel menu
  ============================================================ */
  tabelMenu?.addEventListener("change", function (e) {
    if (e.target.classList.contains("toggle-status-menu")) {
      const checkbox = e.target;
      const menuId = checkbox.dataset.id;
      const isTersedia = checkbox.checked; // true = Tersedia, false = Habis

      console.log(`Menu ${menuId} status diubah ke: ${isTersedia ? "Tersedia" : "Habis"}`);
      
      if (typeof window.showToast === "function") {
        window.showToast(`Status menu ${menuId} berhasil diperbarui ✓`);
      }
    }
  });

  /* ============================================================
     7. INTERAKSI POP-UP EXPORT DATA MENU
  ============================================================ */
  const btnExport = document.getElementById('btnExport');
  const popupExport = document.getElementById('popupExport');
  const closeExportBtn = document.getElementById('closeExportBtn');

  // A. Membuka Popup Export & Menghitung Otomatis Jumlah Menu yang Tampil
  if (btnExport) {
    btnExport.addEventListener('click', function (e) {
      e.preventDefault();
      
      // Hitung baris tabel yang saat ini sedang tidak disembunyikan (display != none)
      const visibleRows = document.querySelectorAll("#tabel-menu tbody tr:not([style*='display: none'])");
      const count = visibleRows.length;
      
      // Update info teks dinamis jumlah menu di dalam pilihan radio button rentang data
      const countEl = document.getElementById('exportRangeCount');
      if (countEl) countEl.textContent = `(${count} Menu)`;

      // Buka popup menggunakan function core framework Anda
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
      e.preventDefault();t
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

  /* ============================================================
     6. IMAGE UPLOAD & PREVIEW LOGIC
     Menangani pratinjau gambar saat memilih file gambar baru
  ============================================================ */
  /* Upload foto popup tambah */
  const popupTambahInputFoto = document.getElementById('popup-tambah-input-foto');
  const popupTambahUploadArea = document.getElementById('popup-tambah-upload-area');
  const popupTambahPreview    = document.getElementById('popup-tambah-preview');

  popupTambahUploadArea?.addEventListener('click', () => popupTambahInputFoto?.click());

  popupTambahInputFoto?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      if (popupTambahPreview) popupTambahPreview.src = e.target.result;
      popupTambahUploadArea.classList.add('has-foto');
    };
    reader.readAsDataURL(file);
  });

  /* Ganti foto popup edit */
  const popupEditInputFoto  = document.getElementById('popup-edit-input-foto');
  const popupEditFotoImg    = document.getElementById('popup-edit-foto-img');

  document.getElementById('popup-edit-btn-ganti-foto')?.addEventListener('click', function(e) {
    e.preventDefault();
    popupEditInputFoto?.click();
  });

  popupEditInputFoto?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { 
      if (popupEditFotoImg) popupEditFotoImg.src = e.target.result; 
    };
    reader.readAsDataURL(file);
  });

  /* Hapus foto popup edit */
  document.getElementById('popup-edit-btn-hapus-foto')?.addEventListener('click', function (e) {
    e.preventDefault();
    if (popupEditFotoImg) popupEditFotoImg.src = "https://i.pravatar.cc/300?img=5"; // generic placeholder
    if (popupEditInputFoto) popupEditInputFoto.value = ""; // clear file path
  });

  document.querySelectorAll('[data-close], .btn-batal').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      window._closePopup?.(); // Menutup pop-up yang sedang aktif
    });
  });
});