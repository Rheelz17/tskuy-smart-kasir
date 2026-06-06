<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit Menu — Tskuy Admin</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/admincopy.css') }}" />

  <style>
    /* ============================================================
       BASE — identik dengan menu-tambah & karyawan-edit
    ============================================================ */
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: "Poppins", sans-serif;
      background: #fff;
      color: #1a1a1a;
      min-height: 100vh;
      padding-bottom: 90px;
    }

    /* ---- HEADER ---- */
    .form-page-header {
      display: flex;
      align-items: center;
      justify-content: center;
      position: sticky;
      top: 0;
      z-index: 20;
      padding: 16px 20px;
      border-bottom: 1px solid #f0f0f0;
      background: #fff;
    }

    .btn-back {
      position: absolute;
      left: 20px;
      width: 36px; height: 36px;
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

    /* ============================================================
       SECTION FOTO EDIT — Berbeda dengan tambah:
       Foto sudah ada → tampilkan foto + tombol Ganti & Hapus
       Mirip foto-edit-card di karyawan-edit.
    ============================================================ */
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
      aspect-ratio: 16/9;
      object-fit: cover;
      border-radius: 12px;
      background: #e5e7eb;
      display: block;
    }

    /* Tombol Ganti & Hapus foto di bawah gambar */
    .foto-edit-actions {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-top: 12px;
    }

    .btn-foto-edit,
    .btn-foto-hapus {
      width: 44px; height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      cursor: pointer;
      transition: opacity .2s;
    }

    .btn-foto-edit  { background: rgba(239,177,0,.15); }
    .btn-foto-hapus { background: rgba(251,44,54,.12); }
    .btn-foto-edit:hover,
    .btn-foto-hapus:hover { opacity: .7; }

    /* Label keterangan bawah aksi foto */
    .foto-edit-hint {
      text-align: center;
      font-size: 11.5px;
      color: #94a3b8;
      margin-top: 8px;
    }

    /* State: tidak ada foto / foto dihapus → tampilkan area upload */
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
    .upload-foto-label { font-size: 14px; font-weight: 700; color: #1a1a1a; }
    .upload-foto-hint-up { font-size: 12px; color: #94a3b8; text-align: center; }

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
    .upload-foto-area.has-foto .upload-foto-hint-up  { display: none; }

    .upload-foto-input {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }

    /* ---- FORM FIELDS ---- */
    .form-group { display: flex; flex-direction: column; gap: 6px; }

    .form-label { font-size: 13px; font-weight: 600; color: #1a1a1a; }

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
      transition: border-color .2s, box-shadow .2s;
      appearance: none;
    }

    .form-input-kuning,
    .form-select-kuning { height: 48px; }

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

    .form-input-kuning[readonly] {
      background: #f3f4f6;
      color: #94a3b8;
      cursor: default;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; }
    input[type="number"] { -moz-appearance: textfield; }

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

    .form-row-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .harga-preview {
      font-size: 11.5px;
      color: #22c55e;
      font-weight: 600;
      padding: 2px 0 0 2px;
      min-height: 18px;
      display: block;
    }

    /* ---- MOOD CHIPS ---- */
    .mood-chips { display: flex; flex-wrap: wrap; gap: 8px; }

    .mood-chip-label { cursor: pointer; }

    .mood-chip-label input[type="checkbox"] { display: none; }

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

    .mood-chip-label input[type="checkbox"]:checked + .mood-chip-text {
      background: #fef3c7;
      border-color: #efb100;
      color: #92400e;
      font-weight: 600;
    }

    /* ---- FLAG TOGGLES ---- */
    .flag-list { display: flex; flex-direction: column; gap: 12px; }

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

    .flag-info { display: flex; flex-direction: column; gap: 2px; }
    .flag-name { font-size: 13.5px; font-weight: 600; color: #1a1a1a; }
    .flag-desc { font-size: 11.5px; color: #94a3b8; }

    /* Toggle switch — identik karyawan */
    .toggle-switch { position: relative; width: 52px; height: 28px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
    .toggle-track { position: absolute; inset: 0; background: #d1d5db; border-radius: 999px; transition: background .2s; cursor: pointer; }
    .toggle-thumb { position: absolute; top: 3px; left: 3px; width: 22px; height: 22px; background: #fff; border-radius: 50%; transition: transform .2s; box-shadow: 0 1px 4px rgba(0,0,0,.2); pointer-events: none; }
    .toggle-switch input:checked + .toggle-track              { background: #22c55e; }
    .toggle-switch input:checked + .toggle-track .toggle-thumb { transform: translateX(24px); }

    .toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 4px 0; }
    .toggle-label-text { font-size: 14px; font-weight: 500; color: #1a1a1a; }

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

    .btn-form-simpan:hover    { background: #d9a000; }
    .btn-form-simpan:disabled { background: #d1d5db; cursor: not-allowed; }

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

    .toast-mobile.show   { display: flex; }
    .toast-mobile.sukses  { background: #16a34a; }
    .toast-mobile.error   { background: #dc2626; }

    /* ============================================================
       RESPONSIVE — Desktop layout 2 kolom
    ============================================================ */
    @media (min-width: 768px) {
      body { background: #f3f4f6; padding-bottom: 0; }

      .form-page-header {
        position: relative;
        max-width: 780px;
        margin: 0 auto;
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

      .form-desktop-layout {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 24px;
        align-items: start;
      }

      .foto-edit-card { height: 100%; }

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
    <h1 class="form-page-title">Edit Menu</h1>
  </header>

  <p class="form-page-subtitle">
    Sesuaikan informasi menu kuliner<br>
    <strong style="color:#1a1a1a;">MN-{{ sprintf('%03d', $menu->id) }} — {{ $menu->name }}</strong>
  </p>

  {{-- ═══════════════ FORM ═══════════════ --}}
  {{--
    Method spoofing PUT: Laravel tidak mendukung PUT native dari HTML form.
    Gunakan @method('PUT') + POST.
    Tapi karena kita pakai Fetch API dengan FormData, kita append _method=PUT manual di JS.
  --}}
  <form id="form-edit-menu">
    @csrf

    <main class="form-page-body">

      <div class="form-desktop-layout">

        {{-- ══ KOLOM KIRI: FOTO EDIT ══ --}}
        <div class="foto-edit-card">
          <p class="foto-edit-label">Foto Menu</p>

          {{--
            Tampilkan foto saat ini (jika ada),
            atau area upload kosong jika belum ada foto.
          --}}
          @if($menu->image)
            {{-- Foto sudah ada: tampilkan dengan tombol ganti & hapus --}}
            <img
              src="{{ asset('storage/' . $menu->image) }}"
              alt="{{ $menu->name }}"
              class="foto-edit-img"
              id="foto-edit-img"
            >
            <div class="foto-edit-actions">
              {{-- Tombol Ganti Foto --}}
              <button type="button" class="btn-foto-edit" id="btn-ganti-foto" title="Ganti Foto">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                  <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"
                        stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                        stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              {{-- Tombol Hapus Foto --}}
              <button type="button" class="btn-foto-hapus" id="btn-hapus-foto" title="Hapus Foto">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                  <path d="M3 6h18M8 6V4h8v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"
                        stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div>
            <p class="foto-edit-hint">Ganti foto atau hapus foto saat ini</p>
          @else
            {{-- Belum ada foto: tampilkan area upload --}}
            <div class="upload-foto-area" id="upload-area-edit">
              <input
                type="file"
                class="upload-foto-input"
                id="input-foto-edit"
                name="image"
                accept="image/jpg,image/jpeg,image/png,image/webp"
              >
              <svg class="upload-foto-icon" viewBox="0 0 48 48" fill="none">
                <path d="M32 32L24 24L16 32" stroke="#94a3b8" stroke-width="2.5"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M24 24V40" stroke="#94a3b8" stroke-width="2.5"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M40.82 36.82A10 10 0 0034 18h-2.52A16 16 0 108 36.92"
                      stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <p class="upload-foto-label">Unggah Foto Menu</p>
              <p class="upload-foto-hint-up">Klik atau seret foto produk ke sini (JPG / PNG / WEBP)</p>
              <img src="" alt="Preview" class="upload-foto-preview" id="foto-preview-edit">
            </div>
          @endif

          {{-- Input file tersembunyi untuk ganti foto (saat foto sudah ada) --}}
          @if($menu->image)
            <input
              type="file"
              id="input-foto-ganti"
              name="image"
              accept="image/jpg,image/jpeg,image/png,image/webp"
              style="display:none;"
            >
          @endif

          {{-- Hidden flag: tandai jika foto minta dihapus --}}
          <input type="hidden" name="hapus_foto" id="hapus-foto-flag" value="0">
        </div>

        {{-- ══ KOLOM KANAN: FIELDS FORM ══ --}}
        <div class="form-fields-col">

          {{-- ── ID Menu ── --}}
          <div class="form-group">
            <label class="form-label" for="input-id">ID Menu</label>
            <input
              type="text"
              class="form-input-kuning"
              id="input-id"
              value="MN-{{ sprintf('%03d', $menu->id) }}"
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
              value="{{ old('name', $menu->name) }}"
              placeholder="Nama Menu"
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
              placeholder="Deskripsi singkat rasa, porsi, atau bahan menu..."
            >{{ old('description', $menu->description) }}</textarea>
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
                <option value="" disabled>Pilih Kategori</option>
                @foreach($categories as $category)
                  <option
                    value="{{ $category->id }}"
                    {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}
                  >
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
                value="{{ old('stock', $menu->stock) }}"
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
                value="{{ old('price', (int)$menu->price) }}"
                min="0"
                placeholder="Contoh: 15000"
                required
              >
              <span class="harga-preview" id="harga-preview">
                Rp {{ number_format((int)$menu->price, 0, ',', '.') }}
              </span>
            </div>
          </div>

        </div>
        {{-- /form-fields-col --}}

      </div>
      {{-- /form-desktop-layout --}}

      {{-- ════════════════════════════════════════════
           SEKSI MOOD — relasi many-to-many
           $menu->moods → koleksi mood yang sudah dipilih sebelumnya.
           Controller: $menu->moods()->sync($request->input('moods', []))
      ════════════════════════════════════════════ --}}
      <hr class="section-divider">
      <span class="section-title-mini">Mood / Suasana</span>

      <div class="form-group">
        <label class="form-label">
          Cocok untuk Mood
          <span class="label-optional">(bisa pilih lebih dari satu)</span>
        </label>

        @php
          // Ambil array ID mood yang sudah dipilih untuk menu ini
          $selectedMoodIds = $menu->moods->pluck('id')->toArray();
        @endphp

        <div class="mood-chips">
          @forelse($moods as $mood)
            <label class="mood-chip-label">
              <input
                type="checkbox"
                name="moods[]"
                value="{{ $mood->id }}"
                {{-- Pre-checked jika mood ini sudah dipilih sebelumnya --}}
                {{ in_array($mood->id, $selectedMoodIds) ? 'checked' : '' }}
              >
              <span class="mood-chip-text">{{ $mood->name }}</span>
            </label>
          @empty
            <p style="font-size:13px;color:#94a3b8;">Belum ada data mood tersedia.</p>
          @endforelse
        </div>
      </div>

      {{-- ════════════════════════════════════════════
           SEKSI FLAG — is_recommended, is_new, is_promo
           Pre-checked sesuai data dari database.
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
            <input
              type="checkbox"
              name="is_recommended"
              value="1"
              id="toggle-rekomendasi"
              {{ $menu->is_recommended ? 'checked' : '' }}
            >
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
            <input
              type="checkbox"
              name="is_new"
              value="1"
              id="toggle-baru"
              {{ $menu->is_new ? 'checked' : '' }}
            >
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
            <input
              type="checkbox"
              name="is_promo"
              value="1"
              id="toggle-promo"
              {{ $menu->is_promo ? 'checked' : '' }}
            >
            <div class="toggle-track">
              <div class="toggle-thumb"></div>
            </div>
          </label>
        </div>

      </div>

      {{-- ════════════════════════════════════════════
           TOGGLE KETERSEDIAAN
      ════════════════════════════════════════════ --}}
      <hr class="section-divider">

      <div class="toggle-row">
        <div>
          <span class="toggle-label-text">Tersedia (Aktif)</span>
          <p style="font-size:12px;color:#94a3b8;margin-top:2px;">
            Menu bisa dipesan oleh pelanggan & kasir
          </p>
        </div>
        <label class="toggle-switch">
          <input
            type="checkbox"
            id="toggle-tersedia"
            name="is_available"
            value="1"
            {{ $menu->is_available ? 'checked' : '' }}
          >
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
        <span class="btn-spinner"></span>
        <span class="btn-simpan-txt">Simpan Perubahan</span>
      </button>
    </footer>

  </form>

  {{-- Toast --}}
  <div class="toast-mobile" id="toast-notif">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" id="toast-icon"></svg>
    <span id="toast-pesan">Berhasil!</span>
  </div>

  <script>
    /* ============================================================
       VARIABEL dari BLADE → JavaScript
       Dikirim sebagai konstanta agar tidak perlu inline templating
       berulang di dalam fungsi JS.
    ============================================================ */
    const MENU_ID         = {{ $menu->id }};
    const ROUTE_UPDATE    = "{{ route('admin.menu.update', $menu->id) }}";
    const ROUTE_MENU_LIST = "{{ route('admin.menu') }}";
    const CSRF_TOKEN      = document.querySelector('meta[name="csrf-token"]').content;

    /* ============================================================
       1. NAVIGASI KEMBALI
    ============================================================ */
    const kembaliKeMenu = () => { window.location.href = ROUTE_MENU_LIST; };
    document.getElementById('btn-back')?.addEventListener('click', kembaliKeMenu);
    document.getElementById('btn-batal')?.addEventListener('click', kembaliKeMenu);

    /* ============================================================
       2. LIVE PREVIEW FORMAT HARGA
    ============================================================ */
    document.getElementById('input-harga')?.addEventListener('input', function () {
      const angka = parseInt(this.value, 10);
      const el    = document.getElementById('harga-preview');
      if (el) {
        el.textContent = (!isNaN(angka) && angka >= 0)
          ? 'Rp ' + angka.toLocaleString('id-ID')
          : '';
      }
    });

    /* ============================================================
       3. GANTI / HAPUS FOTO (jika foto sudah ada)
    ============================================================ */
    const inputFotoGanti = document.getElementById('input-foto-ganti');
    const fotoEditImg    = document.getElementById('foto-edit-img');
    const hapusFotoFlag  = document.getElementById('hapus-foto-flag');

    // Tombol Ganti → klik input file tersembunyi
    document.getElementById('btn-ganti-foto')?.addEventListener('click', () => {
      inputFotoGanti?.click();
    });

    // Saat file baru dipilih → update preview gambar
    inputFotoGanti?.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      if (file.size > 2 * 1024 * 1024) {
        showToast('Ukuran foto melebihi 2 MB.', 'error');
        this.value = '';
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        if (fotoEditImg) fotoEditImg.src = e.target.result;
      };
      reader.readAsDataURL(file);
    });

    // Tombol Hapus Foto → set flag hapus_foto = 1, grayscale gambar
    document.getElementById('btn-hapus-foto')?.addEventListener('click', () => {
      if (!confirm('Hapus foto menu ini? Foto tidak bisa dikembalikan.')) return;
      if (hapusFotoFlag) hapusFotoFlag.value = '1';
      if (fotoEditImg)  fotoEditImg.style.filter = 'grayscale(1) opacity(.4)';
      showToast('Foto akan dihapus setelah disimpan.', 'sukses');
    });

    /* ============================================================
       4. UPLOAD FOTO (jika belum ada foto)
    ============================================================ */
    const inputFotoEdit   = document.getElementById('input-foto-edit');
    const uploadAreaEdit  = document.getElementById('upload-area-edit');
    const fotoPreviewEdit = document.getElementById('foto-preview-edit');

    inputFotoEdit?.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      if (file.size > 2 * 1024 * 1024) {
        showToast('Ukuran foto melebihi 2 MB.', 'error');
        this.value = '';
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        if (fotoPreviewEdit) fotoPreviewEdit.src = e.target.result;
        uploadAreaEdit?.classList.add('has-foto');
      };
      reader.readAsDataURL(file);
    });

    /* ============================================================
       5. TOAST HELPER
    ============================================================ */
    function showToast(pesan, tipe = 'sukses') {
      const toast  = document.getElementById('toast-notif');
      const pesanEl = document.getElementById('toast-pesan');
      const ikonEl  = document.getElementById('toast-icon');
      if (!toast || !pesanEl) return;

      pesanEl.textContent = pesan;
      toast.className = 'toast-mobile show ' + tipe;

      if (tipe === 'sukses') {
        ikonEl.innerHTML = '<path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>';
      } else {
        ikonEl.innerHTML = '<circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.5" fill="none"/><path d="M15 9l-6 6M9 9l6 6" stroke="#fff" stroke-width="2" stroke-linecap="round"/>';
      }

      setTimeout(() => toast.classList.remove('show'), 3500);
    }

    /* ============================================================
       6. SUBMIT FORM via Fetch API + Method Spoofing PUT
          Laravel tidak mendukung PUT dari HTML form biasa.
          Solusi: kirim POST dengan field _method=PUT di FormData.
          Controller menerima request.isMethod('PUT') → true.
    ============================================================ */
    document.getElementById('form-edit-menu')?.addEventListener('submit', function (e) {
      e.preventDefault();

      const btn = document.getElementById('btn-simpan');
      btn.disabled = true;
      btn.classList.add('loading');

      const formData = new FormData(this);

      // Method spoofing: beritahu Laravel ini sebenarnya PUT
      formData.append('_method', 'PUT');

      // Jika ada file baru (dari ganti foto), pastikan field 'image' tidak duplikat.
      // FormData sudah otomatis mengambil <input name="image"> yang ter-aktif.

      fetch(ROUTE_UPDATE, {
        method : 'POST', // POST + _method=PUT
        body   : formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN'    : CSRF_TOKEN,
        }
      })
      .then(async (response) => {
        const data = await response.json();

        if (!response.ok) {
          btn.disabled = false;
          btn.classList.remove('loading');

          if (data.errors) {
            const pesanEror = Object.values(data.errors).flat().join('\n');
            showToast(pesanEror.split('\n')[0], 'error');
          } else {
            showToast(data.message || 'Terjadi kesalahan.', 'error');
          }
          return;
        }

        if (data.success) {
          showToast(data.message || 'Data menu berhasil diperbarui!', 'sukses');
          setTimeout(() => {
            window.location.href = ROUTE_MENU_LIST;
          }, 1200);
        } else {
          btn.disabled = false;
          btn.classList.remove('loading');
          showToast('Gagal menyimpan perubahan.', 'error');
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