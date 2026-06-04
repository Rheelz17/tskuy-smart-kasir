/* ============================================================
   karyawan.js — Logika khusus halaman Manajemen Karyawan
   Dipanggil SETELAH admin.js
   Berisi:
   1. Tab filter jabatan (All / Admin / Kasir / Koki)
      → filter card mobile + baris tabel desktop
   2. Tombol hapus → isi nama ke popup konfirmasi, buka popup
   3. Search real-time
   4. Tombol tambah & edit → akan ditambah di iterasi berikutnya
============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  /* ============================================================
     1. TAB FILTER JABATAN
     Klik tab → update active state + filter card + filter tabel
  ============================================================ */
  document
    .querySelectorAll(".category-tabs .tab[data-jabatan]")
    .forEach((btn) => {
      btn.addEventListener("click", function () {
        // Update active tab
        document
          .querySelectorAll(".category-tabs .tab[data-jabatan]")
          .forEach((b) => {
            b.classList.remove("active");
          });
        this.classList.add("active");

        const jabatan = this.dataset.jabatan; // 'all' | 'admin' | 'kasir' | 'koki'

        // Filter card list (mobile)
        document
          .querySelectorAll(".karyawan-card[data-jabatan]")
          .forEach((card) => {
            if (jabatan === "all") {
              card.style.display = "";
            } else {
              card.style.display =
                card.dataset.jabatan === jabatan ? "" : "none";
            }
          });

        // Filter baris tabel (desktop)
        // Kolom jabatan = kolom ke-4 (index 3, setelah foto, nama, id)
        document.querySelectorAll("#tabel-karyawan tbody tr").forEach((row) => {
          if (jabatan === "all") {
            row.style.display = "";
          } else {
            const rowJabatan = row.cells[3]?.textContent.trim().toLowerCase();
            row.style.display = rowJabatan === jabatan ? "" : "none";
          }
        });
      });
    });

  /* ============================================================
     2. TOMBOL HAPUS KARYAWAN
     Setiap .btn-hapus-karyawan punya data-nama
     JS ambil data-nama → isi ke #hapus-nama-karyawan → buka popup
  ============================================================ */
  document.addEventListener("click", function (e) {
    const hapusBtn = e.target.closest(".btn-hapus-karyawan");
    if (!hapusBtn) return;

    const nama = hapusBtn.dataset.nama || "karyawan ini";

    // Isi nama ke teks popup
    const namaEl = document.getElementById("hapus-nama-karyawan");
    if (namaEl) namaEl.textContent = nama;

    // Buka popup konfirmasi hapus via shared popup system
    window._openPopup?.("popup-hapus-karyawan");
  });

  /* ============================================================
     3. KONFIRMASI HAPUS — tombol "Ya, Hapus"
     Saat ini hanya tutup popup + log ke console.
     Nanti saat sudah connect backend, ganti dengan fetch/axios DELETE.
  ============================================================ */
  document
    .getElementById("btn-confirm-hapus")
    ?.addEventListener("click", function () {
      const nama = document.getElementById("hapus-nama-karyawan")?.textContent;
      console.log(`Hapus karyawan: ${nama}`);
      // TODO: kirim request DELETE ke backend
      // fetch(`/api/karyawan/${id}`, { method: 'DELETE' })
      window._closePopup?.();
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
    });
  });

  /* ============================================================
     5. TOMBOL TAMBAH & EDIT KARYAWAN
     Placeholder — popup tambah/edit akan ditambah di iterasi berikutnya
  ============================================================ */
  // document.querySelector(".btn-tambah")?.addEventListener("click", function () {
  //   // TODO: buka popup tambah karyawan
  //   window._openPopup?.("popup-tambah-karyawan");
  // });

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

  // document.addEventListener("click", function (e) {
  //   const editBtn = e.target.closest(".btn-edit-karyawan");
  //   if (!editBtn) return;
  //   // TODO: pre-fill form edit dengan data karyawan yang dipilih
  //   window._openPopup?.("popup-edit-karyawan");
  // });

  document.addEventListener('click', function (e) {
    const editBtn = e.target.closest('.btn-edit-karyawan');
    if (!editBtn) return;

    const data = {
      id      : editBtn.dataset.id,
      nama    : editBtn.dataset.nama,
      jabatan : editBtn.dataset.jabatan,
      telp    : editBtn.dataset.telp,
      email   : editBtn.dataset.email,
      alamat  : editBtn.dataset.alamat,
    };

    if (window.innerWidth <= 480) {
      // Mobile: navigasi ke halaman edit dengan query param
      const params = new URLSearchParams(data);
      window.location.href = `/admin/karyawan/edit?${params}`;
    } else {
      // Desktop: pre-fill popup edit lalu buka
      e.preventDefault();
      prefillEditPopup(data);
      window._openPopup?.('popup-edit-karyawan');
    }
  });

  /**
   * Isi form di popup edit karyawan (desktop only).
   * ID element input di popup harus berprefix "popup-edit-".
   */
  function prefillEditPopup(data) {
    const set = (id, val) => {
      const el = document.getElementById(id);
      if (el) el.value = val;
    };

    set('popup-edit-id',     data.id);
    set('popup-edit-nama',   data.nama);
    set('popup-edit-telp',   data.telp);
    set('popup-edit-email',  data.email);
    set('popup-edit-alamat', data.alamat);

    // Select jabatan
    const selectEl = document.getElementById('popup-edit-jabatan');
    if (selectEl && data.jabatan) {
      const opt = [...selectEl.options].find(
        o => o.value.toLowerCase() === data.jabatan.toLowerCase()
      );
      if (opt) opt.selected = true;
    }
  }


//   <!--
//   ============================================================
//   SCRIPT TAMBAHAN — tambahkan di dalam <script> karyawan.js
//   atau inline sebelum </body>, SETELAH admin.js dan karyawan.js

//   Handles:
//   - Upload foto di popup tambah
//   - Ganti/hapus foto di popup edit
//   ============================================================
// -->

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
      popupTambahPreview.src = e.target.result;
      popupTambahUploadArea.classList.add('has-foto');
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
    reader.onload = e => { popupEditFotoImg.src = e.target.result; };
    reader.readAsDataURL(file);
  });

  /* Hapus foto popup edit */
  document.getElementById('popup-edit-btn-hapus-foto')?.addEventListener('click', () => {
    if (popupEditFotoImg) popupEditFotoImg.src = 'https://via.placeholder.com/300?text=No+Photo';
    if (popupEditInputFoto) popupEditInputFoto.value = '';
  });

});
