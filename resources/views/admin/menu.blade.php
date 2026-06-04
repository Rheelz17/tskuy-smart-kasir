@extends('layouts.admin')

@section('title', 'Manajemen Menu — Tskuy Admin')

@section('header_title', 'Manajemen Menu')
@section('header_subtitle', 'Mengatur hak akses dan data karyawan warkop.')

@section('content')
  <div class="page-title-section">
    <h1 class="page-title">Manajemen Menu</h1>
    <p class="page-subtitle">Mengatur hak akses dan data karyawan warkop.</p>
  </div>

  <main class="scroll-area">
    <div class="action-bar-mobile" style="margin-bottom:10px;">
        <div class="search-wrapper">
            <input type="text" placeholder="Apa yang kamu mau coba?">
            <span class="search-icon"><svg width="18" height="18" viewBox="0 0 26 26" fill="none"><path d="M25.103 22.071L19.66 16.628A10.721 10.721 0 1010.721 21.443c2.182 0 4.212-.662 5.907-1.786l5.443 5.443a2.143 2.143 0 003.032-3.03zM3.216 10.721a7.505 7.505 0 1115.01 0 7.505 7.505 0 01-15.01 0z" fill="#F8B602"/></svg></span>
        </div>
        <div class="action-buttons-row">
            <button class="btn-outline">
            <svg width="16" height="16" viewBox="0 0 22 22" fill="none"><path d="M19.25 5.5H17.417M19.25 11H14.667M19.25 16.5H14.667M6.417 18.333V12.431c0-.19 0-.286-.018-.377a1.375 1.375 0 00-.332-.673L3.071 7.735c-.12-.149-.179-.224-.22-.307a1.375 1.375 0 00-.213-.639V5.134c0-.514 0-.77.1-.967a.917.917 0 01.4-.4C3.336 3.667 3.592 3.667 4.105 3.667h8.067c.513 0 .77 0 .966.1.177.088.317.228.405.4.1.198.1.454.1.967v1.685c0 .19 0 .286-.018.377a1.375 1.375 0 01-.332.673l-3.026 3.746c-.12.149-.179.224-.22.307a1.375 1.375 0 01-.213.639v3.152L6.417 18.333z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Filter
            </button>
            <button class="btn-outline">
            <svg width="16" height="16" viewBox="0 0 30 30" fill="none"><path d="M15.603 14.095V5.284M13.132 7.549l1.375-1.742a2.32 2.32 0 013.563 0l1.375 1.742M21.639 19.836H9.567M11.768 10.893C5.511 12.51 6.009 17.071 6.009 17.071s-.498 4.577 5.759 6.174a13.124 13.124 0 007.663 0c6.255-1.616 5.759-6.174 5.759-6.174s.496-4.578-5.759-6.178z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Export
            </button>
        </div>
    </div>

    <!-- Tombol Tambah -->
    <div class="action-bar-top">
        <button class="btn-tambah" id="btn-tambah-menu">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.4019 9.23002V3.356M8.75497 4.86578L9.67177 3.70427C9.7571 3.59604 9.86739 3.50829 9.9939 3.44787C10.1204 3.38744 10.2598 3.35596 10.401 3.35596C10.5423 3.35596 10.6816 3.38744 10.8081 3.44787C10.9346 3.50829 11.0448 3.59604 11.1302 3.70427L12.0471 4.86578M14.4261 13.0575H6.37782M7.84554 7.0955C3.6739 8.17264 4.00628 11.2138 4.00628 11.2138C4.00628 11.2138 3.6739 14.2657 7.84554 15.3302C9.52076 15.7485 11.2793 15.7485 12.9546 15.3302C17.1243 14.2531 16.7938 11.2138 16.7938 11.2138C16.7938 11.2138 17.1243 8.16187 12.9546 7.0955" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Tambah Menu
        </button>
    </div>

    
    <!-- Action bar desktop (search + filter + export) -->
    <div class="action-bar">
        <div class="category-tabs" style="margin-bottom:14px;">
        <button class="tab active" data-kategori="all">All</button>
        <button class="tab" data-kategori="makanan">Makanan</button>
        <button class="tab" data-kategori="minuman">Minuman</button>
        <button class="tab" data-kategori="cemilan">Cemilan</button>
        </div>
        <div class="action-right">
            <div class="search-wrapper">
            <input type="text" placeholder="Cari Menu...">
            <span class="search-icon"><svg width="16" height="16" viewBox="0 0 26 26" fill="none"><path d="M25.103 22.071L19.66 16.628A10.721 10.721 0 1010.721 21.443c2.182 0 4.212-.662 5.907-1.786l5.443 5.443a2.143 2.143 0 003.032-3.03zM3.216 10.721a7.505 7.505 0 1115.01 0 7.505 7.505 0 01-15.01 0z" fill="#F8B602"/></svg></span>
            </div>
            <button class="btn-outline" id="btnExport">
            <svg width="16" height="16" viewBox="0 0 30 30" fill="none"><path d="M15.603 14.095V5.284M13.132 7.549l1.375-1.742a2.32 2.32 0 013.563 0l1.375 1.742M21.639 19.836H9.567M11.768 10.893C5.511 12.51 6.009 17.071 6.009 17.071s-.498 4.577 5.759 6.174a13.124 13.124 0 007.663 0c6.255-1.616 5.759-6.174 5.759-6.174s.496-4.578-5.759-6.178z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Export
            </button>
        </div>
    </div>

    <!--
    CARD LIST — mobile only
    data-jabatan di setiap card → karyawan.js filter saat tab diklik
    class btn-hapus-karyawan + data-nama → karyawan.js isi popup hapus
    class btn-edit-karyawan → karyawan.js buka popup edit (nanti)
    -->
    <div class="karyawan-card-list">

        <div class="karyawan-card" data-jabatan="kasir">
            <img src="https://i.pravatar.cc/150?img=5" alt="Fahri" class="karyawan-thumb">
            <div class="karyawan-card-info">
            <div class="karyawan-card-top">
                <p class="karyawan-card-name">Fahri Eka Pratama</p>
                <span class="karyawan-card-status">Aktif</span>
            </div>
            <p class="karyawan-card-meta">EMP001 | Kasir</p>
            <div class="karyawan-card-bottom">
                <span class="karyawan-card-phone">0821-8422-9314</span>
                <div class="tindakan-col">
                <span style="font-size:11px;color:#94a3b8;">Tindakan:</span>
                <a href="#" class="btn-tindakan btn-edit-karyawan" data-nama="Fahri Eka Pratama"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                <a href="#" class="btn-tindakan btn-hapus-karyawan" data-nama="Fahri Eka Pratama"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                </div>
            </div>
            </div>
        </div>

        <div class="karyawan-card" data-jabatan="koki">
            <img src="https://i.pravatar.cc/150?img=8" alt="Juniansyah" class="karyawan-thumb">
            <div class="karyawan-card-info">
            <div class="karyawan-card-top">
                <p class="karyawan-card-name">Juniansyah Raka</p>
                <span class="karyawan-card-status">Aktif</span>
            </div>
            <p class="karyawan-card-meta">EMP002 | Koki</p>
            <div class="karyawan-card-bottom">
                <span class="karyawan-card-phone">0898-8274-987</span>
                <div class="tindakan-col">
                <span style="font-size:11px;color:#94a3b8;">Tindakan:</span>
                <a href="#" class="btn-tindakan btn-edit-karyawan" data-nama="Juniansyah Raka"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                <a href="#" class="btn-tindakan btn-hapus-karyawan" data-nama="Juniansyah Raka"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                </div>
            </div>
            </div>
        </div>

        <div class="karyawan-card" data-jabatan="admin">
            <img src="https://i.pravatar.cc/150?img=12" alt="Kurniawan" class="karyawan-thumb">
            <div class="karyawan-card-info">
            <div class="karyawan-card-top">
                <p class="karyawan-card-name">Kurniawan Dwi Suyono</p>
                <span class="karyawan-card-status">Aktif</span>
            </div>
            <p class="karyawan-card-meta">EMP003 | Admin</p>
            <div class="karyawan-card-bottom">
                <span class="karyawan-card-phone">0878-9183-8765</span>
                <div class="tindakan-col">
                <span style="font-size:11px;color:#94a3b8;">Tindakan:</span>
                <a href="#" class="btn-tindakan btn-edit-karyawan" data-nama="Kurniawan Dwi Suyono"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                <a href="#" class="btn-tindakan btn-hapus-karyawan" data-nama="Kurniawan Dwi Suyono"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                </div>
            </div>
            </div>
        </div>

        <div class="karyawan-card" data-jabatan="kasir">
            <img src="https://i.pravatar.cc/150?img=15" alt="Rafliansyah" class="karyawan-thumb">
            <div class="karyawan-card-info">
            <div class="karyawan-card-top">
                <p class="karyawan-card-name">Rafliansyah Firman</p>
                <span class="karyawan-card-status">Aktif</span>
            </div>
            <p class="karyawan-card-meta">EMP004 | Kasir</p>
            <div class="karyawan-card-bottom">
                <span class="karyawan-card-phone">0812-7452-8174</span>
                <div class="tindakan-col">
                <span style="font-size:11px;color:#94a3b8;">Tindakan:</span>
                <a href="#" class="btn-tindakan btn-edit-karyawan" data-nama="Rafliansyah Firman"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                <a href="#" class="btn-tindakan btn-hapus-karyawan" data-nama="Rafliansyah Firman"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                </div>
            </div>
            </div>
        </div>

        <div class="karyawan-card" data-jabatan="kasir">
            <img src="https://i.pravatar.cc/150?img=18" alt="Herman" class="karyawan-thumb">
            <div class="karyawan-card-info">
            <div class="karyawan-card-top">
                <p class="karyawan-card-name">Herman Nasution</p>
                <span class="karyawan-card-status">Aktif</span>
            </div>
            <p class="karyawan-card-meta">EMP005 | Kasir</p>
            <div class="karyawan-card-bottom">
                <span class="karyawan-card-phone">0878-8241-1112</span>
                <div class="tindakan-col">
                <span style="font-size:11px;color:#94a3b8;">Tindakan:</span>
                <a href="#" class="btn-tindakan btn-edit-karyawan" data-nama="Herman Nasution"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                <a href="#" class="btn-tindakan btn-hapus-karyawan" data-nama="Herman Nasution"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                </div>
            </div>
            </div>
        </div>

        <div class="karyawan-card" data-jabatan="koki">
            <img src="https://i.pravatar.cc/150?img=20" alt="Asep" class="karyawan-thumb">
            <div class="karyawan-card-info">
            <div class="karyawan-card-top">
                <p class="karyawan-card-name">Asep Irfanudin</p>
                <span class="karyawan-card-status">Aktif</span>
            </div>
            <p class="karyawan-card-meta">EMP006 | Koki</p>
            <div class="karyawan-card-bottom">
                <span class="karyawan-card-phone">0872-8274-888</span>
                <div class="tindakan-col">
                <span style="font-size:11px;color:#94a3b8;">Tindakan:</span>
                <a href="#" class="btn-tindakan btn-edit-karyawan" data-nama="Asep Irfanudin"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                <a href="#" class="btn-tindakan btn-hapus-karyawan" data-nama="Asep Irfanudin"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                </div>
            </div>
            </div>
        </div>

        <div class="karyawan-card" data-jabatan="koki">
            <img src="https://i.pravatar.cc/150?img=22" alt="Kamala" class="karyawan-thumb">
            <div class="karyawan-card-info">
            <div class="karyawan-card-top">
                <p class="karyawan-card-name">Kamala Juan Siregar</p>
                <span class="karyawan-card-status">Aktif</span>
            </div>
            <p class="karyawan-card-meta">EMP007 | Koki</p>
            <div class="karyawan-card-bottom">
                <span class="karyawan-card-phone">0821-9824-3456</span>
                <div class="tindakan-col">
                <span style="font-size:11px;color:#94a3b8;">Tindakan:</span>
                <a href="#" class="btn-tindakan btn-edit-karyawan" data-nama="Kamala Juan Siregar"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                <a href="#" class="btn-tindakan btn-hapus-karyawan" data-nama="Kamala Juan Siregar"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                </div>
            </div>
            </div>
        </div>

        <div class="karyawan-card" data-jabatan="koki">
            <img src="https://i.pravatar.cc/150?img=25" alt="Paris" class="karyawan-thumb">
            <div class="karyawan-card-info">
            <div class="karyawan-card-top">
                <p class="karyawan-card-name">Paris Agustinus</p>
                <span class="karyawan-card-status">Aktif</span>
            </div>
            <p class="karyawan-card-meta">EMP008 | Koki</p>
            <div class="karyawan-card-bottom">
                <span class="karyawan-card-phone">0812-9999-2222</span>
                <div class="tindakan-col">
                <span style="font-size:11px;color:#94a3b8;">Tindakan:</span>
                <a href="#" class="btn-tindakan btn-edit-karyawan" data-nama="Paris Agustinus"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                <a href="#" class="btn-tindakan btn-hapus-karyawan" data-nama="Paris Agustinus"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                </div>
            </div>
            </div>
        </div>

    </div>

    <footer class="content-footer-mobile">
        <p class="data-info">Menampilkan 8 dari 9 karyawan</p>
        <div class="pagination">
            <button class="page-link disabled">Sebelumnya</button>
            <button class="page-number active">1</button>
            <button class="page-number">2</button>
            <button class="page-link">Selanjutnya</button>
        </div>
    </footer>

    <!-- Tabel desktop -->
    <div class="table-container">
        <table id="tabel-menu">
            <thead>
                <tr>
                    <th class="col-foto">Foto</th>
                    <th class="col-left">Nama</th>
                    <th>ID Menu</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <tr data-kategori="minuman">
                    <td><div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;"></div></td>
                    <td class="col-left text-bold">Kopi Susu Tskuy</td>
                    <td>MNU001</td>
                    <td>Minuman</td>
                    <td><span class="notelp-badge">50 Porsi</span></td>
                    <td>Rp 15.000</td>
                    <td>
                        <div class="popup-form-toggle" style="padding: 0; margin: 0; justify-content: center; border: none;">
                            <label class="popup-toggle-switch">
                                <input type="checkbox" class="toggle-status-menu" data-id="{{ $menu->id ?? '1' }}" checked>
                                <div class="popup-toggle-track">
                                    <div class="popup-toggle-thumb"></div>
                                </div>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="tindakan-col">
                            <a href="#" class="btn-tindakan btn-edit-menu" data-id="MNU001" data-nama="Kopi Susu Tskuy" data-kategori="minuman" data-stok="50" data-harga="15000" data-status="tersedia">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                            <a href="#" class="btn-tindakan btn-hapus-menu" data-nama="Kopi Susu Tskuy">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-kategori="makanan">
                    <td><div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;"></div></td>
                    <td class="col-left text-bold">Roti Bakar Cokelat</td>
                    <td>MNU002</td>
                    <td>Makanan</td>
                    <td><span class="notelp-badge">20 Porsi</span></td>
                    <td>Rp 18.000</td>
                    <td>
                        <div class="popup-form-toggle" style="padding: 0; margin: 0; justify-content: center; border: none;">
                            <label class="popup-toggle-switch">
                                <input type="checkbox" class="toggle-status-menu" data-id="{{ $menu->id ?? '1' }}" checked>
                                <div class="popup-toggle-track">
                                    <div class="popup-toggle-thumb"></div>
                                </div>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="tindakan-col">
                            <a href="#" class="btn-tindakan btn-edit-menu" data-id="MNU002" data-nama="Roti Bakar Cokelat" data-kategori="makanan" data-stok="20" data-harga="18000" data-status="tersedia">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                            <a href="#" class="btn-tindakan btn-hapus-menu" data-nama="Roti Bakar Cokelat">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-kategori="cemilan">
                    <td><div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;"></div></td>
                    <td class="col-left text-bold">French Fries</td>
                    <td>MNU003</td>
                    <td>Cemilan</td>
                    <td><span class="notelp-badge">0 Porsi</span></td>
                    <td>Rp 12.000</td>
                    <td>
                        <div class="popup-form-toggle" style="padding: 0; margin: 0; justify-content: center; border: none;">
                                <label class="popup-toggle-switch">
                                    <input type="checkbox" class="toggle-status-menu" data-id="{{ $menu->id ?? '1' }}" checked>
                                    <div class="popup-toggle-track">
                                        <div class="popup-toggle-thumb"></div>
                                    </div>
                                </label>
                            </div>
                    </td>
                    <td>
                        <div class="tindakan-col">
                            <a href="#" class="btn-tindakan btn-edit-menu" data-id="MNU003" data-nama="French Fries" data-kategori="cemilan" data-stok="0" data-harga="12000" data-status="habis">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                            <a href="#" class="btn-tindakan btn-hapus-menu" data-nama="French Fries">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <footer class="content-footer">
        <p class="data-info">Menampilkan 8 dari 9 karyawan</p>
        <div class="pagination">
            <button class="page-link disabled">Sebelumnya</button>
            <button class="page-number active">1</button>
            <button class="page-number">2</button>
            <button class="page-link">Selanjutnya</button>
        </div>
    </footer>
  </main>
@endsection


@section('page_popups')
    <div class="popup popup-form-karyawan" id="popup-tambah-menu">
        <div class="popup-form-header">
            <div>
                <h2 class="popup-form-title">Tambah Menu Baru</h2>
                <p class="popup-form-subtitle">Isi kolom di bawah untuk menambahkan menu kuliner baru</p>
            </div>
            <button class="popup-close-merah" data-close aria-label="Tutup">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="#fee2e2"/>
                    <path d="M15 9L9 15M9 9L15 15" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="popup-form-body">
            <div class="popup-form-foto-col">
                <div class="popup-upload-area" id="popup-tambah-upload-area">
                    <input type="file" class="popup-upload-input" id="popup-tambah-input-foto" accept="image/jpg,image/jpeg,image/png">
                    <svg width="40" height="40" viewBox="0 0 48 48" fill="none">
                        <path d="M32 32L24 24L16 32" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M24 24V40" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M40.82 36.82A10 10 0 0034 18h-2.52A16 16 0 108 36.92" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="popup-upload-label">Unggah Foto Menu</p>
                    <p class="popup-upload-hint">Klik atau seret foto produk ke sini (JPG / PNG).</p>
                    <img src="" alt="Preview" class="popup-upload-preview" id="popup-tambah-preview">
                </div>
            </div>

            <div class="popup-form-fields-col">
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-id">ID Menu</label>
                    <input type="text" class="popup-form-input" id="popup-tambah-id" value="MNU004" readonly>
                </div>

                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-nama">Nama Menu</label>
                    <input type="text" class="popup-form-input" id="popup-tambah-nama" placeholder="Masukkan Nama Menu...">
                </div>

                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-kategori">Kategori</label>
                    <div class="popup-select-wrapper">
                        <select class="popup-form-select" id="popup-tambah-kategori">
                            <option value="" disabled selected>Pilih Kategori</option>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                            <option value="cemilan">Cemilan</option>
                        </select>
                        <div class="popup-select-chevron">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M6 9L12 15L18 9" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-stok">Stok Porsi</label>
                    <input type="number" class="popup-form-input" id="popup-tambah-stok" min="0" placeholder="Contoh: 50">
                </div>

                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-harga">Harga Jual (Rp)</label>
                    <input type="number" class="popup-form-input" id="popup-tambah-harga" min="0" placeholder="Contoh: 15000">
                </div>

                <div class="popup-form-toggle">
                    <span class="popup-toggle-label">Status</span>
                    <label class="popup-toggle-switch">
                        <input type="checkbox" id="popup-tambah-akses">
                        <div class="popup-toggle-track">
                            <div class="popup-toggle-thumb"></div>
                        </div>
                    </label>
                </div>

                <div class="popup-form-actions">
                    <button class="popup-btn-batal" data-close>Batal</button>
                    <button class="popup-btn-simpan" id="popup-btn-tambah-simpan">Simpan Menu</button>
                </div>
            </div>
        </div>
    </div>

    <div class="popup popup-form-karyawan" id="popup-edit-menu">
        <div class="popup-form-header">
            <div>
                <h2 class="popup-form-title">Edit Data Menu</h2>
                <p class="popup-form-subtitle">Silahkan sesuaikan info menu kuliner yang ingin diubah.</p>
            </div>
            <button class="popup-close-merah" data-close aria-label="Tutup">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="#fee2e2"/>
                    <path d="M15 9L9 15M9 9L15 15" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="popup-form-body">
            <div class="popup-form-foto-col">
                <div class="popup-foto-edit-wrap">
                    <img src="" alt="Foto Menu" class="popup-foto-edit-img" id="popup-edit-foto-img">
                    
                    <div class="popup-foto-edit-actions">
                        <button class="popup-btn-foto-edit" id="popup-edit-btn-ganti-foto" title="Ganti Foto">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="popup-btn-foto-hapus" id="popup-edit-btn-hapus-foto" title="Hapus Foto">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <input type="file" id="popup-edit-input-foto" accept="image/jpg,image/jpeg,image/png" style="display:none;">
                </div>
            </div>

            <div class="popup-form-fields-col">
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-id">ID Menu</label>
                    <input type="text" class="popup-form-input" id="popup-edit-id" readonly>
                </div>

                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-nama">Nama Menu</label>
                    <input type="text" class="popup-form-input" id="popup-edit-nama" placeholder="Nama Menu">
                </div>

                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-kategori">Kategori</label>
                    <div class="popup-select-wrapper">
                        <select class="popup-form-select" id="popup-edit-kategori">
                            <option value="" disabled>Pilih Kategori</option>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                            <option value="cemilan">Cemilan</option>
                        </select>
                        <div class="popup-select-chevron">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M6 9L12 15L18 9" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-stok">Stok Porsi</label>
                    <input type="number" class="popup-form-input" id="popup-edit-stok" min="0">
                </div>

                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-harga">Harga Jual (Rp)</label>
                    <input type="number" class="popup-form-input" id="popup-edit-harga" min="0">
                </div>

                <div class="popup-form-actions">
                    <button class="popup-btn-batal" data-close>Batal</button>
                    <button class="popup-btn-simpan" id="popup-btn-edit-simpan">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="popup popup-warning" id="popup-hapus-menu">
        <button class="popup-close popup-close-float" data-close>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8" />
                <path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round" />
            </svg>
        </button>
        <div class="popup-body popup-body-warning">
            <div class="warning-icon-wrap">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                    <path d="M12 9V13M12 17H12.01" stroke="#fff" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </div>
            <h3 class="warning-title">
                Hapus menu <span id="popup-hapus-nama-menu" style="color: #ef4444;">[Nama Menu]</span>?
            </h3>
            <p class="warning-desc">
                Data menu kuliner ini akan dihapus permanen dari katalog kasir dan tidak bisa dikembalikan.
            </p>
            <div class="warning-actions">
                <button class="btn-batal-warning" data-close>Batal</button>
                <button class="btn-confirm-warning" id="btn-confirm-hapus" data-close>Ya, Hapus</button>
            </div>
        </div>
    </div>

    <div class="popup popup-export" id="popupExport">
        <div class="popup-export-header">
        <h3>Unduh Data Menu</h3>
        <button class="popup-close-white" id="closeExportBtn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </button>
        </div>
        <div class="popup-export-body">
        <div class="export-format-title">Pilih Format Data</div>
        <div class="export-format-options">
            <div class="export-option selected" data-fmt="xlsx">
            <div class="export-icon xlsx">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
                </svg>
            </div>
            <span class="export-option-name">Excel</span>
            </div>
            <div class="export-option" data-fmt="pdf">
            <div class="export-icon pdf">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
                </svg>
            </div>
            <span class="export-option-name">PDF</span>
            </div>
        </div>

        <div>
            <div class="export-range-title">Rentang Data</div>
            <div class="export-range-list" id="exportRangeList">
            <label class="export-range-item">
                <input type="radio" name="exportRange" value="all">
                <span>Semua Data Menu <span>(123 Item)</span></span>
            </label>
            <label class="export-range-item">
                <input type="radio" name="exportRange" value="current" checked>
                <span>Hanya Menu Yang Sedang Sditampilkan <span id="exportRangeCount">(8 Menu)</span></span>
            </label>
            <label class="export-range-item">
                <input type="radio" name="exportRange" value="none">
                <span>Tidak Tersedia</span>
            </label>
            </div>
        </div>

        <button class="btn-unduh" id="btnUnduhData">Unduh Data</button>
        </div>
    </div>
@endsection


@section('scripts')
  <script src="{{ asset('js/menu.js') }}"></script>
@endsection