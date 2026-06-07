{{--
  Partial: koki-filter.blade.php
  ─────────────────────────────────────────────────────────
  Tab filter antrian dapur.
  Counter diisi server saat render, lalu diperbarui JS live.
  Variabel dibutuhkan: $orders (Collection)
  ─────────────────────────────────────────────────────────
--}}
@php
  $cntSemua   = $orders->count();
  $cntPending = $orders->where('status', 'pending')->count();
  $cntCooking = $orders->where('status', 'cooking')->count();
  $cntReady   = $orders->where('status', 'ready')->count();
@endphp

<nav class="koki-filter-bar" role="tablist" aria-label="Filter antrian pesanan">

  <button class="koki-filter-tab active"
          data-filter="semua"
          role="tab" aria-selected="true">
    Semua
    <span class="koki-tab-badge" id="badge-semua">{{ $cntSemua }}</span>
  </button>

  <button class="koki-filter-tab"
          data-filter="pending"
          role="tab" aria-selected="false">
    Menunggu
    <span class="koki-tab-badge" id="badge-pending">{{ $cntPending }}</span>
  </button>

  <button class="koki-filter-tab"
          data-filter="cooking"
          role="tab" aria-selected="false">
    Memasak
    <span class="koki-tab-badge" id="badge-cooking">{{ $cntCooking }}</span>
  </button>

  <button class="koki-filter-tab"
          data-filter="ready"
          role="tab" aria-selected="false">
    Siap Saji
    <span class="koki-tab-badge" id="badge-ready">{{ $cntReady }}</span>
  </button>

</nav>