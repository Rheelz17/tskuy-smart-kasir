<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Karyawan — Tskuy Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="{{ asset('css/admincopy.css') }}" />

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: "Poppins", sans-serif;
      background: #fff;
      color: #1a1a1a;
      min-height: 100vh;
      padding-bottom: 90px;
    }

    /* Header */
    .form-page-header {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      padding: 16px 20px;
      border-bottom: 1px solid #f0f0f0;
      background: #fff;
    }

    .btn-back {
      position: absolute;
      left: 20px;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: none;
      border: none;
      cursor: pointer;
      color: #efb100;
    }

    .form-page-title { font-size: 17px; font-weight: 700; color: #1a1a1a; text-align: center; }
    .form-page-subtitle { text-align: center; font-size: 13px; color: #94a3b8; padding: 12px 24px 0; line-height: 1.5; }

    .form-page-body {
      padding: 20px 20px 0;
      display: flex;
      flex-direction: column;
      gap: 18px;
    }

    /* ---- SECTION FOTO EDIT ----
       Berbeda dengan tambah: foto sudah ada, ada tombol edit & hapus
       di bawah foto. Foto ditampilkan dalam card terpisah.
    */
    .foto-edit-card {
      background: #fff;
      border: 1px solid #f0f0f0;
      border-radius: 16px;
      padding: 16px;
    }

    .foto-edit-label {
      font-size: 13px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 12px;
    }

    .foto-edit-img {
      width: 100%;
      aspect-ratio: 4/3;
      object-fit: cover;
      border-radius: 12px;
      background: #e5e7eb;
      display: block;
    }

    /* Tombol edit & hapus foto di bawah foto */
    .foto-edit-actions {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-top: 12px;
    }

    .btn-foto-edit,
    .btn-foto-hapus {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      cursor: pointer;
      transition: opacity 0.2s;
    }

    .btn-foto-edit  { background: rgba(239,177,0,0.15); }
    .btn-foto-hapus { background: rgba(251,44,54,0.12); }

    .btn-foto-edit:hover,
    .btn-foto-hapus:hover { opacity: 0.75; }

    /* Input file tersembunyi untuk ganti foto */
    .input-foto-hidden {
      display: none;
    }

    /* Divider antara foto dan form */
    .foto-form-divider {
      border: none;
      border-top: 1px solid #f0f0f0;
      margin: 4px 0;
    }

    /* Form fields — sama dengan tambah-karyawan.html */
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 13px; font-weight: 600; color: #1a1a1a; }

    .form-input-kuning,
    .form-select-kuning {
      width: 100%;
      height: 48px;
      padding: 0 14px;
      border: 1.5px solid #efb100;
      border-radius: 10px;
      font-size: 14px;
      font-family: "Poppins", sans-serif;
      color: #1a1a1a;
      background: #fff;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
      appearance: none;
    }

    .form-input-kuning:focus,
    .form-select-kuning:focus {
      border-color: #d9a000;
      box-shadow: 0 0 0 3px rgba(239,177,0,0.12);
    }

    .form-input-kuning::placeholder { color: #bbb; }

    /* ID field: readonly dengan bg abu */
    .form-input-kuning[readonly] {
      background: #f3f4f6;
      color: #94a3b8;
      cursor: default;
    }

    .select-wrapper { position: relative; }
    .select-wrapper .form-select-kuning { padding-right: 44px; }

    .select-chevron {
      position: absolute;
      right: 0; top: 0;
      width: 44px; height: 48px;
      background: #efb100;
      border-radius: 0 10px 10px 0;
      display: flex;
      align-items: center;
      justify-content: center;
      pointer-events: none;
    }

    /* Toggle Akses Login */
    .toggle-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 4px 0;
    }

    .toggle-label-text { font-size: 14px; font-weight: 500; color: #1a1a1a; }

    .toggle-switch { position: relative; width: 52px; height: 28px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }

    .toggle-track {
      position: absolute;
      inset: 0;
      background: #d1d5db;
      border-radius: 999px;
      transition: background 0.2s;
      cursor: pointer;
    }

    .toggle-thumb {
      position: absolute;
      top: 3px; left: 3px;
      width: 22px; height: 22px;
      background: #fff;
      border-radius: 50%;
      transition: transform 0.2s;
      box-shadow: 0 1px 4px rgba(0,0,0,0.2);
      pointer-events: none;
    }

    .toggle-switch input:checked + .toggle-track { background: #22c55e; }
    .toggle-switch input:checked + .toggle-track .toggle-thumb { transform: translateX(24px); }

    /* Footer fixed */
    .form-page-footer {
      position: fixed;
      bottom: 0; left: 0; right: 0;
      display: flex;
      gap: 12px;
      padding: 14px 20px;
      background: #fff;
      border-top: 1px solid #f0f0f0;
      z-index: 50;
    }

    .btn-form-batal {
      flex: 1;
      padding: 13px;
      border: 1.5px solid #d1d5db;
      border-radius: 12px;
      background: #fff;
      color: #94a3b8;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-form-batal:hover { border-color: #94a3b8; color: #555; }

    .btn-form-simpan {
      flex: 2;
      padding: 13px;
      border: none;
      border-radius: 12px;
      background: #efb100;
      color: #fff;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.2s;
    }

    .btn-form-simpan:hover { background: #d9a000; }
  </style>
</head>
<body>
  <header class="form-page-header">
    <button class="btn-back" id="btn-back" aria-label="Kembali">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <h1 class="form-page-title">Edit Data Karyawan</h1>
  </header>

  <p class="form-page-subtitle">Silakan Sesuaikan Perubahan yang Anda Inginkan</p>

  <main class="form-page-body">

    <!-- ============================================================
         FOTO EDIT — tampilkan foto existing dengan tombol ganti/hapus
         src foto diisi JS dari query param atau fallback placeholder
    ============================================================ -->
    <div class="foto-edit-card">
      <p class="foto-edit-label">Detail Gambar</p>
      <img
        src="https://i.pravatar.cc/400?img=5"
        alt="Foto Karyawan"
        class="foto-edit-img"
        id="foto-edit-img"
      >
      <div class="foto-edit-actions">
        <!-- Tombol edit foto → trigger input file -->
        <button class="btn-foto-edit" id="btn-ganti-foto" title="Ganti Foto">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        <!-- Tombol hapus foto → kembali ke placeholder -->
        <button class="btn-foto-hapus" id="btn-hapus-foto" title="Hapus Foto">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M3 6h18M8 6V4h8v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
      <!-- Input file tersembunyi untuk ganti foto -->
      <input type="file" class="input-foto-hidden" id="input-foto-edit" accept="image/jpg,image/jpeg,image/png">
    </div>

    <hr class="foto-form-divider">

    <!-- ID — readonly -->
    <div class="form-group">
      <label class="form-label" for="edit-id">ID</label>
      <input type="text" class="form-input-kuning" id="edit-id" readonly>
    </div>

    <!-- Nama Lengkap -->
    <div class="form-group">
      <label class="form-label" for="edit-nama">Nama Lengkap</label>
      <input type="text" class="form-input-kuning" id="edit-nama" placeholder="Masukkan Nama Lengkap">
    </div>

    <!-- Jabatan -->
    <div class="form-group">
      <label class="form-label" for="edit-jabatan">Jabatan</label>
      <div class="select-wrapper">
        <select class="form-select-kuning" id="edit-jabatan">
          <option value="" disabled>Pilih Jabatan</option>
          <option value="admin">Admin</option>
          <option value="kasir">Kasir</option>
          <option value="koki">Koki</option>
        </select>
        <div class="select-chevron">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M6 9L12 15L18 9" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Nomor Telepon -->
    <div class="form-group">
      <label class="form-label" for="edit-telp">Nomor Telepon</label>
      <input type="tel" class="form-input-kuning" id="edit-telp" placeholder="Masukkan Nomor Telepon">
    </div>

    <!-- Email -->
    <div class="form-group">
      <label class="form-label" for="edit-email">Email</label>
      <input type="email" class="form-input-kuning" id="edit-email" placeholder="Masukkan Email">
    </div>

    <!-- Alamat -->
    <div class="form-group">
      <label class="form-label" for="edit-alamat">Alamat</label>
      <input type="text" class="form-input-kuning" id="edit-alamat" placeholder="Masukkan Alamat">
    </div>

    <!-- Toggle Akses Login -->
    <div class="toggle-row">
      <span class="toggle-label-text">Akses Login</span>
      <label class="toggle-switch">
        <input type="checkbox" id="toggle-akses">
        <div class="toggle-track">
          <div class="toggle-thumb"></div>
        </div>
      </label>
    </div>

  </main>

  <footer class="form-page-footer">
    <button class="btn-form-batal" id="btn-batal">Batal</button>
    <button class="btn-form-simpan" id="btn-simpan">Simpan</button>
  </footer>

  <script>
        /* ============================================================
       AMBIL DATA DARI QUERY PARAM → ISI KE FORM
       Saat ini data datang dari karyawan.js via URLSearchParams.
       Di Laravel nanti: data diisi langsung dari Blade.
    ============================================================ */
    const params = new URLSearchParams(window.location.search);

    document.getElementById('edit-id').value    = params.get('id')      || '';
    document.getElementById('edit-nama').value  = params.get('nama')    || '';
    document.getElementById('edit-telp').value  = params.get('telp')    || '';
    document.getElementById('edit-email').value = params.get('email')   || '';
    document.getElementById('edit-alamat').value = params.get('alamat') || '';

    // Set nilai select jabatan
    const jabatanParam = params.get('jabatan') || '';
    const selectJabatan = document.getElementById('edit-jabatan');
    if (jabatanParam) {
      // Cari option yang value-nya cocok (case-insensitive)
      const option = [...selectJabatan.options].find(
        o => o.value.toLowerCase() === jabatanParam.toLowerCase()
      );
      if (option) option.selected = true;
    }

    /* ============================================================
       GANTI FOTO — klik tombol edit → buka input file
    ============================================================ */
    const inputFotoEdit = document.getElementById('input-foto-edit');
    const fotoEditImg   = document.getElementById('foto-edit-img');

    document.getElementById('btn-ganti-foto')?.addEventListener('click', () => {
      inputFotoEdit.click();
    });

    inputFotoEdit?.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => { fotoEditImg.src = e.target.result; };
      reader.readAsDataURL(file);
    });

    /* ============================================================
       HAPUS FOTO — kembalikan ke placeholder
    ============================================================ */
    document.getElementById('btn-hapus-foto')?.addEventListener('click', () => {
      fotoEditImg.src = 'https://via.placeholder.com/400x300?text=No+Photo';
      inputFotoEdit.value = '';
    });

    /* ============================================================
       PENTING: SESUAIKAN NAVIGASI KEMBALI MENGGUNAKAN ROUTE LARAVEL
    ============================================================ */
    document.getElementById('btn-back')?.addEventListener('click', () => {
      window.location.href = "{{ route('admin.karyawan') }}";
    });

    document.getElementById('btn-batal')?.addEventListener('click', () => {
      window.location.href = "{{ route('admin.karyawan') }}";
    });

    /* Tombol Simpan (Simulasi) */
    document.getElementById('btn-simpan')?.addEventListener('click', () => {
      const nama    = document.getElementById('input-nama').value.trim();
      const jabatan = document.getElementById('input-jabatan').value;
      const telp    = document.getElementById('input-telp').value.trim();
      const email   = document.getElementById('input-email').value.trim();

      if (!nama || !jabatan || !telp || !email) {
        alert('Mohon lengkapi semua field yang wajib diisi.');
        return;
      }

      alert('Data karyawan berhasil ditambahkan (simulasi).');
      window.location.href = "{{ route('admin.karyawan') }}";
    });
  </script>
</body>
</html>