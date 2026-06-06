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
    /* ============================================================
       PAGE TAMBAH KARYAWAN — mobile full page
       Style khusus halaman ini, tidak perlu masuk admin.css
       karena hanya dipakai di satu halaman ini
    ============================================================ */

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: "Poppins", sans-serif;
      background: #fff;
      color: #1a1a1a;
      min-height: 100vh;
      /* Padding bottom untuk footer fixed */
      padding-bottom: 90px;
    }

    /* ---- HEADER ---- */
    .form-page-header {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      padding: 16px 20px;
      border-bottom: 1px solid #f0f0f0;
      background: #fff;
    }

    /* Tombol back arrow kiri */
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

    .form-page-title {
      font-size: 17px;
      font-weight: 700;
      color: #1a1a1a;
      text-align: center;
    }

    /* ---- SUBTITLE ---- */
    .form-page-subtitle {
      text-align: center;
      font-size: 13px;
      color: #94a3b8;
      padding: 12px 24px 0;
      line-height: 1.5;
    }

    /* ---- BODY / FORM ---- */
    .form-page-body {
      padding: 20px 20px 0;
      display: flex;
      flex-direction: column;
      gap: 18px;
    }

    /* ---- UPLOAD FOTO ---- */
    /*
      Area upload foto dengan dashed border.
      Saat foto sudah dipilih, .upload-foto-area.has-foto
      menampilkan preview gambar dan sembunyikan teks.
    */
    .upload-foto-area {
      border: 2px dashed #d1d5db;
      border-radius: 16px;
      padding: 28px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      background: #f9fafb;
      transition: border-color 0.2s, background 0.2s;
      position: relative;
      overflow: hidden;
    }

    .upload-foto-area:hover {
      border-color: #efb100;
      background: #fffbeb;
    }

    .upload-foto-area.has-foto {
      padding: 0;
      border-color: #efb100;
    }

    .upload-foto-icon {
      width: 48px;
      height: 48px;
      color: #94a3b8;
    }

    .upload-foto-label {
      font-size: 14px;
      font-weight: 700;
      color: #1a1a1a;
    }

    .upload-foto-hint {
      font-size: 12px;
      color: #94a3b8;
      text-align: center;
    }

    /* Preview foto setelah dipilih */
    .upload-foto-preview {
      display: none;
      width: 100%;
      aspect-ratio: 4/3;
      object-fit: cover;
      border-radius: 14px;
    }

    .upload-foto-area.has-foto .upload-foto-preview { display: block; }
    .upload-foto-area.has-foto .upload-foto-icon,
    .upload-foto-area.has-foto .upload-foto-label,
    .upload-foto-area.has-foto .upload-foto-hint { display: none; }

    /* Input file tersembunyi */
    .upload-foto-input {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }

    /* ---- FORM FIELDS ---- */
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .form-label {
      font-size: 13px;
      font-weight: 600;
      color: #1a1a1a;
    }

    /*
      Input dan select dengan border kuning sesuai desain.
      Readonly field (ID) punya background abu-abu.
    */
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

    .form-input-kuning[readonly] {
      background: #f3f4f6;
      color: #94a3b8;
      cursor: default;
    }

    /* Wrapper select untuk custom chevron */
    .select-wrapper {
      position: relative;
    }

    .select-wrapper .form-select-kuning {
      padding-right: 44px;
    }

    /* Chevron kuning kustom untuk select */
    .select-chevron {
      position: absolute;
      right: 0;
      top: 0;
      width: 44px;
      height: 48px;
      background: #efb100;
      border-radius: 0 10px 10px 0;
      display: flex;
      align-items: center;
      justify-content: center;
      pointer-events: none;
    }

    /* ---- TOGGLE AKSES LOGIN ---- */
    .toggle-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 4px 0;
    }

    .toggle-label-text {
      font-size: 14px;
      font-weight: 500;
      color: #1a1a1a;
    }

    /* Custom toggle switch */
    .toggle-switch {
      position: relative;
      width: 52px;
      height: 28px;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
      position: absolute;
    }

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
      top: 3px;
      left: 3px;
      width: 22px;
      height: 22px;
      background: #fff;
      border-radius: 50%;
      transition: transform 0.2s;
      box-shadow: 0 1px 4px rgba(0,0,0,0.2);
      pointer-events: none;
    }

    .toggle-switch input:checked + .toggle-track { background: #22c55e; }
    .toggle-switch input:checked + .toggle-track .toggle-thumb { transform: translateX(24px); }

    /* ---- FOOTER FIXED ---- */
    /*
      Footer tombol Batal & Simpan, fixed di bagian bawah layar.
      Sama seperti desain mobile pada gambar.
    */
    .form-page-footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
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

    /* Disabled state Simpan (sebelum form diisi) */
    .btn-form-simpan:disabled {
      background: #d1d5db;
      cursor: not-allowed;
    }
  </style>
</head>
<body>
  <header class="form-page-header">
    <button class="btn-back" id="btn-back" aria-label="Kembali">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <h1 class="form-page-title">Tambah Karyawan Baru</h1>
  </header>

  <p class="form-page-subtitle">Isi Kolom di Bawah untuk Menambahkan<br>Karyawan Baru</p>

  <!-- ============================================================
       FORM BODY
  ============================================================ -->
  <main class="form-page-body">

    <!--
      UPLOAD FOTO
      Klik area → input file terbuka.
      Saat file dipilih, JS tambah class .has-foto dan isi src preview.
    -->
    <div class="upload-foto-area" id="upload-area">
      <input
        type="file"
        class="upload-foto-input"
        id="input-foto"
        accept="image/jpg,image/jpeg,image/png"
      >
      <svg class="upload-foto-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M32 32L24 24L16 32" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M24 24V40" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M40.82 36.82A10 10 0 0034 18h-2.52A16 16 0 108 36.92" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <p class="upload-foto-label">Unggah Foto Wajah</p>
      <p class="upload-foto-hint">Klik atau seret gambar produk (JPG or PNG).</p>
      <img src="" alt="Preview" class="upload-foto-preview" id="foto-preview">
    </div>

    <!-- ID Karyawan — auto-generated, readonly -->
    <div class="form-group">
      <label class="form-label" for="input-id">ID</label>
      <input
        type="text"
        class="form-input-kuning"
        id="input-id"
        value="EMP009"
        readonly
      >
    </div>

    <!-- Nama Lengkap -->
    <div class="form-group">
      <label class="form-label" for="input-nama">Nama Lengkap</label>
      <input
        type="text"
        class="form-input-kuning"
        id="input-nama"
        placeholder="Masukkan Nama Lengkap"
      >
    </div>

    <!-- Jabatan -->
    <div class="form-group">
      <label class="form-label" for="input-jabatan">Jabatan</label>
      <div class="select-wrapper">
        <select class="form-select-kuning" id="input-jabatan">
          <option value="" disabled selected>Pilih Jabatan</option>
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
      <label class="form-label" for="input-telp">Nomor Telepon</label>
      <input
        type="tel"
        class="form-input-kuning"
        id="input-telp"
        placeholder="Masukkan Nomor Telepon"
      >
    </div>

    <!-- Email -->
    <div class="form-group">
      <label class="form-label" for="input-email">Email</label>
      <input
        type="email"
        class="form-input-kuning"
        id="input-email"
        placeholder="Masukkan Email"
      >
    </div>

    <!-- Alamat -->
    <div class="form-group">
      <label class="form-label" for="input-alamat">Alamat</label>
      <input
        type="text"
        class="form-input-kuning"
        id="input-alamat"
        placeholder="Masukkan alamat"
      >
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

  <!-- ============================================================
       FOOTER FIXED — Batal + Simpan
  ============================================================ -->
  <footer class="form-page-footer">
    <button class="btn-form-batal" id="btn-batal">Batal</button>
    <button class="btn-form-simpan" id="btn-simpan">Simpan</button>
  </footer>
  
  <script>
        /* ============================================================
       UPLOAD FOTO — preview saat file dipilih
    ============================================================ */
    const inputFoto  = document.getElementById('input-foto');
    const uploadArea = document.getElementById('upload-area');
    const fotoPreview = document.getElementById('foto-preview');

    inputFoto?.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (e) {
        fotoPreview.src = e.target.result;
        uploadArea.classList.add('has-foto');
      };
      reader.readAsDataURL(file);
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