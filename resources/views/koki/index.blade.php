@extends('koki.koki')

@section('title', 'Dapur — Tskuy POS')

@section('content')
<div class="koki-app">

  {{-- ══════════════════════════════════════════════════════════════
       HEADER
  ══════════════════════════════════════════════════════════════ --}}
  <header class="koki-header">
    <div class="header-left">
      <div class="logo-circle">
        <img src="{{ asset('image/logo_warkop.png') }}" alt="Tskuy">
      </div>
      <div>
        <div class="header-title">🍳 Dapur Tskuy</div>
        <div class="header-subtitle">Kitchen Console</div>
      </div>
    </div>
    <div class="header-right">
      <span class="live-clock" id="liveClock">--:--:--</span>
      <div class="user-pill">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=efb100&color=fff"
             alt="{{ Auth::user()->name }}">
        <span>{{ Auth::user()->name }}</span>
      </div>
    </div>
  </header>

  {{-- ══════════════════════════════════════════════════════════════
       FILTER COMPONENT — counter diperbarui oleh JS secara live
  ══════════════════════════════════════════════════════════════ --}}
  @include('components.koki-filter')

  {{-- ══════════════════════════════════════════════════════════════
       MAIN CONTENT — GRID KARTU PESANAN
  ══════════════════════════════════════════════════════════════ --}}
  <main class="koki-content">
    <div class="orders-grid" id="ordersGrid">

      @forelse($orders as $order)
        @php
          /*
           * Tentukan class kartu berdasarkan tipe & status:
           *   .dine-in   → Dine In (accent bar ungu)
           *   .takeaway  → Takeaway (accent bar biru)
           *   .urgent    → ditambahkan oleh JS saat elapsed >= 20 mnt
           *   .completed → pesanan sudah selesai
           *
           * Card identifier:
           *   Dine In  → tampilkan nomor meja (MEJA XX)
           *   Takeaway → tampilkan ID nota (Order #TXXXX)
           */
          $isDineIn    = $order['tipe_pesanan'] === 'Dine In';
          $isCompleted = $order['status'] === 'completed';

          $typeClass  = $isCompleted ? 'completed' : ($isDoneIn ?? ($isDineIn ? 'dine-in' : 'takeaway'));
          $typeClass  = $isCompleted ? 'completed' : ($isDineIn ? 'dine-in' : 'takeaway');
          $badgeClass = $isDineIn ? 'badge-dine-in' : 'badge-takeaway';

          // Hitung elapsed untuk render awal (JS akan update tiap 10 detik)
          $createdAt  = new \DateTime($order['created_at']);
          $elapsedMin = max(0, (int) round((now()->getTimestamp() - $createdAt->getTimestamp()) / 60));

          // Identifier header kartu
          $identifier = $isDineIn
            ? 'MEJA ' . str_pad($order['nomor_meja'], 2, '0', STR_PAD_LEFT)
            : 'Order #' . $order['id_nota'];
        @endphp

        <div class="order-card {{ $typeClass }}"
             id="card-{{ $order['id'] }}"
             data-order-id="{{ $order['id'] }}"
             data-status="{{ $order['status'] }}"
             data-tipe="{{ $order['tipe_pesanan'] }}"
             data-pelanggan="{{ $order['nama_pelanggan'] }}"
             data-meja="{{ $order['nomor_meja'] ?? '' }}"
             data-nota="{{ $order['id_nota'] }}"
             data-created-at="{{ $order['created_at'] }}"
             data-items="{{ json_encode($order['detail_pesanan']) }}"
             role="button"
             tabindex="{{ $isCompleted ? '-1' : '0' }}">

          {{-- ── CARD HEADER ────────────────────────────────── --}}
          <div class="card-header">
            <div class="card-order-id">{{ $identifier }}</div>
            <div class="timer-badge">
              <span class="timer-dot"></span>
              <span class="timer-val" data-created-at="{{ $order['created_at'] }}">
                {{ $elapsedMin }} mnt
              </span>
            </div>
          </div>

          {{-- ── CARD META (badge tipe + meja jika Dine In) ── --}}
          <div class="card-meta">
            <span class="type-badge {{ $badgeClass }}">
              {{ $order['tipe_pesanan'] }}
            </span>

            @if($isDineIn && $order['nomor_meja'])
              <span class="card-meja-info">
                Meja <span class="meja-num">{{ $order['nomor_meja'] }}</span>
              </span>
            @endif

            <span class="card-customer">👤 {{ $order['nama_pelanggan'] }}</span>
          </div>

          {{-- ── ITEM LIST (maksimal 3 item tampil di kartu) ── --}}
          <div class="card-items">
            @foreach(array_slice($order['detail_pesanan'], 0, 3) as $item)
              <div class="card-item-row">
                <span class="card-item-dot"></span>
                <span class="card-item-qty">×{{ $item['qty'] }}</span>
                <span class="card-item-name">{{ $item['nama'] }}</span>
              </div>
            @endforeach

            @if(count($order['detail_pesanan']) > 3)
              <div class="card-more">+{{ count($order['detail_pesanan']) - 3 }} item lainnya</div>
            @endif
          </div>

          {{-- ── CARD FOOTER (status pill) ─────────────────── --}}
          <div class="card-footer">
            <span class="status-pill {{ $isCompleted ? 'status-completed' : 'status-pending' }}"
                  data-status-pill>
              {{ $isCompleted ? '✓ Selesai' : 'Menunggu' }}
            </span>
            @if(!$isCompleted)
              <svg class="card-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            @endif
          </div>

        </div>

      @empty
        <div class="empty-state" style="grid-column:1/-1">
          <div class="empty-icon">🍽️</div>
          <div class="empty-title">Belum ada pesanan masuk</div>
          <div class="empty-desc">Pesanan dari pelanggan dan kasir akan muncul di sini secara otomatis.</div>
        </div>
      @endforelse

    </div>{{-- /#ordersGrid --}}
  </main>

</div>{{-- /.koki-app --}}

{{-- ══════════════════════════════════════════════════════════════
     MODAL OVERLAY (global, dipakai kedua modal)
══════════════════════════════════════════════════════════════ --}}
<div id="modalOverlay" class="modal-overlay" aria-hidden="true"></div>

{{-- ══════════════════════════════════════════════════════════════
     DETAIL MODAL — koki klik item satu per satu
══════════════════════════════════════════════════════════════ --}}
<div id="detailModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">

  <button class="modal-close" id="modalCloseBtn" aria-label="Tutup modal">
    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
      <path d="M1 1l12 12M13 1L1 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
  </button>

  <div class="modal-head">
    <div class="modal-order-id" id="modalTitle">—</div>
    <div class="modal-info-row" id="modalInfoRow">—</div>
  </div>

  {{-- List item (diisi JS lewat renderModalItems) --}}
  <div class="modal-items-wrap" id="modalItemsWrap"></div>

  {{-- Progress bar --}}
  <div class="modal-progress">
    <div class="progress-label">
      <span>Progress masak</span>
      <span>
        <span class="progress-done" id="progDone">0</span>/<span id="progTotal">0</span> selesai
      </span>
    </div>
    <div class="progress-track">
      <div class="progress-fill" id="progFill" style="width:0%"></div>
    </div>
  </div>

  <div class="modal-foot">
    <button class="btn-complete cant-complete" id="btnComplete" disabled>
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5"
              stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <span id="btnCompleteLabel">Selesaikan Pesanan (0/0)</span>
    </button>
  </div>

</div>

{{-- ══════════════════════════════════════════════════════════════
     SUCCESS MODAL — muncul setelah "Selesaikan Pesanan" diklik
══════════════════════════════════════════════════════════════ --}}
<div id="successModal" class="success-modal" role="dialog" aria-modal="true">
  <div class="success-ring" aria-hidden="true">
    <svg width="44" height="44" viewBox="0 0 24 24" fill="none">
      <path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="2.8"
            stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
  <div class="success-title">Pesanan Berhasil Dibuat!</div>
  <p class="success-sub">Terima kasih telah bekerja keras untuk membuat pesanan ini!</p>
  <div class="success-detail" id="successDetail"></div>
  <button class="btn-back" id="btnBackToQueue">← Kembali ke Antrian</button>
</div>

@endsection

@section('scripts')

<script src="{{ asset('js/koki.js') }}"></script>
@endsection