{{--
    Component: koki-filter.blade.php
    ─────────────────────────────────────────────────────────────
    Tab filter antrian dapur.

    Counter badge diisi server saat render pertama,
    kemudian diperbarui secara live oleh koki.js (via updateCounters()).

    Status yang ditangani:
      - semua    : semua order aktif
      - pending  : baru masuk, belum diproses
      - cooking  : sedang dimasak
      - ready    : siap saji, menunggu kasir
      - selesai  : COMPLETED hari ini (riwayat)

    Variabel yang dibutuhkan dari parent view: $orders (Collection)
    ─────────────────────────────────────────────────────────────
--}}

@php
    // Hitung counter awal dari server
    $cntSemua   = $orders->count();
    $cntPending = $orders->whereIn('status', ['pending'])->count();
    $cntCooking = $orders->whereIn('status', ['cooking'])->count();
    $cntReady   = $orders->whereIn('status', ['ready'])->count();
    $cntSelesai = $orders->whereIn('status', ['completed'])->count();

    // Counter "menunggu" = PENDING + COOKING
    $cntMenunggu = $cntPending + $cntCooking;
@endphp

<nav class="filter-bar" role="tablist" aria-label="Filter antrian pesanan">

  {{-- Tab: Semua --}}
  <button class="filter-tab active"
          data-filter="semua"
          role="tab"
          aria-selected="true"
          aria-controls="ordersGrid">
    Semua
    <span class="tab-badge" id="badge-semua">{{ $cntSemua }}</span>
  </button>

  {{-- Tab: Menunggu (PENDING) — pesanan baru, belum dimasak --}}
  <button class="filter-tab"
          data-filter="pending"
          role="tab"
          aria-selected="false"
          aria-controls="ordersGrid">
    🕐 Menunggu
    <span class="tab-badge" id="badge-pending">{{ $cntPending }}</span>
  </button>

  {{-- Tab: Memasak (COOKING) — sedang diproses koki --}}
  <button class="filter-tab"
          data-filter="cooking"
          role="tab"
          aria-selected="false"
          aria-controls="ordersGrid">
    🔥 Memasak
    <span class="tab-badge" id="badge-cooking">{{ $cntCooking }}</span>
  </button>

  {{-- Tab: Siap Saji (READY) — menunggu kasir/pelayan --}}
  <button class="filter-tab"
          data-filter="ready"
          role="tab"
          aria-selected="false"
          aria-controls="ordersGrid">
    ✅ Siap Saji
    <span class="tab-badge" id="badge-ready">{{ $cntReady }}</span>
  </button>

</nav>