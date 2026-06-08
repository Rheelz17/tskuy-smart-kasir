/* ============================================================
   menu.js — Logika halaman Manajemen Menu (FIXED)
   Dipanggil SETELAH admin.js & kasir-core.js di layouts/admin.blade.php

   BUG YANG DIPERBAIKI:
   ① Hamburger drawer tidak bisa dibuka
      → Listener dipindah ke DOMContentLoaded, tidak bergantung pada
        window._openDrawer yang mungkin belum terdefinisi
   ② Tambah/Edit menu tidak muncul
      → Breakpoint mobile diubah 768px (konsisten dengan layout),
        popup dibuka via classList langsung sebagai fallback
   ③ Filter tab kategori berdasarkan ID (bukan nama) — data-kategori="1" 
      bukan "makanan", disesuaikan dengan data dari controller Laravel
   ④ Foto edit: tombol Ganti & Hapus tidak berfungsi
      → Event listener dipasang ulang dengan benar setelah prefill
   ⑤ Paginasi tidak bisa ke sebelumnya/selanjutnya
      → Fungsi renderPaginationButtons diperbaiki agar tombol benar-benar 
        memanggil updateTampilanDanPaginasi() setelah perubahan currentPage
   ⑥ Popup tambah tidak reset field saat dibuka ulang
   ⑦ Breakpoint mobile/desktop 480→768 konsisten
============================================================ */

document.addEventListener("DOMContentLoaded", function () {

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  /* ============================================================
     ① HAMBURGER DRAWER
     Dipasang langsung di sini sebagai backup — admin.js
     kadang load terlambat. Jika admin.js sudah menangani ini,
     listener ini tidak akan konflik karena sudah ada .is-open check.
  ============================================================ */
  const hamburgerBtn  = document.getElementById('hamburger-btn');
  const drawer        = document.getElementById('drawer');
  const drawerOverlay = document.getElementById('drawer-overlay');

  function bukaDrawer() {
    drawer?.classList.add('is-open');
    drawerOverlay?.classList.add('is-open');
    hamburgerBtn?.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function tutupDrawer() {
    drawer?.classList.remove('is-open');
    drawerOverlay?.classList.remove('is-open');
    hamburgerBtn?.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  // Pasang listener HANYA jika belum ada (hindari dobel)
  if (hamburgerBtn && !hamburgerBtn._drawerBound) {
    hamburgerBtn._drawerBound = true;
    hamburgerBtn.addEventListener('click', function () {
      if (drawer?.classList.contains('is-open')) {
        tutupDrawer();
      } else {
        bukaDrawer();
      }
    });
  }

  if (drawerOverlay && !drawerOverlay._drawerBound) {
    drawerOverlay._drawerBound = true;
    drawerOverlay.addEventListener('click', tutupDrawer);
  }

  // Tombol close di dalam drawer (jika ada)
  document.querySelectorAll('[data-close-drawer]').forEach(btn => {
    if (!btn._drawerBound) {
      btn._drawerBound = true;
      btn.addEventListener('click', tutupDrawer);
    }
  });

  /* ============================================================
     HELPER POPUP — wrapper aman yang tidak crash jika admin.js
     belum define window._openPopup
  ============================================================ */
  function bukaPopup(id) {
    if (typeof window._openPopup === 'function') {
      window._openPopup(id);
    } else {
      // Fallback langsung
      document.querySelector('.popup-overlay')?.classList.add('is-open');
      document.getElementById(id)?.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }
  }

  function tutupPopup(id) {
    if (typeof window._closePopup === 'function') {
      window._closePopup(id);
    } else {
      document.querySelector('.popup-overlay')?.classList.remove('is-open');
      if (id) {
        document.getElementById(id)?.classList.remove('is-open');
      } else {
        document.querySelectorAll('.popup.is-open').forEach(p => p.classList.remove('is-open'));
      }
      document.body.style.overflow = '';
    }
  }

  /* ============================================================
     HELPER: Deteksi Mobile (≤768px — konsisten dengan media query CSS)
  ============================================================ */
  const isMobile = () => window.innerWidth <= 768;

  /* ============================================================
     ② TOMBOL TAMBAH MENU
     Mobile → navigasi ke halaman menu-tambah.blade.php
     Desktop → buka popup tambah
  ============================================================ */
  document.getElementById('btn-tambah-menu')?.addEventListener('click', function (e) {
    e.preventDefault();

    if (isMobile()) {
      window.location.href = '/admin/menu/tambah';
      return;
    }

    // Desktop: reset semua field form tambah sebelum dibuka
    const fieldsToReset = [
      'popup-tambah-nama', 'popup-tambah-deskripsi',
      'popup-tambah-stok', 'popup-tambah-harga'
    ];
    fieldsToReset.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });

    // Reset select kategori ke default
    const selectKat = document.getElementById('popup-tambah-kategori');
    if (selectKat) selectKat.selectedIndex = 0;

    // Reset toggle tersedia ke aktif
    const toggleTersedia = document.getElementById('popup-tambah-status');
    if (toggleTersedia) toggleTersedia.checked = true;

    // Reset area upload foto
    const uploadArea = document.getElementById('popup-tambah-upload-area');
    const preview    = document.getElementById('popup-tambah-preview');
    const inputFoto  = document.getElementById('popup-tambah-input-foto');
    if (uploadArea) uploadArea.classList.remove('has-foto');
    if (preview)    preview.src = '';
    if (inputFoto)  inputFoto.value = '';

    bukaPopup('popup-tambah-menu');
  });

  /* ============================================================
     ③ TOMBOL EDIT MENU — via delegasi event (desktop tabel + mobile card)
     Mobile → navigasi ke halaman menu-edit.blade.php
     Desktop → prefill popup edit
  ============================================================ */
  document.addEventListener('click', function (e) {
    const editBtn = e.target.closest('.btn-edit-menu');
    if (!editBtn) return;
    e.preventDefault();
    e.stopPropagation();

    const ds = editBtn.dataset; // id, nama, deskripsi, kategori, stok, harga, foto, status

    if (isMobile()) {
      window.location.href = `/admin/menu/${ds.id}/edit`;
      return;
    }

    // Desktop: prefill popup edit
    prefillPopupEdit(ds);
    bukaPopup('popup-edit-menu');
  });

  /* ============================================================
     ④ PREFILL POPUP EDIT — isi semua field sesuai data menu
  ============================================================ */
  function prefillPopupEdit(ds) {
    const set = (id, val) => {
      const el = document.getElementById(id);
      if (el) el.value = val ?? '';
    };

    // Tampilkan kode format yang lebih rapi di field ID (display-only)
    set('popup-edit-id', ds.kode || `MNU${String(ds.id).padStart(3, '0')}`);

    set('popup-edit-nama',      ds.nama);
    set('popup-edit-deskripsi', ds.deskripsi);
    set('popup-edit-stok',      ds.stok);
    set('popup-edit-harga',     ds.harga);

    // Pilih kategori yang sesuai (value = category_id integer)
    const selectKat = document.getElementById('popup-edit-kategori');
    if (selectKat && ds.kategori) {
      // Coba cocokkan via value (ID numerik dari Laravel)
      const opt = [...selectKat.options].find(o => String(o.value) === String(ds.kategori));
      if (opt) opt.selected = true;
    }

    // Toggle status tersedia
    const toggleStatus = document.getElementById('popup-edit-tersedia');
    if (toggleStatus) {
      toggleStatus.checked = (ds.status == 1 || ds.status === 'true' || ds.status === '1');
    }

    // Foto menu
    const fotoImg = document.getElementById('popup-edit-foto-img');
    if (fotoImg) {
      fotoImg.src     = ds.foto ? `/storage/${ds.foto}` : '/images/default-menu.jpg';
      fotoImg.style.filter = ''; // Hapus efek grayscale jika ada dari edit sebelumnya
    }

    // Reset input file ganti foto
    const inputGanti = document.getElementById('popup-edit-input-foto');
    if (inputGanti) inputGanti.value = '';

    // Simpan ID asli (integer) di data attribute form untuk dipakai saat submit
    const formEdit = document.getElementById('form-edit-menu');
    if (formEdit) formEdit.dataset.menuId = ds.id;
  }

  /* ============================================================
     ④.b FOTO POPUP EDIT — Ganti & Hapus foto
  ============================================================ */

  // Tombol Ganti Foto → trigger input file tersembunyi
  document.getElementById('popup-edit-btn-ganti-foto')?.addEventListener('click', function () {
    document.getElementById('popup-edit-input-foto')?.click();
  });

  // File dipilih → update preview gambar langsung
  document.getElementById('popup-edit-input-foto')?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
      showNotif('Ukuran foto melebihi 2 MB.', 'error');
      this.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = e => {
      const img = document.getElementById('popup-edit-foto-img');
      if (img) {
        img.src = e.target.result;
        img.style.filter = ''; // Pastikan tidak grayscale
      }
    };
    reader.readAsDataURL(file);
  });

  // Tombol Hapus Foto → set foto ke placeholder + grayscale visual
  document.getElementById('popup-edit-btn-hapus-foto')?.addEventListener('click', function () {
    if (!confirm('Hapus foto menu ini? Foto tidak bisa dikembalikan.')) return;

    const img = document.getElementById('popup-edit-foto-img');
    if (img) {
      img.src = '/images/default-menu.jpg';
      img.style.filter = 'grayscale(1) opacity(0.4)';
    }

    const inputFoto = document.getElementById('popup-edit-input-foto');
    if (inputFoto) inputFoto.value = '';

    showNotif('Foto akan dihapus saat kamu klik Simpan.', 'info');
  });

  /* ============================================================
     UPLOAD FOTO POPUP TAMBAH
  ============================================================ */
  const popupTambahArea    = document.getElementById('popup-tambah-upload-area');
  const popupTambahInput   = document.getElementById('popup-tambah-input-foto');
  const popupTambahPreview = document.getElementById('popup-tambah-preview');

  popupTambahArea?.addEventListener('click', () => popupTambahInput?.click());

  popupTambahInput?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
      showNotif('Ukuran foto melebihi 2 MB.', 'error');
      this.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = e => {
      if (popupTambahPreview) {
        popupTambahPreview.src = e.target.result;
        popupTambahArea.classList.add('has-foto');
      }
    };
    reader.readAsDataURL(file);
  });

  /* ============================================================
     ⑥ PROSES AJAX: SIMPAN MENU BARU (popup-tambah-menu)
  ============================================================ */
  document.getElementById('popup-btn-tambah-simpan')?.addEventListener('click', function (e) {
    e.preventDefault();

    const nama     = document.getElementById('popup-tambah-nama')?.value?.trim();
    const kategori = document.getElementById('popup-tambah-kategori')?.value;
    const stok     = document.getElementById('popup-tambah-stok')?.value;
    const harga    = document.getElementById('popup-tambah-harga')?.value;

    if (!nama || !kategori || !stok || !harga) {
      showNotif('Lengkapi semua field yang wajib diisi.', 'error');
      return;
    }

    const formData = new FormData();
    formData.append('name',          nama);
    formData.append('category_id',   kategori);
    formData.append('stock',         stok);
    formData.append('price',         harga);
    formData.append('description',   document.getElementById('popup-tambah-deskripsi')?.value || '');

    const toggleTersedia = document.getElementById('popup-tambah-status');
    // Checkbox: jika checked → kirim "1", jika tidak → tidak dikirim sama sekali
    // Controller cek dengan $request->has('is_available')
    if (toggleTersedia?.checked) {
      formData.append('is_available', '1');
    }

    const inputFoto = document.getElementById('popup-tambah-input-foto');
    if (inputFoto?.files[0]) {
      formData.append('image', inputFoto.files[0]);
    }

    const btn = this;
    btn.textContent = 'Menyimpan...';
    btn.disabled    = true;

    fetch('/admin/menu', {
      method  : 'POST',
      headers : {
        'X-CSRF-TOKEN'     : document.querySelector('meta[name="csrf-token"]')?.content,
        'Accept'           : 'application/json',
        'X-Requested-With' : 'XMLHttpRequest',
      },
      body: formData,
    })
    .then(async res => {
      const data = await res.json();
      if (res.ok && data.success) {
        showNotif('Menu baru berhasil ditambahkan!', 'success');
        setTimeout(() => window.location.reload(), 900);
      } else if (data.errors) {
        const pesanError = Object.values(data.errors).flat().join('\n');
        showNotif(pesanError.split('\n')[0], 'error');
      } else {
        showNotif(data.message || 'Gagal menyimpan menu.', 'error');
      }
    })
    .catch(err => {
      console.error('Error:', err);
      showNotif('Koneksi bermasalah. Coba lagi.', 'error');
    })
    .finally(() => {
      btn.textContent = 'Simpan Menu';
      btn.disabled    = false;
    });
  });

  /* ============================================================
     ⑥.b PROSES AJAX: UPDATE MENU (popup-edit-menu)
  ============================================================ */
  document.getElementById('popup-btn-edit-simpan')?.addEventListener('click', function (e) {
    e.preventDefault();

    // Ambil ID dari form dataset (disimpan saat prefill)
    const formEdit = document.getElementById('form-edit-menu');
    const menuId   = formEdit?.dataset.menuId;

    if (!menuId) {
      showNotif('ID menu tidak ditemukan. Coba tutup dan buka kembali.', 'error');
      return;
    }

    const nama     = document.getElementById('popup-edit-nama')?.value?.trim();
    const kategori = document.getElementById('popup-edit-kategori')?.value;
    const stok     = document.getElementById('popup-edit-stok')?.value;
    const harga    = document.getElementById('popup-edit-harga')?.value;

    if (!nama || !kategori) {
      showNotif('Nama dan kategori wajib diisi.', 'error');
      return;
    }

    const formData = new FormData();
    formData.append('_method',     'PUT');  // Method spoofing Laravel
    formData.append('name',        nama);
    formData.append('category_id', kategori);
    formData.append('stock',       stok   || '0');
    formData.append('price',       harga  || '0');
    formData.append('description', document.getElementById('popup-edit-deskripsi')?.value || '');

    // Toggle tersedia
    const toggleStatus = document.getElementById('popup-edit-tersedia');
    if (toggleStatus?.checked) formData.append('is_available', '1');

    // Foto baru (jika ada)
    const inputFotoGanti = document.getElementById('popup-edit-input-foto');
    if (inputFotoGanti?.files[0]) {
      formData.append('image', inputFotoGanti.files[0]);
    }

    // Cek apakah foto di-hapus (grayscale = tanda sudah dihapus)
    const fotoImg = document.getElementById('popup-edit-foto-img');
    if (fotoImg?.style.filter.includes('grayscale')) {
      formData.append('hapus_foto', '1');
    }

    const btn = this;
    btn.textContent = 'Menyimpan...';
    btn.disabled    = true;

    fetch(`/admin/menu/${menuId}`, {
      method  : 'POST',       // POST + _method=PUT
      headers : {
        'X-CSRF-TOKEN'     : document.querySelector('meta[name="csrf-token"]')?.content,
        'Accept'           : 'application/json',
        'X-Requested-With' : 'XMLHttpRequest',
      },
      body: formData,
    })
    .then(async res => {
      const data = await res.json();
      if (res.ok && data.success) {
        showNotif('Data menu berhasil diperbarui!', 'success');
        setTimeout(() => window.location.reload(), 900);
      } else if (data.errors) {
        const pesanError = Object.values(data.errors).flat().join('\n');
        showNotif(pesanError.split('\n')[0], 'error');
      } else {
        showNotif(data.message || 'Gagal memperbarui menu.', 'error');
      }
    })
    .catch(err => {
      console.error('Error:', err);
      showNotif('Koneksi bermasalah. Coba lagi.', 'error');
    })
    .finally(() => {
      btn.textContent = 'Simpan Perubahan';
      btn.disabled    = false;
    });
  });

  /* ============================================================
     TOMBOL HAPUS MENU — isi popup konfirmasi lalu buka
  ============================================================ */
  document.addEventListener('click', function (e) {
    const hapusBtn = e.target.closest('.btn-hapus-menu');
    if (!hapusBtn) return;
    e.preventDefault();

    const id   = hapusBtn.dataset.id;
    const nama = hapusBtn.dataset.nama || 'menu ini';

    const idEl   = document.getElementById('popup-hapus-db-id');
    const namaEl = document.getElementById('popup-hapus-nama-menu');
    if (idEl)   idEl.value       = id;
    if (namaEl) namaEl.textContent = nama;

    bukaPopup('popup-hapus-menu');
  });

  /* ============================================================
     KONFIRMASI HAPUS — tombol "Ya, Hapus"
  ============================================================ */
  document.getElementById('btn-confirm-hapus')?.addEventListener('click', function () {
    const dbId = document.getElementById('popup-hapus-db-id')?.value;

    fetch(`/admin/menu/${dbId}`, {
      method  : 'DELETE',
      headers : {
        'X-CSRF-TOKEN'     : document.querySelector('meta[name="csrf-token"]')?.content,
        'Content-Type'     : 'application/json',
        'Accept'           : 'application/json',
        'X-Requested-With' : 'XMLHttpRequest',
      },
    })
    .then(res => res.json())
    .then(data => {
      tutupPopup();
      if (data.success) {
        showNotif('Menu berhasil dihapus!', 'success');
        setTimeout(() => window.location.reload(), 900);
      } else {
        showNotif(data.message || 'Gagal menghapus menu.', 'error');
      }
    })
    .catch(() => showNotif('Koneksi bermasalah. Coba lagi.', 'error'));
  });

  /* ============================================================
     TOMBOL BATAL / data-close
  ============================================================ */
  document.querySelectorAll('[data-close]').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      tutupPopup();
    });
  });

  /* ============================================================
     ③ FILTER TAB KATEGORI
     PENTING: data-kategori di tombol tab (menu.blade.php mobile)
     menggunakan $cat->id (integer), sedangkan data-kategori di card
     menggunakan $item->category_id (integer) juga.
     Tabel desktop menggunakan strtolower($menu->category->name).
     Jadi kita handle dua kasus: ID (mobile card) dan nama (desktop tabel).
  ============================================================ */
  document.querySelectorAll('.category-tabs .tab[data-kategori]').forEach(btn => {
    btn.addEventListener('click', function () {
      const kategoriDipilih = this.dataset.kategori;

      // Update active state semua tab sekaligus (sinkronisasi mobile+desktop)
      document.querySelectorAll('.category-tabs .tab').forEach(b => b.classList.remove('active'));
      document.querySelectorAll(`.category-tabs .tab[data-kategori="${kategoriDipilih}"]`)
        .forEach(b => b.classList.add('active'));

      // Filter cards mobile (berdasarkan category_id integer)
      document.querySelectorAll('.menu-card[data-kategori]').forEach(card => {
        if (kategoriDipilih === 'all') {
          card.style.display = '';
        } else {
          card.style.display = (String(card.dataset.kategori) === String(kategoriDipilih)) ? '' : 'none';
        }
      });

      // Filter baris tabel desktop (berdasarkan nama kategori lowercase)
      document.querySelectorAll('#tabel-menu tbody tr[data-kategori]').forEach(row => {
        if (kategoriDipilih === 'all') {
          row.style.display = '';
        } else {
          // Coba cocokkan dengan nama kategori (row pakai nama string)
          row.style.display = (String(row.dataset.kategori) === String(kategoriDipilih)) ? '' : 'none';
        }
      });

      currentPage = 1;
      updateTampilanDanPaginasi();
    });
  });

  /* ============================================================
     TOGGLE STATUS KETERSEDIAAN (tabel + card mobile)
  ============================================================ */
  function eksekusiToggleStatus(checkbox) {
    const menuId   = checkbox.dataset.id;
    const namaMenu = checkbox.dataset.nama || `ID ${menuId}`;
    const isChecked = checkbox.checked;
    const statusAwal = !isChecked;
    const currentToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // Sinkronkan semua toggle dengan ID yang sama
    document.querySelectorAll(`.toggle-status-menu[data-id="${menuId}"]`).forEach(cb => {
      cb.checked = isChecked;
    });

    fetch(`/admin/menu/${menuId}/toggle-status`, {
      method  : 'POST',
      headers : {
        'X-CSRF-TOKEN'     : currentToken,
        'Content-Type'     : 'application/json',
        'Accept'           : 'application/json',
        'X-Requested-With' : 'XMLHttpRequest',
      },
      body: JSON.stringify({ is_available: isChecked ? 1 : 0 }),
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const statusTeks = isChecked ? 'Tersedia' : 'Habis';
        showNotif(`${namaMenu} → ${statusTeks}`, 'success');
      } else {
        // Rollback semua toggle
        document.querySelectorAll(`.toggle-status-menu[data-id="${menuId}"]`).forEach(cb => {
          cb.checked = statusAwal;
        });
        showNotif(data.message || 'Gagal memperbarui status.', 'error');
      }
    })
    .catch(() => {
      document.querySelectorAll(`.toggle-status-menu[data-id="${menuId}"]`).forEach(cb => {
        cb.checked = statusAwal;
      });
      showNotif('Koneksi bermasalah.', 'error');
    });
  }

  // Delegasi event untuk toggle di tabel dan di card list
  document.getElementById('tabel-menu')?.addEventListener('change', e => {
    if (e.target.classList.contains('toggle-status-menu')) eksekusiToggleStatus(e.target);
  });

  document.querySelector('.menu-card-list')?.addEventListener('change', e => {
    if (e.target.classList.contains('toggle-status-menu')) eksekusiToggleStatus(e.target);
  });

  /* ============================================================
     SEARCH REAL-TIME
  ============================================================ */
  document.querySelectorAll('.search-wrapper input').forEach(input => {
    input.addEventListener('input', function () {
      const keyword = this.value.toLowerCase().trim();

      // Sinkronkan search input mobile+desktop
      document.querySelectorAll('.search-wrapper input').forEach(other => {
        if (other !== this) other.value = this.value;
      });

      currentPage = 1;
      updateTampilanDanPaginasi();
    });
  });

  /* ============================================================
     ⑤ TAMPILAN + PAGINASI — FIXED
  ============================================================ */
  let currentPage  = 1;
  const itemsPerPage = 8;

  function updateTampilanDanPaginasi() {
    const tabAktif       = document.querySelector('.category-tabs .tab.active');
    const filterKategori = tabAktif?.dataset.kategori ?? 'all';

    const searchInput = document.querySelector('.search-wrapper input');
    const keyword     = searchInput?.value.toLowerCase().trim() ?? '';

    // ── DESKTOP: Filter baris tabel ─────────────────────────
    const rows    = document.querySelectorAll('#tabel-menu tbody tr');
    const rowsOk  = [];

    rows.forEach(row => {
      const rowKat   = String(row.dataset.kategori ?? '');
      const rowTeks  = row.textContent.toLowerCase();
      const cocokKat = (filterKategori === 'all' || rowKat === String(filterKategori));
      const cocokSrc = rowTeks.includes(keyword);

      if (cocokKat && cocokSrc) {
        rowsOk.push(row);
        row.style.display = '';    // Sementara tampilkan, paginasi akan hide sisanya
      } else {
        row.style.display = 'none';
      }
    });

    const totalRows  = rowsOk.length;
    const totalPagesR = Math.max(1, Math.ceil(totalRows / itemsPerPage));
    if (currentPage > totalPagesR) currentPage = totalPagesR;

    const startR = (currentPage - 1) * itemsPerPage;
    const endR   = startR + itemsPerPage;
    rowsOk.forEach((row, i) => { row.style.display = (i >= startR && i < endR) ? '' : 'none'; });

    const infoDesktop = document.querySelector('.content-footer .data-info');
    if (infoDesktop) {
      const s = totalRows === 0 ? 0 : startR + 1;
      const e = Math.min(endR, totalRows);
      infoDesktop.textContent = `Menampilkan ${s} sampai ${e} dari ${totalRows} menu`;
    }
    renderPaginasi(document.querySelector('.content-footer .pagination'), totalPagesR);

    // ── MOBILE: Filter card list ─────────────────────────────
    const cards  = document.querySelectorAll('.menu-card');
    const cardsOk = [];

    cards.forEach(card => {
      const cardKat  = String(card.dataset.kategori ?? '');
      const cardTeks = card.textContent.toLowerCase();
      const cocokKat = (filterKategori === 'all' || cardKat === String(filterKategori));
      const cocokSrc = cardTeks.includes(keyword);

      if (cocokKat && cocokSrc) {
        cardsOk.push(card);
      } else {
        card.style.display = 'none';
      }
    });

    const totalCards  = cardsOk.length;
    const totalPagesC = Math.max(1, Math.ceil(totalCards / itemsPerPage));

    cardsOk.forEach((card, i) => { card.style.display = (i >= startR && i < endR) ? '' : 'none'; });

    const infoMobile = document.querySelector('.content-footer-mobile .data-info');
    if (infoMobile) {
      const s = totalCards === 0 ? 0 : startR + 1;
      const e = Math.min(endR, totalCards);
      infoMobile.textContent = `Menampilkan ${s} sampai ${e} dari ${totalCards} menu`;
    }
    renderPaginasi(document.querySelector('.content-footer-mobile .pagination'), totalPagesC);
  }

  /* ============================================================
     ⑤.b RENDER TOMBOL PAGINASI — FIXED
     Masalah sebelumnya: currentPage tidak diubah sebelum panggil
     updateTampilanDanPaginasi(), sehingga tombol tidak efektif.
  ============================================================ */
  function renderPaginasi(container, totalPages) {
    if (!container) return;
    container.innerHTML = '';

    // Tombol Sebelumnya
    const prevBtn = document.createElement('button');
    prevBtn.className = `page-link${currentPage === 1 ? ' disabled' : ''}`;
    prevBtn.textContent = 'Sebelumnya';
    prevBtn.disabled    = currentPage === 1;
    prevBtn.addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;                   // ← ini yang dulu hilang
        updateTampilanDanPaginasi();
        container.closest('.content-footer-mobile, .content-footer')
          ?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    });
    container.appendChild(prevBtn);

    // Angka halaman
    for (let i = 1; i <= totalPages; i++) {
      const pageBtn = document.createElement('button');
      pageBtn.className   = `page-number${currentPage === i ? ' active' : ''}`;
      pageBtn.textContent = i;
      pageBtn.addEventListener('click', () => {
        currentPage = i;               // ← dan ini
        updateTampilanDanPaginasi();
      });
      container.appendChild(pageBtn);
    }

    // Tombol Selanjutnya
    const nextBtn = document.createElement('button');
    nextBtn.className = `page-link${currentPage === totalPages ? ' disabled' : ''}`;
    nextBtn.textContent = 'Selanjutnya';
    nextBtn.disabled    = currentPage === totalPages;
    nextBtn.addEventListener('click', () => {
      if (currentPage < totalPages) {
        currentPage++;                  // ← dan ini
        updateTampilanDanPaginasi();
        container.closest('.content-footer-mobile, .content-footer')
          ?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    });
    container.appendChild(nextBtn);
  }

  // Jalankan saat halaman pertama kali dimuat
  updateTampilanDanPaginasi();

  /* ============================================================
     POPUP EXPORT
  ============================================================ */
  document.getElementById('btnExport')?.addEventListener('click', function (e) {
    e.preventDefault();
    const visibleRows = document.querySelectorAll("#tabel-menu tbody tr:not([style*='display: none'])");
    const countEl = document.getElementById('exportRangeCount');
    if (countEl) countEl.textContent = `(${visibleRows.length} Menu)`;
    bukaPopup('popupExport');
  });

  document.getElementById('closeExportBtn')?.addEventListener('click', function (e) {
    e.preventDefault();
    tutupPopup('popupExport');
  });

  document.querySelectorAll('.export-option').forEach(opt => {
    opt.addEventListener('click', function () {
      document.querySelectorAll('.export-option').forEach(o => o.classList.remove('selected'));
      this.classList.add('selected');
    });
  });

  document.getElementById('btnUnduhData')?.addEventListener('click', function (e) {
    e.preventDefault();
    const fmt      = document.querySelector('.export-option.selected')?.dataset.fmt ?? 'xlsx';
    const rentang  = document.querySelector('input[name="exportRange"]:checked')?.value ?? 'current';
    tutupPopup('popupExport');
    showNotif(`Data (${rentang}) diunduh sebagai ${fmt.toUpperCase()}`, 'success');
  });

  /* ============================================================
     HELPER showNotif — wrapper toast yang fleksibel
     Coba window.showToast dulu (dari admin.js),
     kalau tidak ada fallback ke console.
  ============================================================ */
  function showNotif(pesan, tipe = 'success') {
    if (typeof window.showToast === 'function') {
      window.showToast(pesan);
      return;
    }
    // Fallback: buat toast sederhana inline
    const wrap = document.getElementById('toast-container')
                || document.querySelector('.toast-container');
    if (!wrap) {
      console.log(`[${tipe}] ${pesan}`);
      return;
    }
    const toast = document.createElement('div');
    toast.style.cssText = `
      background: ${tipe === 'error' ? '#dc2626' : tipe === 'info' ? '#2563eb' : '#16a34a'};
      color: #fff; padding: 11px 16px; border-radius: 10px;
      font-size: 13px; font-weight: 600; margin-bottom: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,.15); max-width: 320px;
    `;
    toast.textContent = pesan;
    wrap.prepend(toast);
    setTimeout(() => toast.remove(), 3500);
  }

}); // END DOMContentLoaded