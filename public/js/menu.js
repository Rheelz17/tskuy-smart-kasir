/* ============================================================
   menu.js — Logika khusus halaman Manajemen Menu
   Dipanggil SETELAH admin.js (atau popup core utama Anda)
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
      const idMenu = btnHapus.dataset.id

      const elNama = document.getElementById("popup-hapus-nama-menu");
      if (elNama) elNama.textContent = namaMenu;

      const elId = document.getElementById("popup-hapus-db-id");
      if (elId) elId.value = idMenu;

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
        const idMenu = btnHapus.dataset.id;

        const elNama = document.getElementById("popup-hapus-nama-menu");
        if (elNama) elNama.textContent = namaMenu;

        const elId = document.getElementById("popup-hapus-db-id");
        if (elId) elId.value = idMenu;

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
     3. PROSES AJAX — ABSOLUTE BACKEND CONNECTORS
  ============================================================ */
  // A. PROSES AJAX: SIMPAN (TAMBAH)
  document.getElementById("popup-btn-tambah-simpan")?.addEventListener("click", function (e) {
    e.preventDefault();

    // Buat FormData untuk menangani file upload foto produk
    const formData = new FormData();
    formData.append("name", document.getElementById("popup-tambah-nama").value);
    formData.append("description", document.getElementById("popup-tambah-deskripsi").value);
    formData.append("category_id", document.getElementById("popup-tambah-kategori").value);
    formData.append("stock", document.getElementById("popup-tambah-stok").value);
    formData.append("price", document.getElementById("popup-tambah-harga").value);

    // Ambil status ketersediaan (1 jika dicentang, 0 jika tidak)
    const aksesInput = document.getElementById("popup-tambah-status");
    if (aksesInput && aksesInput.checked) {
        formData.append("is-available", "1");
    } else {
        // Mengirimkan string "0". 
        formData.append("is-available", "0");
    }    

    // Ambil input file foto
    const fotoInput = document.getElementById("popup-tambah-input-foto");
    if (fotoInput && fotoInput.files[0]) {
        formData.append("foto", fotoInput.files[0]);
    }

    const currentToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    // Kirim data ke endpoint Laravel
    fetch("/admin/menu", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": currentToken,
            "Accept": "application/json"
        },
        body: formData
    })
    .then(res => {
        if (!res.ok) {
            // Jika backend melempar error validasi (422) atau error server (500)
            throw new Error("Server error atau validasi gagal");
        }
        return res.json();
    })
    .then(dataMenu => {
        if (dataMenu.success) {
            window.location.reload(); // Reload halaman jika berhasil untk sinkronisasi data baru
        } else {
            alert("Gagal menambahkan menu: " + (dataMenu.message || "Periksa kembali inputan Anda."));
        }
    })
    .catch(err => console.error("Error:", err));
  });

  // B. PROSES AJAX: UPDATE (EDIT)
  document.getElementById("popup-btn-edit-simpan")?.addEventListener("click", function (e) {
    e.preventDefault();
    const dbId = document.getElementById("popup-edit-id").value;
    const currentToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!dbId) {
        alert("ID Menu tidak valid!");
        return;
    }

    const formData = new FormData();
    formData.append("name", document.getElementById("popup-edit-nama").value);
    formData.append("description", document.getElementById("popup-edit-deskripsi").value);
    formData.append("category_id", document.getElementById("popup-edit-kategori").value);
    formData.append("stock", document.getElementById("popup-edit-stok").value);
    formData.append("price", document.getElementById("popup-edit-harga").value);    
    // Method Spoofing Laravel karena FormData tidak mendukung PUT secara murni saat upload file
    formData.append("_method", "PUT");

    if (inputFoto && inputFoto.files[0]) {
        formData.append("image", inputFoto.files[0]);
    }

    fetch(`/admin/menu/${dbId}`, {
        method: "POST", // Tetap POST, dibantu _method PUT di atas
        headers: {
            "X-CSRF-TOKEN": currentToken,
            "Accept": "application/json"
        },
        body: formData
    })
    .then(res => res.json())
    .then(dataMenu => {
        if (dataMenu.success) {
            window.location.reload();
        } else {
            alert("Gagal memperbarui data: " + (dataMenu.message || "Periksa kembali inputan Anda."));
        }
    })
    .catch(err => console.error("Error:", err));
  });

  /*  3. KONFIRMASI HAPUS — tombol "Ya, Hapus" */
  document.getElementById("btn-confirm-hapus")?.addEventListener("click", function () {
    const dbId = document.getElementById("popup-hapus-db-id").value;
    const currentToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!dbId) {
        alert("ID Menu tidak ditemukan!");
        return;
    }
    fetch(`/admin/menu/${dbId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": currentToken,
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(dataMenu => {
        if (dataMenu.success) {
            window.location.reload();
        } else {
            alert("Gagal menghapus menu: " + (dataMenu.message || "Terjadi kesalahan."));
        }
        window._closePopup?.();
    })
    .catch(err => console.error("Error:", err));
  });

  /* ============================================================
     4. TOMBOL EDIT MENU (PRE-FILL DATA POPUP)
     Klik tombol edit → pindahkan dataset menu ke form fields popup edit
  ============================================================ */
  //tambah MENU
  const btnTambahMenu = document.getElementById("btn-tambah-menu");
  if (btnTambahMenu) {
    btnTambahMenu.addEventListener("click", function (e) {
      if (window.innerWidth <= 480) {
        // Mobile: navigasi ke halaman terpisah
        window.location.href = '/admin/menu/tambah';
      } else {
        // Desktop: buka popup
        e.preventDefault();
        // Panggil core popup untuk memunculkan popup tambah menu
        window._openPopup?.("popup-tambah-menu");
      }      
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
    if (document.getElementById("popup-edit-deskripsi")) 
      document.getElementById("popup-edit-deskripsi").value = dataset.deskripsi || "";
    if (document.getElementById("popup-edit-kategori")) 
      document.getElementById("popup-edit-kategori").value = dataset.kategori || ""; // Mengisi ID kategori
    if (document.getElementById("popup-edit-stok")) 
      document.getElementById("popup-edit-stok").value = dataset.stok || "0";
    if (document.getElementById("popup-edit-harga")) 
      document.getElementById("popup-edit-harga").value = dataset.harga || "0";

    // Mengeset preview foto menu asli dari database, jika kosong arahkan ke placeholder default
    const popupEditFotoImg = document.getElementById("popup-edit-foto-img");
    if (popupEditFotoImg) {
      popupEditFotoImg.src = dataset.foto ? `/storage/${dataset.foto}` : "/images/default-menu.jpg"; 
    }

    // Buka popup edit menu
    window._openPopup?.("popup-edit-menu");
  }

  /* ============================================================
     5. TOGGLE INTERAKTIF STATUS KETERSEDIAAN MENU (TAMBAHAN KHUSUS)
     Mengubah status aktif/tidak langsung dari baris tabel menu
  ============================================================ */
  tabelMenu?.addEventListener("change", function (e) {
    // if (e.target.classList.contains("toggle-status-menu")) {
    //   const checkbox = e.target;
    //   const menuId = checkbox.dataset.id;
    //   const namaMenu = checkbox.dataset.nama || `ID ${menuId}`; // Mengambil nama menu jika ada di dataset
    //   const isTersedia = checkbox.checked ? "1" : "0"; 
    //   const currentToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    //   const statusAwal = !checkbox.checked;

    //   // Ubah URL ke endpoint khusus toggle-status dengan method PATCH
    //   fetch(`/admin/menu/${menuId}/toggle-status`, {
    //       method: "PATCH",
    //       headers: {
    //           "X-CSRF-TOKEN": currentToken,
    //           "Content-Type": "application/json",
    //           "Accept": "application/json"
    //       },
    //       body: JSON.stringify({
    //           is_available: isTersedia
    //       })
    //   })
    //   .then(res => res.json())
    //   .then(dataMenu => {
    //       if (dataMenu.success) {
    //           if (typeof window.showToast === "function") {
    //               const teksStatus = checkbox.checked ? "Tersedia" : "Habis";
    //               window.showToast(`Status ${namaMenu} diubah menjadi [${teksStatus}] ✓`);
    //           }
    //       } else {
    //           checkbox.checked = statusAwal;
    //           alert("Gagal memperbarui status: " + (dataMenu.message || "Terjadi kesalahan."));
    //       }
    //   })
    //   .catch(err => {
    //       console.error("Error:", err);
    //       checkbox.checked = statusAwal;
    //       alert("Terjadi kesalahan koneksi saat memperbarui status.");
    //   });
    // }
    if (e.target.classList.contains("toggle-status-menu")) {
      eksekusiToggleStatus(e.target);
    }
  });

  const cardListMenu = document.querySelector(".menu-card-list");
  cardListMenu?.addEventListener("change", function (e) {
    if (e.target.classList.contains("toggle-status-menu")) {
      eksekusiToggleStatus(e.target);
    }
  });

  function eksekusiToggleStatus(checkbox) {
    const menuId = checkbox.dataset.id;
    const namaMenu = checkbox.dataset.nama || `ID ${menuId}`;
    const isTersedia = checkbox.checked ? "1" : "0"; 
    const currentToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const statusAwal = !checkbox.checked;

    fetch(`/admin/menu/${menuId}/toggle-status`, {
        method: "PATCH",
        headers: {
            "X-CSRF-TOKEN": currentToken,
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            is_available: isTersedia
        })
    })
    .then(res => res.json())
    .then(dataMenu => {
        if (dataMenu.success) {
            if (typeof window.showToast === "function") {
                const teksStatus = checkbox.checked ? "Tersedia" : "Habis";
                window.showToast(`Status ${namaMenu} diubah menjadi [${teksStatus}] ✓`);
            }
        } else {
            checkbox.checked = statusAwal;
            alert("Gagal memperbarui status: " + (dataMenu.message || "Terjadi kesalahan."));
        }
    })
    .catch(err => {
        console.error("Error:", err);
        checkbox.checked = statusAwal;
        alert("Terjadi kesalahan koneksi saat memperbarui status.");
    });
  } 

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

  /* ============================================================
     6. IMAGE UPLOAD & PREVIEW LOGIC
     Menangani pratinjau gambar saat memilih file gambar baru
  ============================================================ */
  /* Upload foto popup tambah */
  const popupTambahInputFoto = document.getElementById('popup-tambah-input-foto');
  const popupTambahUploadArea = document.getElementById('popup-tambah-upload-area');
  const popupTambahPreview    = document.getElementById('popup-tambah-preview');

  popupTambahUploadArea?.addEventListener('click', (e) => {
    e.preventDefault(); 
    popupTambahInputFoto?.click();
  });

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

/*  A. LOGIKAL FOTO POPUP EDIT (TRIGGER & PREVIEW) Ganti foto popup edit */
  const btnGantiFoto = document.getElementById("popup-edit-btn-ganti-foto");
  const inputFoto = document.getElementById("popup-edit-input-foto");
  const imgPreview = document.getElementById("popup-edit-foto-img");

// Klik icon pensil emas -> memicu klik input file asli yang tersembunyi
  btnGantiFoto?.addEventListener("click", function () {
    e.preventDefault();
    inputFoto?.click();
  });

  // Saat user memilih foto baru, ganti gambar preview di popup secara realtime
  inputFoto?.addEventListener("change", function () {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            if (imgPreview) imgPreview.src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    }
  });

  // Tombol Hapus Foto di Popup Edit
  document.getElementById("popup-edit-btn-hapus-foto")?.addEventListener("click", function () {
    e.preventDefault();
    if (imgPreview) imgPreview.src = "/images/default-menu.jpg"; // Path default foto menu kosong
    if (inputFoto) inputFoto.value = ""; // Reset input file
  });

  document.querySelectorAll('[data-close], .btn-batal').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      window._closePopup?.(); // Menutup pop-up yang sedang aktif
    });
  });


  // ============================================================
  // FUNGSI UTAMA: FILTER DAN PAGINASI MULTI-DEVICE
  // ============================================================
  let currentPage = 1;
  const itemsPerPage = 6;

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

    // Filter tahap awal: Kumpulkan data yang lolos pencarian & tab
    rows.forEach((row) => {
      const rowKategori = row.dataset.kategori;
      const text = row.textContent.toLowerCase();

      const cocokKategori = (filterKategori === "all" || rowKategori === filterKategori);
      const cocokSearch = text.includes(keyword);

      if (cocokKategori && cocokSearch) {
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
      infoDesktop.textContent = `Menampilkan ${displayStart} sampai ${displayEnd} dari ${totalRows} menu`;
    }
    renderPaginationButtons(document.querySelector(".content-footer .pagination"), totalPagesRows);

    // ------------------------------------------------------------
    // B. PROSES UNTUK MOBILE (CARD LIST)
    // ------------------------------------------------------------
    const cards = document.querySelectorAll(".karyawan-card");
    let matchedCards = [];

    // Filter tahap awal mobile card
    cards.forEach((card) => {
      const cardKategori = card.dataset.kategori;
      const text = card.textContent.toLowerCase();

      const cocokKategori = (filterKategori === "all" || cardKategori === filterKategori);
      const cocokSearch = text.includes(keyword);

      if (cocokKategori && cocokSearch) {
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
});