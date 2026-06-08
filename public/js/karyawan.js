/* ============================================================
   karyawan.js — Logika khusus halaman Manajemen Karyawan
   Dipanggil SETELAH admin.js
   Berisi:
   1. Tab filter jabatan (All / Admin / Kasir / Koki)
      → filter card mobile + baris tabel desktop
   2. Tombol hapus → isi nama ke popup konfirmasi, buka popup
   3. Proses AJAX (Tambah, Edit, Hapus) ke Backend Laravel
   4. Search real-time
   5. Preview Foto
============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  // Ambil CSRF Token dari meta tag head HTML Laravel
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  /* ============================================================
     1. TAB FILTER JABATANa
     Klik tab → update active state + filter card + filter tabel
  ============================================================ */

  document.querySelectorAll(".category-tabs .tab[data-jabatan]").forEach((btn) => {
    btn.addEventListener("click", function () {
      const jabatanTerpilih = this.dataset.jabatan;
      const targetString = jabatanTerpilih;

      document.querySelectorAll(".category-tabs .tab[data-jabatan]").forEach((b) => {
        if (b.dataset.jabatan === jabatanTerpilih) {
            b.classList.add("active");
        } else {
            b.classList.remove("active");
        }
      });

      // Filter card list (mobile)
      document
        .querySelectorAll(".karyawan-card[data-jabatan]")
        .forEach((card) => {
          const cardJabatan = card.dataset.jabatan;
          if (jabatanTerpilih === "all") {
            card.style.display = "";
          } else {
            card.style.display = cardJabatan === targetString ? "" : "none";
          }
        });

      // Filter baris tabel (desktop)
      // Kolom jabatan = kolom ke-4 (index 3, setelah foto, nama, id)
      document.querySelectorAll("#tabel-karyawan tbody tr").forEach((row) => {
        const rowJabatan = row.dataset.jabatan;
        if (jabatanTerpilih === "all") {
          row.style.display = "";
        } else {
          row.style.display = rowJabatan === targetString ? "" : "none";
        }
      });
      currentPage = 1; // Reset ke halaman 1 setiap kali ganti kategori
      updateTampilanDanPaginasi();
    });
  });

  /* ============================================================
     2. TOMBOL HAPUS KARYAWAN
  ============================================================ */
  document.addEventListener("click", function (e) {
    const hapusBtn = e.target.closest(".btn-hapus-karyawan");
    if (!hapusBtn) return;

    e.preventDefault();
    const id = hapusBtn.dataset.id;
    const nama = hapusBtn.dataset.nama || "karyawan ini";

    // Set data ke dalam elemen popup konfirmasi hapus
    const idEl = document.getElementById("popup-hapus-db-id");
    const namaEl = document.getElementById("hapus-nama-karyawan");

    if (idEl) idEl.value = id;
    if (namaEl) namaEl.textContent = nama;

    // Buka popup konfirmasi hapus via shared popup system
    if (typeof window._openPopup === "function") {
        window._openPopup("popup-hapus-karyawan");
    } else {
        document.getElementById("popup-hapus-karyawan")?.classList.add("is-open");
    }
  });

  /* ============================================================
     3. PROSES AJAX — ABSOLUTE BACKEND CONNECTORS
  ============================================================ */
  // A. PROSES AJAX: SIMPAN (TAMBAH KARYAWAN)
  document.getElementById("popup-btn-tambah-simpan")?.addEventListener("click", function (e) {
    e.preventDefault();

    // Buat FormData untuk menangani file upload upload foto
    const formData = new FormData();
    formData.append("nama", document.getElementById("popup-tambah-nama").value);
    formData.append("jabatan", document.getElementById("popup-tambah-jabatan").value);
    formData.append("telp", document.getElementById("popup-tambah-telp").value);
    formData.append("email", document.getElementById("popup-tambah-email").value);
    formData.append("alamat", document.getElementById("popup-tambah-alamat").value);

    // Tambahkan status akses login (1 jika dicentang, 0 jika tidak)
    const aksesInput = document.getElementById("popup-tambah-akses");
    if (aksesInput && aksesInput.checked) {
        formData.append("is_active", "1");
    } else {
        // Mengirimkan string "0". 
        formData.append("is_active", "0");
    }    

    const fotoInput = document.getElementById("popup-tambah-input-foto");
    if (fotoInput && fotoInput.files[0]) {
        formData.append("photo", fotoInput.files[0]);
    }

    const currentToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    // Kirim data ke endpoint Laravel
    fetch("/admin/karyawan/store", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": currentToken,
            "Accept": "application/json"
        },
        body: formData
    })
    .then(res => res.json())
    .then(dataKaryawan => {
        if (dataKaryawan.success) {
            window.location.reload(); // Reload halaman jika berhasil untk sinkronisasi data baru
        } else {
            alert("Gagal menambahkan karyawan: " + (dataKaryawan.message || "Periksa kembali inputan Anda."));
        }
    })
    .catch(err => console.error("Error:", err));
  });

  // B. PROSES AJAX: UPDATE (EDIT KARYAWAN)
  document.getElementById("popup-btn-edit-simpan")?.addEventListener("click", function (e) {
    e.preventDefault();
    const dbId = document.getElementById("popup-edit-db-id").value;

    const formData = new FormData();
    formData.append("nama", document.getElementById("popup-edit-nama").value);
    formData.append("jabatan", document.getElementById("popup-edit-jabatan").value);
    formData.append("telp", document.getElementById("popup-edit-telp").value);
    formData.append("email", document.getElementById("popup-edit-email").value);
    formData.append("alamat", document.getElementById("popup-edit-alamat").value);
    
    // Karena HTML Form data tidak support PUT secara langsung saat upload file, gunakan Method Spoofing Laravel
    formData.append("_method", "PUT");

    // Tambahkan status akses login hasil edit
    const statusAksesEdit = document.getElementById('popup-edit-akses').checked ? 1 : 0;
    formData.append('is_active', statusAksesEdit);

    const fotoInput = document.getElementById("popup-edit-input-foto");
    if (fotoInput && fotoInput.files[0]) {
        formData.append("photo", fotoInput.files[0]);
    }

    fetch(`/admin/karyawan/${dbId}/update`, {
        method: "POST", // Tetap POST, dibantu _method PUT di atas
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json"
        },
        body: formData
    })
    .then(res => res.json())
    .then(dataKaryawan => {
        if (dataKaryawan.success) {
            window.location.reload();
        } else {
            alert("Gagal memperbarui data: " + (dataKaryawan.message || "Periksa kembali inputan Anda."));
        }
    })
    .catch(err => console.error("Error:", err));
  });

  /*  3. KONFIRMASI HAPUS — tombol "Ya, Hapus" */
  document.getElementById("btn-confirm-hapus")?.addEventListener("click", function () {
    const dbId = document.getElementById("popup-hapus-db-id").value;
    const currentToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch(`/admin/karyawan/${dbId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": currentToken,
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(dataKaryawan => {
        if (dataKaryawan.success) {
            window.location.reload();
        } else {
            alert("Gagal menghapus karyawan.");
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
      document.querySelectorAll(".karyawan-card").forEach((card) => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(keyword) ? "" : "none";
      });

      // Filter baris tabel desktop
      document.querySelectorAll("#tabel-karyawan tbody tr").forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? "" : "none";
      });
      currentPage = 1; // Reset ke halaman 1 setiap kali mengetik pencarian baru
      updateTampilanDanPaginasi();
    });
  });

  /* ============================================================
     5. TOMBOL TAMBAH & EDIT KARYAWAN
  ============================================================ */ 
  document.getElementById('btn-tambah-karyawan')?.addEventListener('click', function (e) {
    if (window.innerWidth <= 480) {
      // Mobile: navigasi ke halaman terpisah
      window.location.href = '/admin/karyawan/tambah';
    } else {
      // Desktop: buka popup
      e.preventDefault();
      window._openPopup?.('popup-tambah-karyawan');
    }
  });

  document.addEventListener('click', function (e) {
    const editBtn = e.target.closest('.btn-edit-karyawan');
    if (!editBtn) return;
    
    e.preventDefault();

    const dataKaryawan = {
      dbId    : editBtn.dataset.id,           // membaca data-id
      id      : editBtn.dataset.employeeId,   // membaca data-employee-id (Otomatis CamelCase di JS)
      nama    : editBtn.dataset.nama,         // membaca data-nama
      jabatan : editBtn.dataset.jabatan,      // membaca data-jabatan
      telp    : editBtn.dataset.telp,         // membaca data-telp
      email   : editBtn.dataset.email,        // membaca data-email
      alamat  : editBtn.dataset.alamat,       // membaca data-alamat
      photo   : editBtn.dataset.photo,
      status  : editBtn.dataset.status
    };

    if (window.innerWidth <= 480) {
      // Mobile: navigasi ke halaman edit dengan query param
      window.location.href = `/admin/karyawan/${dataKaryawan.dbId}/edit`;
    } else {
      // Desktop: pre-fill popup edit lalu buka
      e.preventDefault();
      prefillEditPopup(dataKaryawan);
      if (typeof window._openPopup === "function") {
          window._openPopup('popup-edit-karyawan');
      } else {
          document.getElementById('popup-edit-karyawan')?.classList.add('active');
      }
    }
  });

  /*  Isi form di popup edit karyawan (desktop only).   */
  function prefillEditPopup(dataKaryawan) {
    const set = (id, val) => {
      const el = document.getElementById(id);
      if (el) el.value = val || '';
    };

    set('popup-edit-db-id',     dataKaryawan.dbId);
    set('popup-edit-id',     dataKaryawan.id);
    set('popup-edit-nama',   dataKaryawan.nama);
    set('popup-edit-telp',   dataKaryawan.telp);
    set('popup-edit-email',  dataKaryawan.email);
    set('popup-edit-alamat', dataKaryawan.alamat);

    const toggleAkses = document.getElementById('popup-edit-akses'); 
    if (toggleAkses) {
        // Jika dataKaryawan.status bernilai 1 maka otomatis tercentang (aktif), jika 0 maka kosong (nonaktif)
        toggleAkses.checked = (dataKaryawan.status == 1); 
    }

    // Set src gambar eksisting atau default placeholder jika tidak ada foto
    const imgEl = document.getElementById('popup-edit-foto-img');
    if (imgEl) {
        imgEl.src = dataKaryawan.photo ? `/storage/${dataKaryawan.photo}` : 'https://via.placeholder.com/150';
    }
    // Select jabatan
    const selectEl = document.getElementById('popup-edit-jabatan');
    if (selectEl && dataKaryawan.jabatan) {
      const opt = [...selectEl.options].find(
        o => o.value.toLowerCase() === dataKaryawan.jabatan.toLowerCase()
      );
      if (opt) opt.selected = true;
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
      if(popupTambahPreview) {
          popupTambahPreview.src = e.target.result;
          popupTambahUploadArea.classList.add('has-foto');
      }
    };
    reader.readAsDataURL(file);
  });

/* Ganti foto popup edit */
  const popupEditInputFoto  = document.getElementById('popup-edit-input-foto');
  const popupEditFotoImg    = document.getElementById('popup-edit-foto-img');

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
    if (popupEditFotoImg) popupEditFotoImg.src = 'https://via.placeholder.com/150';
    if (popupEditInputFoto) popupEditInputFoto.value = '';
  });

  // ============================================================
  // FUNGSI UTAMA: FILTER DAN PAGINASI MULTI-DEVICE
  // ============================================================
  let currentPage = 1;
  const itemsPerPage = 8;

  function updateTampilanDanPaginasi() {
    // 1. Ambil kriteria filter aktif (Tab + Search Keyword)
    const tabAktif = document.querySelector(".category-tabs .tab.active");
    const filterJabatan = tabAktif ? tabAktif.dataset.jabatan : "all";
    
    const searchInput = document.querySelector(".search-wrapper input");
    const keyword = searchInput ? searchInput.value.toLowerCase().trim() : "";

    // ------------------------------------------------------------
    // A. PROSES UNTUK DESKTOP (TABEL TR)
    // ------------------------------------------------------------
    const rows = document.querySelectorAll("#tabel-karyawan tbody tr");
    let matchedRows = [];

    // Filter tahap awal: Kumpulkan data yang lolos pencarian & tab
    rows.forEach((row) => {
      const rowJabatan = row.dataset.jabatan;
      const text = row.textContent.toLowerCase();

      const cocokJabatan = (filterJabatan === "all" || rowJabatan === filterJabatan);
      const cocokSearch = text.includes(keyword);

      if (cocokJabatan && cocokSearch) {
        matchedRows.push(row);
      } else {
        row.style.display = "none"; // Sembunyikan langsung jika tidak lolos kriteria
      }
    });
    // Hitung total halaman desktop
    const totalRows = matchedRows.length;
    const totalPagesRows = Math.ceil(totalRows / itemsPerPage) || 1;
    
    // Cegah error index jika halaman aktif melampaui total halaman baru setelah difilter
    if (currentPage > totalPagesRows) currentPage = totalPagesRows;

    const startIdx = (currentPage - 1) * itemsPerPage;
    const endIdx = startIdx + itemsPerPage;

    // Tampilkan data desktop hanya yang masuk range halaman aktif
    matchedRows.forEach((row, index) => {
      if (index >= startIdx && index < endIdx) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });

    // Update Info & Tombol Paginasi Desktop
    const infoDesktop = document.querySelector(".content-footer .data-info");
    if (infoDesktop) {
      const displayStart = totalRows === 0 ? 0 : startIdx + 1;
      const displayEnd = Math.min(endIdx, totalRows);
      infoDesktop.textContent = `Menampilkan ${displayStart} sampai ${displayEnd} dari ${totalRows} karyawan`;
    }
    renderPaginationButtons(document.querySelector(".content-footer .pagination"), totalPagesRows);

    // ------------------------------------------------------------
    // B. PROSES UNTUK MOBILE (CARD LIST)
    // ------------------------------------------------------------
    const cards = document.querySelectorAll(".karyawan-card");
    let matchedCards = [];

    // Filter tahap awal mobile card
    cards.forEach((card) => {
      const cardJabatan = card.dataset.jabatan;
      const text = card.textContent.toLowerCase();

      const cocokJabatan = (filterJabatan === "all" || cardJabatan === filterJabatan);
      const cocokSearch = text.includes(keyword);

      if (cocokJabatan && cocokSearch) {
        matchedCards.push(card);
      } else {
        card.style.display = "none";
      }
    });

    // Hitung total halaman mobile
    const totalCards = matchedCards.length;
    const totalPagesCards = Math.ceil(totalCards / itemsPerPage) || 1;

    // Tampilkan data mobile card sesuai range halaman aktif
    matchedCards.forEach((card, index) => {
      if (index >= startIdx && index < endIdx) {
        card.style.display = "";
      } else {
        card.style.display = "none";
      }
    });

    // Update Info & Tombol Paginasi Mobile
    const infoMobile = document.querySelector(".content-footer-mobile .data-info");
    if (infoMobile) {
      const displayStart = totalCards === 0 ? 0 : startIdx + 1;
      const displayEnd = Math.min(endIdx, totalCards);
      infoMobile.textContent = `Menampilkan ${displayStart} sampai ${displayEnd} dari ${totalCards} karyawan`;
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
      const visibleRows = document.querySelectorAll("#tabel-karyawan tbody tr:not([style*='display: none'])");
      const count = visibleRows.length;
      
      // Update info teks dinamis jumlah menu di dalam pilihan radio button rentang data
      const countEl = document.getElementById('exportRangeCount');
      if (countEl) countEl.textContent = `(${count} Karyawan)`;

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