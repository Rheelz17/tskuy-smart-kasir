@extends('layouts.kasir')

@section('title', 'Manajemen Menu – Warkop Tskuy')

@section('header_title', 'Manajemen Menu')
@section('header_subtitle', 'Kelola daftar menu dan ketersediaan stok produk')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/kasir-pages.css') }}" />
@endsection


@section('content')
<main class="scroll-area">
    <div class="action-bar">
        <div class="category-tabs">
            <button class="tab active" data-kategori="all">All</button>
            <button class="tab" data-kategori="makanan">Makanan</button>
            <button class="tab" data-kategori="minuman">Minuman</button>
            <button class="tab" data-kategori="cemilan">Cemilan</button>
        </div>
        <div class="action-right">
            <div class="search-wrapper">
                <input type="text" placeholder="Cari Menu...">
                <span class="search-icon"><svg width="16" height="16" viewBox="0 0 26 26" fill="none">
                        <path d="M25.103 22.071L19.66 16.628A10.721 10.721 0 1010.721 21.443c2.182 0 4.212-.662 5.907-1.786l5.443 5.443a2.143 2.143 0 003.032-3.03zM3.216 10.721a7.505 7.505 0 1115.01 0 7.505 7.505 0 01-15.01 0z" fill="#F8B602" />
                    </svg></span>
            </div>
            <button class="btn-outline" id="btnExport">
                <svg width="16" height="16" viewBox="0 0 30 30" fill="none">
                    <path d="M15.603 14.095V5.284M13.132 7.549l1.375-1.742a2.32 2.32 0 013.563 0l1.375 1.742M21.639 19.836H9.567M11.768 10.893C5.511 12.51 6.009 17.071 6.009 17.071s-.498 4.577 5.759 6.174a13.124 13.124 0 007.663 0c6.255-1.616 5.759-6.174 5.759-6.174s.496-4.578-5.759-6.178z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- Tabel desktop -->
    <!-- <div class="table-container">
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
                    <td>
                        <div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;"></div>
                    </td>
                    <td class="col-left text-bold">Kopi Susu Tskuy</td>
                    <td>MNU001</td>
                    <td>Minuman</td>
                    <td><span class="stok-badge">50 Porsi</span></td>
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
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2" />
                                    <path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="#" class="btn-tindakan btn-hapus-menu" data-nama="Kopi Susu Tskuy">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15" />
                                    <path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-kategori="makanan">
                    <td>
                        <div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;"></div>
                    </td>
                    <td class="col-left text-bold">Roti Bakar Cokelat</td>
                    <td>MNU002</td>
                    <td>Makanan</td>
                    <td><span class="stok-badge">20 Porsi</span></td>
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
                            <a href="#" class="btn-tindakan btn-edit-menu" data-id="MNU001" data-nama="Kopi Susu Tskuy" data-kategori="minuman" data-stok="50" data-harga="15000" data-status="tersedia">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2" />
                                    <path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="#" class="btn-tindakan btn-hapus-menu" data-nama="Kopi Susu Tskuy">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15" />
                                    <path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-kategori="cemilan">
                    <td>
                        <div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;"></div>
                    </td>
                    <td class="col-left text-bold">French Fries</td>
                    <td>MNU003</td>
                    <td>Cemilan</td>
                    <td><span class="stok-badge">0 Porsi</span></td>
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
                            <a href="#" class="btn-tindakan btn-edit-menu" data-id="MNU001" data-nama="Kopi Susu Tskuy" data-kategori="minuman" data-stok="50" data-harga="15000" data-status="tersedia">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2" />
                                    <path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="#" class="btn-tindakan btn-hapus-menu" data-nama="Kopi Susu Tskuy">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15" />
                                    <path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div> -->
    <x-menu-tabel />

    <footer class="content-footer">
        <p class="data-info">Menampilkan 8 dari 9 menu</p>
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
<!-- POPUP EXPORT -->
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
                    <span>Hanya Menu Yang Sedang ditampilkan <span id="exportRangeCount">(8 Menu)</span></span>
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
<script src="{{ asset('js/menu-kasir.js') }}"></script>
@endsection