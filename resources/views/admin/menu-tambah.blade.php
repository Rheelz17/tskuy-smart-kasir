<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Tambah Menu — Tskuy Admin</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  {{-- CSS global admin (sama dengan karyawan-tambah) --}}
  <link rel="stylesheet" href="{{ asset('css/admincopy.css') }}" />

  <style>
    /* ============================================================
       BASE — identik dengan karyawan-tambah.blade.php
    ============================================================ */
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: "Poppins", sans-serif;
      background: #fff;
      color: #1a1a1a;
      min-height: 100vh;
      padding-bottom: 90px; /* ruang untuk footer fixed */
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

    /* ---- FORM BODY ---- */
    .form-page-body {
      padding: 20px 20px 0;
      display: flex;
      flex-direction: column;
      gap: 18px;
    }

    /* ---- SECTION DIVIDER ---- */
    .section-divider {
      border: none;
      border-top: 1px solid #f0f0f0;
      margin: 4px 0 -4px;
    }

    .section-title-mini {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .06em;
      text-transform: uppercase;
      color: #94a3b8;
      margin-bottom: -6px;
    }

    /* ---- UPLOAD FOTO ---- */
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
      transition: border-color .2s, background .2s;
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

    .upload-foto-icon { width: 48px; height: 48px; color: #94a3b8; }

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

    .upload-foto-preview {
      display: none;
      width: 100%;
      aspect-ratio: 16/9;
      object-fit: cover;
      border-radius: 14px;
    }

    .upload-foto-area.has-foto .upload-foto-preview  { display: block; }
    .upload-foto-area.has-foto .upload-foto-icon,
    .upload-foto-area.has-foto .upload-foto-label,
    .upload-foto-area.has-foto .upload-foto-hint    { display: none; }

    .upload-foto-input {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }

    /* ---- FORM FIELDS (identik karyawan) ---- */
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

    .form-label .label-optional {
      font-size: 11px;
      font-weight: 400;
      color: #94a3b8;
      margin-left: 4px;
    }

    .form-input-kuning,
    .form-select-kuning,
    .form-textarea-kuning {
      width: 100%;
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

    /* Tinggi standar untuk input & select */
    .form-input-kuning,
    .form-select-kuning { height: 48px; }

    /* Textarea punya padding vertikal */
    .form-textarea-kuning {
      height: auto;
      padding: 12px 14px;
      resize: none;
      line-height: 1.5;
    }

    .form-input-kuning:focus,
    .form-select-kuning:focus,
    .form-textarea-kuning:focus {
      border-color: #d9a000;
      box-shadow: 0 0 0 3px rgba(239,177,0,.12);
    }

    .form-input-kuning::placeholder,
    .form-textarea-kuning::placeholder { color: #bbb; }

    /* ID field: readonly / auto-generate */
    .form-input-kuning[readonly] {
      background: #f3f4f6;
      color: #94a3b8;
      cursor: default;
    }

    /* Input angka — hilangkan spin arrows */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; }
    input[type="number"] { -moz-appearance: textfield; }

    /* Wrapper select + chevron kuning kustom */
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

    /* ---- ROW DUA KOLOM (Stok & Harga berdampingan) ---- */
    .form-row-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    /* ---- MOOD CHECKBOXES ---- */
    /*
      Mood ditampilkan sebagai chip/pill yang bisa dipilih banyak.
      Saat dicentang → background kuning muda + border kuning.
    */
    .mood-chips {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .mood-chip-label {
      cursor: pointer;
    }

    .mood-chip-label input[type="checkbox"] {
      display: none; /* sembunyikan checkbox asli */
    }

    .mood-chip-text {
      display: inline-flex;
      align-items: center;
      padding: 6px 14px;
      border-radius: 20px;
      border: 1.5px solid #e5e7eb;
      font-size: 13px;
      font-weight: 500;
      color: #555;
      background: #f9fafb;
      transition: all .18s;
      user-select: none;
    }

    .mood-chip-label:hover .mood-chip-text {
      border-color: #efb100;
      color: #92400e;
    }

    /* State: dicentang → style aktif */
    .mood-chip-label input[type="checkbox"]:checked + .mood-chip-text {
      background: #fef3c7;
      border-color: #efb100;
      color: #92400e;
      font-weight: 600;
    }

    /* ---- FLAG CHECKBOXES (Rekomendasi / Baru / Promo) ---- */
    .flag-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .flag-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 14px;
      border: 1.5px solid #f0f0f0;
      border-radius: 10px;
      background: #fafafa;
      transition: border-color .2s;
    }

    .flag-row:has(input:checked) {
      border-color: #efb100;
      background: #fffbeb;
    }

    .flag-info {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .flag-name {
      font-size: 13.5px;
      font-weight: 600;
      color: #1a1a1a;
    }

    .flag-desc {
      font-size: 11.5px;
      color: #94a3b8;
    }

    /* Toggle switch — identik karyawan-tambah */
    .toggle-switch {
      position: relative;
      width: 52px;
      height: 28px;
      flex-shrink: 0;
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
      transition: background .2s;
      cursor: pointer;
    }

    .toggle-thumb {
      position: absolute;
      top: 3px; left: 3px;
      width: 22px; height: 22px;
      background: #fff;
      border-radius: 50%;
      transition: transform .2s;
      box-shadow: 0 1px 4px rgba(0,0,0,.2);
      pointer-events: none;
    }

    .toggle-switch input:checked + .toggle-track              { background: #22c55e; }
    .toggle-switch input:checked + .toggle-track .toggle-thumb { transform: translateX(24px); }

    /* ---- TOGGLE BARIS TERSEDIA — sama persis karyawan ---- */
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

    /* ---- FOOTER FIXED ---- */
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
      font-family: "Poppins", sans-serif;
      transition: all .2s;
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
      font-family: "Poppins", sans-serif;
      transition: background .2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-form-simpan:hover { background: #d9a000; }

    .btn-form-simpan:disabled {
      background: #d1d5db;
      cursor: not-allowed;
    }

    /* Spinner loading di tombol simpan */
    .btn-spinner {
      display: none;
      width: 16px; height: 16px;
      border: 2px solid rgba(255,255,255,.4);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin .6s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .btn-form-simpan.loading .btn-spinner    { display: block; }
    .btn-form-simpan.loading .btn-simpan-txt { opacity: .6; }

    /* ---- KARTU PREVIEW HARGA ---- */
    /*
      Live preview harga diformat ke Rupiah saat user mengetik.
    */
    .harga-preview {
      font-size: 11.5px;
      color: #22c55e;
      font-weight: 600;
      padding: 2px 0 0 2px;
      min-height: 18px;
      display: block;
    }

    /* ---- TOAST NOTIF ---- */
    .toast-mobile {
      position: fixed;
      bottom: 90px; left: 20px; right: 20px;
      padding: 13px 16px;
      border-radius: 12px;
      font-size: 13.5px;
      font-weight: 600;
      color: #fff;
      z-index: 999;
      display: none;
      align-items: center;
      gap: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,.15);
      animation: toast-in .3s ease;
    }

    @keyframes toast-in {
      from { opacity: 0; transform: translateY(12px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .toast-mobile.show  { display: flex; }
    .toast-mobile.sukses { background: #16a34a; }
    .toast-mobile.error  { background: #dc2626; }

    /* ============================================================
       RESPONSIVE — Desktop: tampilkan layout 2 kolom (foto kiri)
       Di bawah 768px: mobile, 1 kolom vertikal
    ============================================================ */
    @media (min-width: 768px) {
      body {
        background: #f3f4f6;
        padding-bottom: 0;
      }

      .form-page-header {
        position: relative;
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        max-width: 780px;
        margin: 0 auto;
        border-radius: 0;
      }

      .form-page-subtitle { max-width: 780px; margin: 0 auto; }

      .form-page-body {
        max-width: 780px;
        margin: 0 auto;
        padding: 24px 24px 32px;
        background: #fff;
        border-radius: 0 0 16px 16px;
        gap: 20px;
      }

      /* Layout 2 kolom di desktop: foto kiri, form kanan */
      .form-desktop-layout {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 24px;
        align-items: start;
      }

      .upload-foto-area {
        aspect-ratio: 4/3;
        padding: 20px;
      }

      .upload-foto-preview {
        aspect-ratio: 4/3;
      }

      .form-page-footer {
        position: static;
        max-width: 780px;
        margin: 16px auto 40px;
        border: none;
        background: transparent;
        padding: 0 24px;
      }
    }
  </style>
</head>
<body>

  {{-- ═══════════════ HEADER ═══════════════ --}}
  <header class="form-page-header">
    <button class="btn-back" id="btn-back" type="button" aria-label="Kembali">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M5 12L12 19M5 12L12 5"
              stroke="currentColor" stroke-width="2.2"
              stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <h1 class="form-page-title">Tambah Menu Baru</h1>
  </header>

  <p class="form-page-subtitle">Isi kolom di bawah untuk menambahkan<br>menu kuliner baru ke katalog</p>

  {{-- ═══════════════ FORM ═══════════════ --}}
  <form id="form-tambah-menu">
    @csrf

    <main class="form-page-body">

      {{-- ── Layout wrapper desktop (foto kiri | form kanan) ── --}}
      <div class="form-desktop-layout">

        {{-- ══ KOLOM KIRI: UPLOAD FOTO ══ --}}
        <div class="upload-foto-area" id="upload-area">
          {{--
            Input file tersembunyi (cover seluruh area).
            Accept: jpg, jpeg, png, webp — sesuai validasi controller.
          --}}
          <input
            type="file"
            class="upload-foto-input"
            id="input-foto"
            name="image"
            accept="image/jpg,image/jpeg,image/png,image/webp"
          >

          {{-- Ikon upload --}}
          <svg class="upload-foto-icon" viewBox="0 0 48 48" fill="none">
            <path d="M32 32L24 24L16 32" stroke="#94a3b8" stroke-width="2.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M24 24V40" stroke="#94a3b8" stroke-width="2.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M40.82 36.82A10 10 0 0034 18h-2.52A16 16 0 108 36.92"
                  stroke="#94a3b8" stroke-width="2.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
          </svg>

          <p class="upload-foto-label">Unggah Foto Menu</p>
          <p class="upload-foto-hint">Klik atau seret foto produk ke sini<br>(JPG / PNG / WEBP, maks 2 MB)</p>

          {{-- Preview setelah gambar dipilih --}}
          <img src="" alt="Preview" class="upload-foto-preview" id="foto-preview">
        </div>

        {{-- ══ KOLOM KANAN: FIELD-FIELD FORM ══ --}}
        <div class="form-fields-col">

          {{-- ── ID Menu (auto, readonly) ── --}}
          <div class="form-group">
            <label class="form-label" for="input-id">ID Menu</label>
            <input
              type="text"
              class="form-input-kuning"
              id="input-id"
              value="Otomatis"
              readonly
            >
          </div>

          {{-- ── Nama Menu ── --}}
          <div class="form-group">
            <label class="form-label" for="input-nama">Nama Menu</label>
            <input
              type="text"
              class="form-input-kuning"
              id="input-nama"
              name="name"
              placeholder="Contoh: Nasi Goreng Tskuy"
              required
              autocomplete="off"
            >
          </div>

          {{-- ── Deskripsi ── --}}
          <div class="form-group">
            <label class="form-label" for="input-deskripsi">
              Deskripsi
              <span class="label-optional">(opsional)</span>
            </label>
            <textarea
              class="form-textarea-kuning"
              id="input-deskripsi"
              name="description"
              rows="3"
              placeholder="Ceritakan singkat rasa, porsi, atau bahan istimewa menu ini..."
            ></textarea>
          </div>

          {{-- ── Kategori ── --}}
          <div class="form-group">
            <label class="form-label" for="input-kategori">Kategori</label>
            <div class="select-wrapper">
              <select
                class="form-select-kuning"
                id="input-kategori"
                name="category_id"
                required
              >
                <option value="" disabled selected>Pilih Kategori</option>
                {{--
                  Iterasi $categories dikirim dari AdminMenuController@tambah.
                  Setiap category → id & name dari tabel categories.
                --}}
                @foreach($categories as $category)
                  <option value="{{ $category->id }}">
                    {{ ucfirst($category->name) }}
                  </option>
                @endforeach
              </select>
              <div class="select-chevron">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M6 9L12 15L18 9" stroke="#fff"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
          </div>

          {{-- ── Stok & Harga (2 kolom) ── --}}
          <div class="form-row-2">
            <div class="form-group">
              <label class="form-label" for="input-stok">Stok Porsi</label>
              <input
                type="number"
                class="form-input-kuning"
                id="input-stok"
                name="stock"
                min="0"
                placeholder="Contoh: 50"
                required
              >
            </div>

            <div class="form-group">
              <label class="form-label" for="input-harga">Harga (Rp)</label>
              <input
                type="number"
                class="form-input-kuning"
                id="input-harga"
                name="price"
                min="0"
                placeholder="Contoh: 15000"
                required
              >
              {{-- Live preview format Rupiah --}}
              <span class="harga-preview" id="harga-preview"></span>
            </div>
          </div>

        </div>
        {{-- /form-fields-col --}}

      </div>
      {{-- /form-desktop-layout --}}

      {{-- ════════════════════════════════════════════
           SEKSI MOOD — relasi many-to-many ke tabel moods
           via pivot menu_moods.
           Sumber data: $moods dari AdminMenuController@tambah
      ════════════════════════════════════════════ --}}
      <hr class="section-divider">
      <span class="section-title-mini">Mood / Suasana</span>

      <div class="form-group">
        <label class="form-label">
          Cocok untuk Mood
          <span class="label-optional">(bisa pilih lebih dari satu)</span>
        </label>

        <div class="mood-chips">
          @forelse($moods as $mood)
            <label class="mood-chip-label">
              {{--
                name="moods[]" — array checkbox.
                Controller: $menu->moods()->sync($request->moods)
              --}}
              <input
                type="checkbox"
                name="moods[]"
                value="{{ $mood->id }}"
              >
              <span class="mood-chip-text">{{ $mood->name }}</span>
            </label>
          @empty
            <p style="font-size:13px;color:#94a3b8;">Belum ada data mood tersedia.</p>
          @endforelse
        </div>
      </div>

      {{-- ════════════════════════════════════════════
           SEKSI FLAG / LABEL — is_recommended, is_new, is_promo
           Checkbox boolean, dikirim sebagai has() check di controller.
      ════════════════════════════════════════════ --}}
      <hr class="section-divider">
      <span class="section-title-mini">Label & Sorotan</span>

      <div class="flag-list">

        <div class="flag-row">
          <div class="flag-info">
            <span class="flag-name">Rekomendasi</span>
            <span class="flag-desc">Tampilkan di bagian "Direkomendasikan" halaman kasir</span>
          </div>
          <label class="toggle-switch">
            <input type="checkbox" name="is_recommended" value="1" id="toggle-rekomendasi">
            <div class="toggle-track">
              <div class="toggle-thumb"></div>
            </div>
          </label>
        </div>

        <div class="flag-row">
          <div class="flag-info">
            <span class="flag-name">Menu Baru</span>
            <span class="flag-desc">Tampilkan badge "Baru" di kartu menu kasir & pelanggan</span>
          </div>
          <label class="toggle-switch">
            <input type="checkbox" name="is_new" value="1" id="toggle-baru">
            <div class="toggle-track">
              <div class="toggle-thumb"></div>
            </div>
          </label>
        </div>

        <div class="flag-row">
          <div class="flag-info">
            <span class="flag-name">Sedang Promo</span>
            <span class="flag-desc">Tampilkan badge "Promo" dan masuk filter promosi</span>
          </div>
          <label class="toggle-switch">
            <input type="checkbox" name="is_promo" value="1" id="toggle-promo">
            <div class="toggle-track">
              <div class="toggle-thumb"></div>
            </div>
          </label>
        </div>

      </div>

      {{-- ════════════════════════════════════════════
           TOGGLE KETERSEDIAAN (Aktif/Nonaktif)
           is_available → tampil di kasir & pelanggan
      ════════════════════════════════════════════ --}}
      <hr class="section-divider">

      <div class="toggle-row">
        <div>
          <span class="toggle-label-text">Tersedia (Aktif)</span>
          <p style="font-size:12px;color:#94a3b8;margin-top:2px;">
            Menu langsung bisa dipesan saat disimpan
          </p>
        </div>
        <label class="toggle-switch">
          {{-- Default: checked (aktif saat pertama kali dibuat) --}}
          <input type="checkbox" id="toggle-tersedia" name="is_available" value="1" checked>
          <div class="toggle-track">
            <div class="toggle-thumb"></div>
          </div>
        </label>
      </div>

    </main>

    {{-- ═══════════════ FOOTER TOMBOL ═══════════════ --}}
    <footer class="form-page-footer">
      <button type="button" class="btn-form-batal" id="btn-batal">Batal</button>
      <button type="submit" class="btn-form-simpan" id="btn-simpan">
        <span class="btn-spinner" id="btn-spinner"></span>
        <span class="btn-simpan-txt">Simpan Menu</span>
      </button>
    </footer>

  </form>

  {{-- Toast notifikasi mobile --}}
  <div class="toast-mobile" id="toast-notif">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" id="toast-icon">
      <path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span id="toast-pesan">Berhasil!</span>
  </div>

  <script>
    /* ============================================================
       1. NAVIGASI KEMBALI
       Menggunakan route() Laravel agar tidak hardcode URL.
    ============================================================ */
    const kembaliKeMenu = () => {
      window.location.href = "{{ route('admin.menu') }}";
    };

    document.getElementById('btn-back')?.addEventListener('click', kembaliKeMenu);
    document.getElementById('btn-batal')?.addEventListener('click', kembaliKeMenu);

    /* ============================================================
       2. PREVIEW FOTO — saat user pilih file
    ============================================================ */
    const inputFoto   = document.getElementById('input-foto');
    const uploadArea  = document.getElementById('upload-area');
    const fotoPreview = document.getElementById('foto-preview');

    inputFoto?.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      // Validasi ukuran: maks 2 MB
      if (file.size > 2 * 1024 * 1024) {
        showToast('Ukuran foto melebihi 2 MB. Pilih file yang lebih kecil.', 'error');
        this.value = '';
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        fotoPreview.src = e.target.result;
        uploadArea.classList.add('has-foto');
      };
      reader.readAsDataURL(file);
    });

    /* ============================================================
       3. LIVE PREVIEW FORMAT HARGA RUPIAH
    ============================================================ */
    const inputHarga  = document.getElementById('input-harga');
    const hargaPreview = document.getElementById('harga-preview');

    inputHarga?.addEventListener('input', function () {
      const angka = parseInt(this.value, 10);
      if (!isNaN(angka) && angka >= 0) {
        hargaPreview.textContent = 'Rp ' + angka.toLocaleString('id-ID');
      } else {
        hargaPreview.textContent = '';
      }
    });

    /* ============================================================
       4. TOAST HELPER
    ============================================================ */
    function showToast(pesan, tipe = 'sukses') {
      const toast = document.getElementById('toast-notif');
      const pesanEl = document.getElementById('toast-pesan');
      const ikonEl  = document.getElementById('toast-icon');

      if (!toast || !pesanEl) return;

      pesanEl.textContent = pesan;
      toast.className = 'toast-mobile show ' + tipe;

      // Ikon sukses vs error
      if (tipe === 'sukses') {
        ikonEl.innerHTML = '<path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>';
      } else {
        ikonEl.innerHTML = '<circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.5" fill="none"/><path d="M15 9l-6 6M9 9l6 6" stroke="#fff" stroke-width="2" stroke-linecap="round"/>';
      }

      // Sembunyikan otomatis setelah 3.5 detik
      setTimeout(() => {
        toast.classList.remove('show');
      }, 3500);
    }

    /* ============================================================
       5. SUBMIT FORM via Fetch API
          Identik dengan karyawan-tambah: pakai FormData,
          kirim ke route admin.menu.store, tangani validasi Laravel.
    ============================================================ */
    document.getElementById('form-tambah-menu')?.addEventListener('submit', function (e) {
      e.preventDefault();

      const btn     = document.getElementById('btn-simpan');
      const spinner = document.getElementById('btn-spinner');

      // State loading
      btn.disabled = true;
      btn.classList.add('loading');

      // FormData otomatis ambil semua field + file gambar
      const formData = new FormData(this);

      fetch("{{ route('admin.menu.store') }}", {
        method : 'POST',
        body   : formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').content,
        }
      })
      .then(async (response) => {
        const data = await response.json();

        // Validasi gagal (422 Laravel)
        if (!response.ok) {
          btn.disabled = false;
          btn.classList.remove('loading');

          if (data.errors) {
            const pesanEror = Object.values(data.errors).flat().join('\n');
            showToast(pesanEror.split('\n')[0], 'error'); // Tampilkan eror pertama di toast
          } else {
            showToast(data.message || 'Terjadi kesalahan pada server.', 'error');
          }
          return;
        }

        // Berhasil disimpan
        if (data.success) {
          showToast(data.message || 'Menu baru berhasil ditambahkan!', 'sukses');
          // Redirect ke halaman daftar menu setelah toast tampil
          setTimeout(() => {
            window.location.href = "{{ route('admin.menu') }}";
          }, 1200);
        } else {
          btn.disabled = false;
          btn.classList.remove('loading');
          showToast('Gagal menyimpan menu. Coba lagi.', 'error');
        }
      })
      .catch((error) => {
        console.error('Fetch error:', error);
        btn.disabled = false;
        btn.classList.remove('loading');
        showToast('Koneksi bermasalah atau terjadi error sistem.', 'error');
      });
    });
  </script>

</body>
</html>