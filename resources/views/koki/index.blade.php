@extends('layouts.koki')

@section('title', 'Dapur — Tskuy Smart Kasir')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/koki.css') }}">
@endsection

@section('content')

{{-- ── Filter Tab ────────────────────────────────────────── --}}
@include('components.koki-filter')

{{-- ── Grid Kartu Pesanan ───────────────────────────────── --}}
<div class="koki-content">
  <div class="orders-grid" id="ordersGrid">

    @forelse($orders as $order)
      @php
        /*
         * ─── Kalkulasi variabel tampilan per kartu ─────────
         *
         * STATUS WARNA KARTU:
         *   fresh   = < 5 menit   → hijau (normal)
         *   warning = 5–15 menit  → kuning/putih (perhatian)
         *   urgent  = > 15 menit  → merah (kritis!)
         *
         * Kalkulasi dilakukan server-side saat render,
         * lalu JS akan terus memperbaruinya secara live setiap detik.
         */

        $createdAt  = \Carbon\Carbon::parse($order['created_at']);
        $elapsedSec = now()->diffInSeconds($createdAt, false);
        $elapsedSec = max(0, $elapsedSec); // Pastikan tidak negatif
        $elapsedMin = (int) floor($elapsedSec / 60);

        // Tentukan kelas warna berdasarkan durasi tunggu
        if ($order['status'] === 'completed' || $order['status'] === 'ready') {
            $timerClass = 'card-done';
        } elseif ($elapsedSec >= 15 * 60) {
            $timerClass = 'card-urgent';   // > 15 mnt → MERAH
        } elseif ($elapsedSec >= 5 * 60) {
            $timerClass = 'card-warning';  // 5–15 mnt → KUNING
        } else {
            $timerClass = 'card-fresh';    // < 5 mnt  → HIJAU
        }

        // Override: kartu READY dan COMPLETED selalu tampil "done"
        if (in_array($order['status'], ['ready', 'completed'])) {
            $timerClass = 'card-done';
        }

        // Badge warna HANYA untuk tipe pesanan
        $badgeTipe  = $order['tipe_pesanan'] === 'Dine In' ? 'badge-dine-in' : 'badge-take-away';

        // Label identifikasi di atas kartu
        $identifier = $order['tipe_pesanan'] === 'Dine In' && $order['nomor_meja']
            ? 'MEJA ' . str_pad($order['nomor_meja'], 2, '0', STR_PAD_LEFT)
            : 'Order #' . $order['order_code'];

        // Status pill label
        $statusLabel = match($order['status']) {
            'pending'   => '🕐 Menunggu',
            'cooking'   => '🔥 Memasak',
            'ready'     => '✅ Siap Saji',
            'completed' => '☑ Selesai',
            default     => ucfirst($order['status']),
        };
        $statusPillClass = match($order['status']) {
            'pending'   => 'status-pending',
            'cooking'   => 'status-cooking',
            'ready'     => 'status-ready',
            'completed' => 'status-done',
            default     => '',
        };

        // Apakah kartu bisa diklik (bukan selesai/cancelled)
        $isClickable = !in_array($order['status'], ['completed', 'cancelled']);
      @endphp

      {{-- ── KARTU PESANAN ─────────────────────────── --}}
      <div class="order-card {{ $timerClass }}"
           id="card-{{ $order['id'] }}"
           data-order-id="{{ $order['id'] }}"
           data-order-code="{{ $order['order_code'] }}"
           data-status="{{ $order['status'] }}"
           data-tipe="{{ $order['tipe_pesanan'] }}"
           data-identifier="{{ $identifier }}"
           data-pelanggan="{{ $order['nama_pelanggan'] }}"
           data-meja="{{ $order['nomor_meja'] ?? '' }}"
           data-created-at="{{ $order['created_at'] }}"
           data-items="{{ json_encode($order['detail_pesanan']) }}"
           @if($isClickable)
             role="button"
             tabindex="0"
             aria-label="Buka detail pesanan {{ $identifier }}"
           @endif>

        {{-- ── HEADER KARTU ── --}}
        <div class="card-header">
          <div class="card-order-id">{{ $identifier }}</div>

          {{-- Timer badge — warna mengikuti timerClass --}}
          <div class="timer-badge">
            <span class="timer-dot"></span>
            <span class="timer-val" data-created-at="{{ $order['created_at'] }}">
              {{ $elapsedMin }} mnt
            </span>
          </div>
        </div>

        {{-- ── META KARTU: Badge tipe + info pelanggan ── --}}
        <div class="card-meta">

          {{--
            BADGE TIPE PESANAN:
            Warna UNGU  → hanya untuk Take Away
            Warna BIRU  → hanya untuk Dine In
            Warna kartu / background card TIDAK berubah
          --}}
          <span class="type-badge {{ $badgeTipe }}">
            {{ $order['tipe_pesanan'] }}
          </span>

          @if($order['tipe_pesanan'] === 'Dine In' && $order['nomor_meja'])
            <span class="card-meja-info">
              Meja <strong class="meja-num">{{ $order['nomor_meja'] }}</strong>
            </span>
          @endif

          <span class="card-customer" title="{{ $order['nama_pelanggan'] }}">
            👤 {{ Str::limit($order['nama_pelanggan'], 16) }}
          </span>
        </div>

        {{-- ── DAFTAR ITEM (Maks 3 di kartu, sisanya "+N lainnya") ── --}}
        <div class="card-items">
          @foreach(array_slice($order['detail_pesanan'], 0, 3) as $item)
            <div class="card-item-row">
              <span class="card-item-dot"></span>
              <span class="card-item-qty">×{{ $item->qty }}</span> {{-- 👈 Ubah jadi ->qty --}}
              <span class="card-item-name">{{ $item->nama }}</span> {{-- 👈 Ubah jadi ->nama --}}
              
              @if(!empty($item->catatan)) {{-- 👈 Ubah jadi ->catatan --}}
                <span class="card-item-note" title="{{ $item->catatan }}">📝</span>
              @endif
            </div>
          @endforeach

          @php $extraCount = count($order['detail_pesanan']) - 3; @endphp
          @if($extraCount > 0)
            <div class="card-more">+{{ $extraCount }} item lainnya</div>
          @endif
        </div>

        {{-- ── FOOTER KARTU: Status pill + chevron ── --}}
        <div class="card-footer">
          <span class="card-status-pill {{ $statusPillClass }}" data-status-pill>
            {{ $statusLabel }}
          </span>
          @if($isClickable)
            <svg class="card-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          @endif
        </div>

      </div>
      {{-- ── /KARTU ── --}}

    @empty
      {{-- State kosong --}}
      <div class="empty-state" style="grid-column: 1/-1;">
        <div class="empty-icon">🍽️</div>
        <div class="empty-title">Belum ada pesanan masuk</div>
        <div class="empty-desc">
          Pesanan dari pelanggan dan kasir akan muncul di sini secara otomatis.
        </div>
      </div>
    @endforelse

  </div>
</div>
{{-- ── /Grid ── --}}


{{-- ============================================================
     OVERLAY — backdrop semua modal
============================================================ --}}
<div id="modalOverlay" class="modal-overlay" aria-hidden="true"></div>


{{-- ============================================================
     DETAIL MODAL — muncul saat kartu diklik
============================================================ --}}
<div id="detailModal"
     class="custom-modal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modalTitle">

  {{-- Tombol tutup X --}}
  <button class="modal-close-btn" id="closeDetailBtn" aria-label="Tutup detail pesanan">
    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
      <path d="M1 1l12 12M13 1L1 13" stroke="currentColor" stroke-width="2"
            stroke-linecap="round"/>
    </svg>
  </button>

  {{-- Header Modal --}}
  <div class="modal-header">
    <div class="modal-order-id" id="modalTitle">—</div>
    <div class="modal-info-row" id="modalInfoRow">—</div>

    {{-- Timer modal --}}
    <div class="modal-timer" id="modalTimer">
      <span class="timer-dot"></span>
      <span id="modalTimerVal">0 mnt</span>
    </div>
  </div>

  {{-- Daftar Item --}}
  <div class="modal-items-wrap">
    <div class="modal-items-label">Daftar Pesanan</div>
    <div class="modal-items-list" id="modalItemsList">
      {{-- Diisi oleh koki.js --}}
    </div>
  </div>

  {{-- Progress Bar --}}
  <div class="modal-progress">
    <div class="progress-label">
      <span>Progress masak</span>
      <span>
        <span class="progress-done" id="progDone">0</span>/<span id="progTotal">0</span> item selesai
      </span>
    </div>
    <div class="progress-track">
      <div class="progress-fill" id="progFill" style="width: 0%"></div>
    </div>
  </div>

  {{-- Action Footer Modal --}}
  <div class="modal-foot">
    {{-- Tombol Mulai Masak (muncul saat status PENDING) --}}
    <button class="btn-mulai-masak btn-action-secondary"
            id="btnMulaiMasak"
            style="display:none;">
      🔥 Mulai Masak
    </button>

    {{-- Tombol Selesaikan (aktif saat semua item done / status COOKING) --}}
    <button class="btn-selesaikan cant-complete btn-action-primary"
            id="btnComplete"
            disabled>
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <path d="M5 13l4 4L19 7" stroke="currentColor"
              stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <span id="btnCompleteLabel">Selesaikan Pesanan (0/0)</span>
    </button>
  </div>

</div>
{{-- ── /Detail Modal ── --}}


{{-- ============================================================
     SUCCESS MODAL — muncul setelah pesanan di-selesaikan
============================================================ --}}
<div id="successModal"
     class="custom-modal success-modal"
     role="dialog"
     aria-modal="true">

  <div class="success-ring" aria-hidden="true">
    <svg width="44" height="44" viewBox="0 0 24 24" fill="none">
      <path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="2.8"
            stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>

  <div class="success-title">Pesanan Siap!</div>
  <p class="success-sub">
    Pesanan telah ditandai <strong>READY</strong> dan kasir sudah diberitahu.
  </p>
  <div class="success-detail" id="successDetail"></div>

  <button class="btn-back" id="btnBackToQueue">
    ← Kembali ke Antrian
  </button>

</div>
{{-- ── /Success Modal ── --}}

@endsection


@push('scripts')
{{--
  Data Route Laravel dikirim ke JS sebagai JSON global.
  Cara ini aman: tidak mengekspos token apapun, hanya URL route.
--}}
<script>
  // ⬇️ Ubah KOKI_ROUTES menjadi ROUTES aja biar sinkron sama JS bawah
  window.ROUTES = {
    mulaiMasak  : (id) => `{{ url('/koki') }}/${id}/mulai-masak`,
    selesaikan  : (id) => `{{ url('/koki') }}/${id}/selesaikan`,
    batalkan    : (id) => `{{ url('/koki') }}/${id}/batalkan`,
    updateItem  : (orderId, itemId) => `{{ url('/koki') }}/${orderId}/item/${itemId}/update`,
    apiOrders   : `{{ route('koki.api.orders') }}`,
    csrfToken   : `{{ csrf_token() }}`,
  };
</script>
@endpush