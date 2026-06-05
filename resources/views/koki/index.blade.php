@extends('layouts.koki')

@section('title', 'Dapur — Tskuy POS')

@section('content')
<div class="koki-main-container" style="flex: 1; width: 100%; padding: 24px; overflow-y: auto; height: calc(100vh - 64px);">

  {{-- COMPONENT FILTER ANTRIAN --}}
  @include('partials.koki-filter')

  {{-- MAIN CONTENT — GRID KARTU PESANAN MELEBAR --}}
  <div class="orders-grid" id="ordersGrid" style="margin-top: 24px; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">

    @forelse($orders as $order)
      @php
        $isDineIn    = $order['tipe_pesanan'] === 'Dine In';
        $isCompleted = $order['status'] === 'completed';

        $typeClass  = $isCompleted ? 'completed' : ($isDineIn ? 'dine-in' : 'takeaway');
        $badgeClass = $isDineIn ? 'badge-dine-in' : 'badge-takeaway';

        // Hitung durasi elapsed waktu pesanan
        $createdAt  = new \DateTime($order['created_at']);
        $elapsedMin = max(0, (int) round((now()->getTimestamp() - $createdAt->getTimestamp()) / 60));

        // Judul atas nota kartu
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

        {{-- ── CARD HEADER ── --}}
        <div class="card-header">
          <div class="card-order-id">{{ $identifier }}</div>
          <div class="timer-badge">
            <span class="timer-dot"></span>
            <span class="timer-val" data-created-at="{{ $order['created_at'] }}">
              {{ $elapsedMin }} mnt
            </span>
          </div>
        </div>

        {{-- ── CARD META ── --}}
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

        {{-- ── ITEM LIST (Maksimal 3 item muncul di muka kartu) ── --}}
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

        {{-- ── CARD FOOTER ── --}}
        <div class="card-footer">
          <span class="status-pill {{ $isCompleted ? 'status-completed' : 'status-pending' }}" data-status-pill>
            {{ $isCompleted ? '✓ Selesai' : 'Menunggu' }}
          </span>
          @if(!$isCompleted)
            <svg class="card-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          @endif
        </div>

      </div>

    @empty
      <div class="empty-state" style="grid-column: 1/-1; padding: 80px 20px;">
        <div class="empty-icon">🍽️</div>
        <div class="empty-title">Belum ada pesanan masuk</div>
        <div class="empty-desc">Pesanan dari pelanggan dan kasir akan muncul di sini secara otomatis.</div>
      </div>
    @endforelse

  </div>
</div>

{{-- ============================================================
     OVERLAYS MODALS (Di luar container agar posisinya fixed terpusat)
============================================================ --}}
<div id="modalOverlay" class="modal-overlay" aria-hidden="true"></div>

{{-- DETAIL MODAL --}}
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

  <div class="modal-items-wrap" id="modalItemsWrap"></div>

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
        <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <span id="btnCompleteLabel">Selesaikan Pesanan (0/0)</span>
    </button>
  </div>
</div>

{{-- SUCCESS MODAL --}}
<div id="successModal" class="success-modal" role="dialog" aria-modal="true">
  <div class="success-ring" aria-hidden="true">
    <svg width="44" height="44" viewBox="0 0 24 24" fill="none">
      <path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
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