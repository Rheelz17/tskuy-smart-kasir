{{--
  ============================================================
  POPUP FILTER — Status Pembayaran & Tipe Pesanan
  Tambahkan ini di dalam @section('page_popups')
  ============================================================
--}}

{{-- Overlay gelap di belakang dropdown filter --}}
<div class="filter-overlay" id="filterOverlay"></div>

{{-- Dropdown filter — muncul di bawah tombol btnFilter --}}
<div class="filter-dropdown" id="filterDropdown" role="dialog" aria-label="Filter Penjualan">

  <div class="filter-dropdown-header">
    <span class="filter-dropdown-title">
      <svg width="14" height="14" viewBox="0 0 22 22" fill="none">
        <path d="M19.25 5.5H17.417M19.25 11H14.667M19.25 16.5H14.667M6.417 18.333V12.431c0-.19 0-.286-.018-.377a1.375 1.375 0 00-.332-.673L3.071 7.735c-.12-.149-.179-.224-.22-.307a1.375 1.375 0 00-.213-.639V5.134c0-.514 0-.77.1-.967a.917.917 0 01.4-.4C3.336 3.667 3.592 3.667 4.105 3.667h8.067c.513 0 .77 0 .966.1.177.088.317.228.405.4.1.198.1.454.1.967v1.685c0 .19 0 .286-.018.377a1.375 1.375 0 01-.332.673l-3.026 3.746c-.12.149-.179.224-.22.307a1.375 1.375 0 01-.213.639v3.152L6.417 18.333z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
      Filter Penjualan
    </span>
    <button class="filter-dropdown-close" id="btnCloseFilter" aria-label="Tutup filter">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
      </svg>
    </button>
  </div>

  {{-- ── FILTER 1: Status Pembayaran ─────────────────────── --}}
  <div class="filter-group">
    <p class="filter-group-label">Status Pembayaran</p>
    <div class="filter-chips" role="group" aria-label="Filter status pembayaran">

      <button class="filter-chip active" data-filter-type="status" data-filter-value="semua">
        Semua
      </button>

      {{-- Nilai harus cocok dengan data-status di <tr> dan .trx-card --}}
      <button class="filter-chip" data-filter-type="status" data-filter-value="paid">
        <span class="chip-dot chip-dot--lunas"></span>
        Lunas
      </button>
      <button class="filter-chip" data-filter-type="status" data-filter-value="pending">
        <span class="chip-dot chip-dot--pending"></span>
        Pending
      </button>
      <button class="filter-chip" data-filter-type="status" data-filter-value="failed">
        <span class="chip-dot chip-dot--gagal"></span>
        Gagal
      </button>
      <button class="filter-chip" data-filter-type="status" data-filter-value="cancelled">
        <span class="chip-dot chip-dot--gagal"></span>
        Batal
      </button>

    </div>
  </div>

  <div class="filter-separator"></div>

  {{-- ── FILTER 2: Tipe Pesanan (Eating Option) ──────────── --}}
  <div class="filter-group">
    <p class="filter-group-label">Tipe Pesanan</p>
    <div class="filter-chips" role="group" aria-label="Filter tipe pesanan">

      <button class="filter-chip active" data-filter-type="tipe" data-filter-value="semua">
        Semua
      </button>

      {{-- Nilai harus cocok dengan data-tipe di <tr> dan .trx-card --}}
      <button class="filter-chip" data-filter-type="tipe" data-filter-value="dine_in">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
          <path d="M3 17h18M3 12h18M3 7h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Dine In
      </button>
      <button class="filter-chip" data-filter-type="tipe" data-filter-value="take_away">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
          <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
          <path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path d="M16 10a4 4 0 01-8 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Take Away
      </button>

    </div>
  </div>

  <div class="filter-separator"></div>

  {{-- ── Action buttons ───────────────────────────────────── --}}
  <div class="filter-actions">
    <button class="filter-btn-reset" id="btnResetFilter">
      Reset Filter
    </button>
    <button class="filter-btn-apply" id="btnApplyFilter">
      Terapkan
      <span class="filter-active-count" id="filterActiveCount" style="display:none;">0</span>
    </button>
  </div>

</div>