{{--
    Component: koki-filter.blade.php
    ─────────────────────────────────────────────────────────────
    Tab filter antrian dapur. Counter badge diisi server saat render,
    lalu diperbarui secara live oleh koki.js (lewat updateCounters()).

    Variabel yang dibutuhkan dari parent view: $orders (Collection)
    ─────────────────────────────────────────────────────────────
--}}

@php
    $total    = $orders->count();
    $menunggu = $orders->where('status', 'pending')->count();
    $selesai  = $orders->where('status', 'completed')->count();
@endphp

<nav class="filter-bar" role="tablist" aria-label="Filter antrian pesanan">

  {{-- Tab: Semua --}}
  <button class="filter-tab active"
          data-filter="semua"
          role="tab"
          aria-selected="true"
          aria-controls="ordersGrid">
    Semua
    <span class="tab-badge" id="badge-semua">{{ $total }}</span>
  </button>

  {{-- Tab: Menunggu (pending, termasuk yang urgent >20 mnt) --}}
  <button class="filter-tab"
          data-filter="menunggu"
          role="tab"
          aria-selected="false"
          aria-controls="ordersGrid">
    Menunggu
    <span class="tab-badge" id="badge-menunggu">{{ $menunggu }}</span>
  </button>

  {{-- Tab: Selesai --}}
  <button class="filter-tab"
          data-filter="selesai"
          role="tab"
          aria-selected="false"
          aria-controls="ordersGrid">
    Selesai
    <span class="tab-badge" id="badge-selesai">{{ $selesai }}</span>
  </button>

</nav>