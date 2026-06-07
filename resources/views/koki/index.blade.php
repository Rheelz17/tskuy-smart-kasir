@extends('layouts.koki')

@section('title', 'Dapur — Tskuy Smart Kasir')

@section('content')

{{-- ═══════════════════════ FILTER BAR ═══════════════════════ --}}
@include('partials.koki-filter')

{{-- ═══════════════════════ GRID KARTU ═══════════════════════ --}}
<div class="koki-board" id="kokiBoard">
  <div class="koki-grid" id="ordersGrid">

    @forelse($orders as $order)
      @php
        /*
         * ─── Kalkulasi status waktu ────────────────────────
         * < 5 mnt   → card-fresh   (hijau)
         * 5–15 mnt  → card-warning (kuning)
         * > 15 mnt  → card-urgent  (merah)
         * ready/completed → card-done
         */
        $createdAt  = \Carbon\Carbon::parse($order['created_at']);
        $elapsedSec = max(0, now()->diffInSeconds($createdAt, false));
        $elapsedMin = (int) floor($elapsedSec / 60);

        if (in_array($order['status'], ['ready', 'completed'])) {
          $urgencyClass = 'card-done';
        } elseif ($elapsedSec >= 15 * 60) {
          $urgencyClass = 'card-urgent';
        } elseif ($elapsedSec >= 5 * 60) {
          $urgencyClass = 'card-warning';
        } else {
          $urgencyClass = 'card-fresh';
        }

        // Badge tipe pesanan
        $isDineIn    = $order['tipe_pesanan'] === 'Dine In';
        $badgeTipe   = $isDineIn ? 'badge-dine' : 'badge-takeaway';

        // Identitas utama kartu = Order Code (bukan nomor meja)
        $primaryId  = '#' . ($order['order_code'] ?? $order['id']);
        $secondaryId = $isDineIn && !empty($order['nomor_meja'])
                     ? 'Meja ' . str_pad($order['nomor_meja'], 2, '0', STR_PAD_LEFT)
                     : 'Take Away';

        // Status pill
        $statusMap = [
          'pending'   => ['label' => 'Menunggu',  'class' => 'pill-pending'],
          'cooking'   => ['label' => 'Memasak',   'class' => 'pill-cooking'],
          'ready'     => ['label' => 'Siap Saji', 'class' => 'pill-ready'],
          'completed' => ['label' => 'Selesai',   'class' => 'pill-done'],
        ];
        $statusInfo = $statusMap[$order['status']] ?? ['label' => ucfirst($order['status']), 'class' => ''];
        $isClickable = !in_array($order['status'], ['ready', 'completed', 'cancelled']);

        // Items untuk data-attr (digunakan JS untuk modal)
        $itemsJson = json_encode($order['detail_pesanan']);
      @endphp

      {{-- ══════════ KARTU PESANAN ══════════ --}}
      <div class="order-card {{ $urgencyClass }}"
           id="card-{{ $order['id'] }}"
           data-order-id="{{ $order['id'] }}"
           data-order-code="{{ $order['order_code'] ?? $order['id'] }}"
           data-status="{{ $order['status'] }}"
           data-tipe="{{ $order['tipe_pesanan'] }}"
           data-primary-id="{{ $primaryId }}"
           data-secondary-id="{{ $secondaryId }}"
           data-pelanggan="{{ $order['nama_pelanggan'] }}"
           data-meja="{{ $order['nomor_meja'] ?? '' }}"
           data-created-at="{{ $order['created_at'] }}"
           data-items="{{ $itemsJson }}"
           @if($isClickable) role="button" tabindex="0" @endif>

        {{-- ── HEADER KARTU ── --}}
        <div class="card-head">
          {{-- ID Pesanan sebagai identitas utama --}}
          <div class="card-primary-id">{{ $primaryId }}</div>
          {{-- Timer badge --}}
          <div class="card-timer {{ $urgencyClass }}">
            <span class="timer-dot"></span>
            <span class="timer-val" data-created-at="{{ $order['created_at'] }}">
              {{ $elapsedMin }} mnt
            </span>
          </div>
        </div>

        {{-- ── META: tipe + meja/customer ── --}}
        <div class="card-meta">
          <span class="badge {{ $badgeTipe }}">{{ $order['tipe_pesanan'] }}</span>
          <span class="card-secondary-id">{{ $secondaryId }}</span>
        </div>

        {{-- ── Nama pelanggan ── --}}
        <div class="card-customer">
          {{ Str::limit($order['nama_pelanggan'] ?? 'Pelanggan', 22) }}
        </div>

        {{-- ── DAFTAR ITEM (maks 3) ── --}}
        <div class="card-items-list">
          @foreach(array_slice((array) $order['detail_pesanan'], 0, 3) as $item)
            @php
              // Support both object and array item format
              $nama    = is_object($item) ? $item->nama    : ($item['nama']    ?? '');
              $qty     = is_object($item) ? $item->qty     : ($item['qty']     ?? 0);
              $catatan = is_object($item) ? $item->catatan : ($item['catatan'] ?? '');
            @endphp
            <div class="card-item">
              <span class="card-item-qty">[{{ $qty }}x]</span>
              <span class="card-item-name">{{ $nama }}</span>
              @if(!empty($catatan))
                <span class="card-item-note-icon" title="{{ $catatan }}">*</span>
              @endif
            </div>
          @endforeach

          @php $extra = count((array) $order['detail_pesanan']) - 3; @endphp
          @if($extra > 0)
            <div class="card-item-more">+{{ $extra }} item lainnya</div>
          @endif
        </div>

        {{-- ── FOOTER: status pill + chevron ── --}}
        <div class="card-foot">
          <span class="status-pill {{ $statusInfo['class'] }}" data-status-pill>
            {{ $statusInfo['label'] }}
          </span>
          @if($isClickable)
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="card-chevron">
              <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          @endif
        </div>

      </div>
      {{-- ══════════ /KARTU ══════════ --}}

    @empty
      <div class="koki-empty" style="grid-column:1/-1">
        <div class="koki-empty-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M3 11l19-9-9 19-2-8-8-2z" stroke="#d1d5db" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="koki-empty-title">Belum ada pesanan masuk</div>
        <div class="koki-empty-desc">
          Pesanan dari pelanggan dan kasir akan muncul di sini secara otomatis.
        </div>
      </div>
    @endforelse

  </div>
</div>

{{-- ═══════════════════════ DETAIL POPUP ═══════════════════════ --}}
<div class="popup koki-popup" id="popupDetail" role="dialog" aria-modal="true" aria-labelledby="popupDetailTitle">

  {{-- Header popup kuning --}}
  <div class="popup-header popup-header-yellow">
    <span id="popupDetailTitle">Detail Pesanan</span>
    <button class="popup-close popup-close-white" id="btnCloseDetail" type="button" aria-label="Tutup">
      <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
        <path d="M1 1l12 12M13 1L1 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>

  {{-- Body popup --}}
  <div class="popup-body koki-popup-body">

    {{-- Identitas pesanan --}}
    <div class="koki-popup-identity">
      <div class="koki-popup-order-id" id="popupOrderId">—</div>
      <div class="koki-popup-meta" id="popupMeta">—</div>
    </div>

    {{-- Daftar item (checklist nyicil) --}}
    <div class="koki-popup-items" id="popupItemsList">
      {{-- Diisi oleh koki.js --}}
    </div>

    {{-- Progress bar --}}
    <div class="koki-progress-wrap">
      <div class="koki-progress-label">
        <span>Progress masak</span>
        <span>
          <span id="progDone">0</span>/<span id="progTotal">0</span> item
        </span>
      </div>
      <div class="koki-progress-track">
        <div class="koki-progress-fill" id="progFill" style="width:0%"></div>
      </div>
    </div>

    {{-- Tombol aksi --}}
    <div class="koki-popup-actions">

      {{-- Tombol Mulai Masak — hanya muncul saat PENDING --}}
      <button class="popup-btn" id="btnMulaiMasak" type="button" style="display:none">
        Mulai Masak
      </button>

      {{-- Tombol Selesaikan — disabled sampai semua item dicentang --}}
      <button class="popup-btn popup-btn-green koki-btn-selesai" id="btnSelesaikan"
              type="button" disabled>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="flex-shrink:0">
          <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span id="btnSelesaikanLabel">Tandai Sebagai Selesai (0/0)</span>
      </button>

      {{-- Tombol Batalkan —  hanya muncul saat PENDING --}}
      <button class="popup-btn popup-btn-danger koki-btn-batal" id="btnBatalkan"
              type="button" style="display:none">
        Batalkan Pesanan
      </button>

    </div>

  </div>
</div>

{{-- ═══════════════════════ SUCCESS POPUP ═══════════════════════ --}}
<div class="popup koki-popup koki-popup-success" id="popupSuccess" role="dialog" aria-modal="true">

  <div class="popup-header popup-header-yellow">
    <span>Pesanan Siap</span>
    <button class="popup-close popup-close-white" id="btnCloseSuccess" type="button" aria-label="Tutup">
      <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
        <path d="M1 1l12 12M13 1L1 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>

  <div class="popup-body" style="text-align:center; padding:28px 24px;">
    <div class="koki-success-icon">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
        <path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="2.8"
              stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <div class="koki-success-title">Pesanan Siap!</div>
    <p class="koki-success-sub">Pesanan telah ditandai <strong>READY</strong> dan kasir sudah diberitahu.</p>
    <div class="koki-success-detail" id="successDetail"></div>
    <button class="popup-btn" id="btnBackToQueue" type="button" style="margin-top:16px">
      Kembali ke Antrian
    </button>
  </div>
</div>

{{-- ═══════════════════════ NOTIF PANEL (referensi kasir) ═══════════════════════ --}}
<div class="notif-panel" id="notifPanel">
  <div class="notif-panel-header">
    <h3>Notifikasi</h3>
    <button class="notif-read-link" id="btnReadAll" type="button">Tandai sudah dibaca</button>
  </div>
  <div class="notif-panel-list" id="notifList">
    <div class="notif-panel-item">
      <div class="notif-panel-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="#efb100">
          <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"
                stroke="#efb100" stroke-width="1.5" fill="none" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="notif-panel-content">
        <div class="notif-panel-title">Siap menerima pesanan</div>
        <div class="notif-panel-desc">Halaman dapur aktif dan terhubung.</div>
        <div class="notif-panel-time">Baru saja</div>
      </div>
    </div>
  </div>
  <button class="notif-panel-footer" type="button" id="btnNotifFooter">Lihat Semua Notifikasi</button>
</div>

@endsection

@section('scripts')
<script>
  /**
   * ROUTES — dikirim dari Laravel ke JS via blade.
   * Menggunakan window.ROUTES agar sinkron dengan koki.js.
   */
  window.ROUTES = {
    mulaiMasak  : (id) => `/koki/${id}/mulai-masak`,
    selesaikan  : (id) => `/koki/${id}/selesaikan`,
    batalkan    : (id) => `/koki/${id}/batalkan`,
    apiOrders   : `/koki/api/orders`,
    csrfToken   : '{{ csrf_token() }}',
  };
</script>
<script src="{{ asset('js/koki.js') }}"></script>
@endsection

