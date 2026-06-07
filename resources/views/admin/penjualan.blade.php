@extends('layouts.admin')

@section('title', 'Detail Penjualan — Tskuy Admin')

@section('header_title', 'Detail Penjualan')
@section('header_subtitle', 'Melihat dan mengelola laporan transaksi warkop.')

@section('content')
<!-- Title mobile -->
<div class="page-title-section">
    <h1 class="page-title">Detail Penjualan</h1>
    <p class="page-subtitle">Melihat dan mengelola laporan transaksi warkop.</p>
</div>

<main class="scroll-area">

    {{-- ══════════════════════════════════════════════════════
         ACTION BAR MOBILE
         Struktur: Search → Tabs Status → Tabs Tipe → Export
    ══════════════════════════════════════════════════════ --}}
    <div class="action-bar-mobile" style="margin-bottom:10px;">
        <div class="search-wrapper">
            <input type="text" id="searchInputMobile" placeholder="Cari transaksi...">
            <span class="search-icon">
                <svg width="18" height="18" viewBox="0 0 26 26" fill="none">
                    <path d="M25.103 22.071L19.66 16.628A10.721 10.721 0 1010.721 21.443c2.182 0 4.212-.662 5.907-1.786l5.443 5.443a2.143 2.143 0 003.032-3.03zM3.216 10.721a7.505 7.505 0 1115.01 0 7.505 7.505 0 01-15.01 0z" fill="#F8B602" />
                </svg>
            </span>
        </div> -->

        {{-- Filter Tipe Pesanan --}}
        <div class="penjualan-filter-label">Tipe Pesanan</div>
        <div class="category-tabs" id="tabsTipeMobile">
            <button class="tab active" data-filter-type="tipe" data-filter-value="semua">All</button>
            <button class="tab" data-filter-type="tipe" data-filter-value="dine_in">Dine In</button>
            <button class="tab" data-filter-type="tipe" data-filter-value="take_away">Take Away</button>
        </div>

        <div class="action-buttons-row">
            <button class="btn-outline" id="btnExportMobile" data-open="popupExport">
                <svg width="16" height="16" viewBox="0 0 30 30" fill="none">
                    <path d="M15.603 14.095V5.284M13.132 7.549l1.375-1.742a2.32 2.32 0 013.563 0l1.375 1.742M21.639 19.836H9.567M11.768 10.893C5.511 12.51 6.009 17.071 6.009 17.071s-.498 4.577 5.759 6.174a13.124 13.124 0 007.663 0c6.255-1.616 5.759-6.174 5.759-6.174s.496-4.578-5.759-6.178z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Export
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         ACTION BAR DESKTOP
         Struktur: Tabs (2 baris) di atas, lalu search + export
         Mirip persis pola karyawan.blade.php
    ══════════════════════════════════════════════════════ --}}
    <div class="action-bar penjualan-action-bar">

        {{-- Baris 1: Filter Status --}}
        <div class="penjualan-filter-row">
            <span class="penjualan-filter-label">Status:</span>
            <div class="category-tabs" id="tabsStatusDesktop">
                <button class="tab active" data-filter-type="status" data-filter-value="semua">All</button>
                <button class="tab" data-filter-type="status" data-filter-value="paid">
                     Lunas
                </button>
                <button class="tab" data-filter-type="status" data-filter-value="pending">
                    Pending
                </button>
            </div>
        </div>

        {{-- Baris 2: Filter Tipe --}}
        <div class="penjualan-filter-row">
            <span class="penjualan-filter-label">Tipe:</span>
            <div class="category-tabs" id="tabsTipeDesktop">
                <button class="tab active" data-filter-type="tipe" data-filter-value="semua">All</button>
                <button class="tab" data-filter-type="tipe" data-filter-value="dine_in">Dine In</button>
                <button class="tab" data-filter-type="tipe" data-filter-value="take_away">Take Away</button>
            </div>
        </div>

        {{-- Baris 3: Search + Export (sama seperti karyawan) --}}
        <div class="action-right">
            <div class="search-wrapper">
                <input type="text" id="searchInputDesktop" placeholder="Cari Data Penjualan....">
                <span class="search-icon">
                    <svg width="16" height="16" viewBox="0 0 26 26" fill="none">
                        <path d="M25.103 22.071L19.66 16.628A10.721 10.721 0 1010.721 21.443c2.182 0 4.212-.662 5.907-1.786l5.443 5.443a2.143 2.143 0 003.032-3.03zM3.216 10.721a7.505 7.505 0 1115.01 0 7.505 7.505 0 01-15.01 0z" fill="#F8B602" />
                    </svg>
                </span>
            </div>
            <button class="btn-outline" id="btnExport" data-open="popupExport">
                <svg width="16" height="16" viewBox="0 0 30 30" fill="none">
                    <path d="M15.603 14.095V5.284M13.132 7.549l1.375-1.742a2.32 2.32 0 013.563 0l1.375 1.742M21.639 19.836H9.567M11.768 10.893C5.511 12.51 6.009 17.071 6.009 17.071s-.498 4.577 5.759 6.174a13.124 13.124 0 007.663 0c6.255-1.616 5.759-6.174 5.759-6.174s.496-4.578-5.759-6.178z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- Tabel / Card penjualan -->
    <x-tabel-penjualan :transactions="$transactions" />

</main>
@endsection

@section('page_popups')
{{-- ── Popup Detail Transaksi (Struk) ──────────────────────── --}}
<div class="popup popup-detail-transaksi" id="popup-detail-transaksi">
    <div class="struk-desktop-header">
        <span>Detail Transaksi</span>
        <button class="popup-close popup-close-white" data-close aria-label="Tutup">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8" />
                <path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round" />
            </svg>
        </button>
    </div>

    <div class="struk-mobile-header">
        <button class="struk-mobile-back" data-close aria-label="Kembali">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <span class="struk-mobile-title">Detail Transaksi</span>
    </div>

    <div class="struk-body">
        <div class="struk-logo-wrap">
            <img src="/assets/img/logo_warkop.png" alt="Logo Tskuy" class="struk-logo-img">
            <p class="struk-nama-warung">WARKOP TSKUY</p>
            <p class="struk-alamat-warung">Jl. Kopi Harapan No.12, Bandung</p>
        </div>

        <hr class="struk-divider-dashed">

        <div class="struk-info-section">
            <div class="struk-info-row">
                <span class="struk-info-label">No. Transaksi:</span>
                <span class="struk-info-value" id="struk-no-trx">#T0945</span>
            </div>
            <div class="struk-info-row">
                <span class="struk-info-label">Tanggal & Waktu:</span>
                <span class="struk-info-value" id="struk-waktu">2 Apr 2026, 14:20</span>
            </div>
            <div class="struk-info-row">
                <span class="struk-info-label">Tipe Pesanan:</span>
                <span class="struk-info-value" id="struk-tipe">Dine In (Meja 4)</span>
            </div>
            <!-- <div class="struk-info-row">
                <span class="struk-info-label">Status Pembayaran:</span>
                <span class="struk-info-value lunas" id="struk-status">Lunas (QRIS)</span>
            </div> -->
        </div>

        <hr class="struk-divider-dashed">

        <p class="struk-section-label">PESANAN</p>

        <div id="struk-items-list">
            <div class="struk-item">
                <div class="struk-item-top">
                    <div>
                        <p class="struk-item-nama">2x Kopi Susu Aren</p>
                        <p class="struk-item-satuan">@ Rp18.000,-</p>
                    </div>
                    <span class="struk-item-harga">Rp36.000,-</span>
                </div>
                <span class="struk-item-note">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                        <rect x="4" y="2" width="16" height="20" rx="2" stroke="#92400e" stroke-width="1.8" />
                        <path d="M8 7H16M8 11H16M8 15H12" stroke="#92400e" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                    Es dipisah, less sugar
                </span>
            </div>
        </div>

        <hr class="struk-divider-dashed">

        <div class="struk-subtotal-row">
            <span class="struk-subtotal-label">Subtotal:</span>
            <span class="struk-subtotal-value" id="struk-subtotal">Rp51.000,-</span>
        </div>
        <div class="struk-subtotal-row">
            <span class="struk-subtotal-label">Pajak/PBI (10%):</span>
            <span class="struk-subtotal-value" id="struk-pajak">Rp5.100,-</span>
        </div>

        <hr class="struk-divider-dashed" style="margin-top:8px;">

        <div class="struk-total-row">
            <span class="struk-total-label">Total</span>
            <span class="struk-total-value" id="struk-total">Rp56.100,-</span>
        </div>

        <p class="struk-footer-text">
            Terimakasih telah berkunjung!<br>
            warkoptskuy.com
        </p>
    </div>

    <div class="struk-actions">
        <button class="btn-struk-unduh" id="btn-unduh-struk">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <polyline points="7 10 12 15 17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            Unduh
        </button>
        <button class="btn-struk-cetak" id="btn-cetak-struk">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M6 9V2h12v7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <rect x="6" y="14" width="12" height="8" rx="1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Cetak Struk
        </button>
    </div>
</div>

{{-- ── Popup Export ─────────────────────────────────────────── --}}
<div class="popup popup-export" id="popupExport">
    <div class="popup-export-header">
        <h3>Unduh Data Penjualan</h3>
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
                    <span>All Data Penjualan <span>({{ $transactions->total() }} Item)</span></span>
                </label>
                <label class="export-range-item">
                    <input type="radio" name="exportRange" value="current" checked>
                    <span>Hanya Penjualan Yang Sedang ditampilkan <span id="exportRangeCount">({{ $transactions->count() }} Penjualan)</span></span>
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
<script src="{{ asset('js/penjualan.js') }}"></script>
@endsection