@extends('layouts.kasir')

@section('title', 'Dashboard Kasir - Tskuy POS')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/style-dashboardKasir.css') }}" />
<style>
/* ── Koki-style urgency untuk queue-card ── */
.queue-card { border-radius: 12px; padding: 12px 14px; min-width: 175px; max-width: 210px;
              box-shadow: 0 2px 8px rgba(0,0,0,.06); flex-shrink:0; transition: transform .15s, box-shadow .15s; }
.queue-card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.10); }

.card-fresh   { background: #f0fdf4; border: 1.5px solid #bbf7d0; }
.card-warning { background: #fffbeb; border: 1.5px solid #fde68a; }
.card-urgent  { background: #fef2f2; border: 1.5px solid #fecaca; }
.card-done    { background: #f9fafb; border: 1.5px solid #e5e7eb; opacity:.7; }

/* Status pills */
.status-pill { display:inline-block; border-radius:20px; font-size:10px; font-weight:600; padding:2px 9px; }
.pill-pending { background:#fef3c7; color:#92400e; }
.pill-cooking { background:#dbeafe; color:#1e40af; }
.pill-ready   { background:#dcfce7; color:#166534; }
.pill-done    { background:#f3f4f6; color:#6b7280; }

/* Badge open bill */
.badge-openbill { background:#fef3c7; color:#92400e; font-size:10px; padding:2px 7px; border-radius:20px; font-weight:600; }

/* queue-header-row1 — hanya aktif di mobile */
.queue-header-row1 { display: flex; align-items: center; }

/* Desktop/tablet: row1 cuma tampil title saja, mobile see-all disembunyikan */
@media (min-width: 768px) {
  #btn-see-all-antrian-mobile { display: none !important; }
  .queue-header-row1 { display: contents; } /* title langsung dalam flex flow */
}

/* Mobile: desktop see-all disembunyikan, mobile see-all tampil */
@media (max-width: 767px) {
  #btn-see-all-antrian { display: none !important; }
  #btn-see-all-antrian-mobile { display: flex !important; }
  .queue-header-left { width: 100%; }
}

/* Sa-list row hover */
.sa-row:hover { box-shadow: 0 2px 10px rgba(0,0,0,.08); }
.sa-row.card-fresh   { background: #f0fdf4; border: 1.5px solid #bbf7d0; }
.sa-row.card-warning { background: #fffbeb; border: 1.5px solid #fde68a; }
.sa-row.card-urgent  { background: #fef2f2; border: 1.5px solid #fecaca; }
.sa-row.card-done    { background: #f9fafb; border: 1.5px solid #e5e7eb; opacity:.8; }
</style>
@endsection

@section('content')
<main class="content-area">

    {{-- ============================================================
         SECTION ANTRIAN — Data real dari DB
    ============================================================ --}}
    <section class="queue-section">
        <div class="queue-header">

            {{-- KIRI: Title + Filter Tabs --}}
            <div class="queue-header-left">
                {{-- Baris 1 di mobile: Title + See All --}}
                <div class="queue-header-row1">
                    <h3 class="section-title">Antrian</h3>
                    {{-- See All hanya tampil di sini di mobile --}}
                    <button id="btn-see-all-antrian-mobile" type="button" class="queue-see-all" style="display:none;">
                        See All
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                            <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                {{-- Filter Tabs --}}
                <div class="antrian-tabs">
                    <button class="antrian-tab antrian-tab-active" data-filter="all">
                        All <span class="tab-badge tab-badge-yellow">{{ $antrianCount['all'] }}</span>
                    </button>
                    <button class="antrian-tab" data-filter="pending">
                        Pending <span class="tab-badge tab-badge-outline">{{ $antrianCount['pending'] }}</span>
                    </button>
                    <button class="antrian-tab" data-filter="dine">
                        Dine in <span class="tab-badge tab-badge-outline">{{ $antrianCount['dine'] }}</span>
                    </button>
                    <button class="antrian-tab" data-filter="takeaway">
                        Take away <span class="tab-badge tab-badge-outline">{{ $antrianCount['takeaway'] }}</span>
                    </button>
                    <button class="antrian-tab" data-filter="openbill">
                        Open Bill <span class="tab-badge tab-badge-outline">{{ $antrianCount['openbill'] }}</span>
                    </button>
                </div>
            </div>

            {{-- KANAN: See All (desktop/tablet) --}}
            <button id="btn-see-all-antrian" type="button" class="queue-see-all">
                See All
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

        </div>

        <div class="queue-wrapper">

            {{-- Arrow Kiri --}}
            <button class="nav-arrow left" id="queue-prev" type="button" aria-label="Geser kiri">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <div class="queue-container" id="queue-container">
                @forelse($antrian as $order)
                    @php
                        /*
                         * Urgency class mengikuti pola koki:
                         * < 10 mnt  → queue-green  (card-fresh)
                         * 10–20 mnt → queue-yellow (card-warning)
                         * > 20 mnt  → queue-red    (card-urgent)
                         * READY     → card-done
                         */
                        $createdAt  = \Carbon\Carbon::parse($order->created_at);
                        $elapsedSec = max(0, now()->diffInSeconds($createdAt, false));
                        $elapsedMin = (int) floor($elapsedSec / 60);

                        if ($order->status === 'READY') {
                            $queueUrgency = 'card-done';
                        } elseif ($elapsedSec >= 20 * 60) {
                            $queueUrgency = 'card-urgent';
                        } elseif ($elapsedSec >= 10 * 60) {
                            $queueUrgency = 'card-warning';
                        } else {
                            $queueUrgency = 'card-fresh';
                        }

                        // Badge tipe
                        if ($order->is_open_bill) {
                            $badgeClass = 'badge-openbill';
                            $badgeLabel = 'Open Bill';
                        } elseif ($order->filter_type === 'takeaway') {
                            $badgeClass = 'badge-takeaway';
                            $badgeLabel = 'Take Away';
                        } else {
                            $badgeClass = 'badge-dine';
                            $badgeLabel = 'Dine In';
                        }

                        // Primary ID = order code
                        $primaryId   = '#' . ($order->order_code ?? $order->id);
                        $secondaryId = $order->table_number
                                     ? 'Meja ' . str_pad($order->table_number, 2, '0', STR_PAD_LEFT)
                                     : 'Take Away';

                        // Status pill
                        $statusMap = [
                            'PENDING'   => ['label' => 'Menunggu',  'class' => 'pill-pending'],
                            'COOKING'   => ['label' => 'Memasak',   'class' => 'pill-cooking'],
                            'READY'     => ['label' => 'Siap Saji', 'class' => 'pill-ready'],
                        ];
                        $statusInfo = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => ''];

                        // Encode items untuk data-attr — sertakan harga dari DB
                        $itemsJson = json_encode($order->items->map(fn($i) => [
                            'nama'    => $i->menu_name,
                            'qty'     => $i->quantity,
                            'catatan' => $i->note ?? '',
                            'harga'   => (int) ($i->price ?? 0),
                        ])->toArray());
                    @endphp

                    <div class="queue-card {{ $queueUrgency }}"
                         id="qcard-{{ $order->id }}"
                         data-type="{{ $order->filter_type }}"
                         data-order-id="{{ $order->id }}"
                         data-order-code="{{ $order->order_code ?? $order->id }}"
                         data-status="{{ $order->status }}"
                         data-primary-id="{{ $primaryId }}"
                         data-secondary-id="{{ $secondaryId }}"
                         data-tipe="{{ $badgeLabel }}"
                         data-total="{{ $order->total }}"
                         data-created-at="{{ $order->created_at }}"
                         data-items="{{ $itemsJson }}"
                         role="button" tabindex="0"
                         style="cursor:pointer;">

                        {{-- Header kartu --}}
                        <div class="card-top" style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
                            <div class="card-primary-id" style="font-size:13px; font-weight:700; color:#1a1a1a;">{{ $primaryId }}</div>
                            <div class="card-timer {{ $queueUrgency }}" style="display:flex; align-items:center; gap:4px; font-size:11px; font-weight:600;">
                                <span class="timer-dot" style="width:6px; height:6px; border-radius:50%; display:inline-block; background:{{ $queueUrgency === 'card-fresh' ? '#22c55e' : ($queueUrgency === 'card-warning' ? '#f59e0b' : ($queueUrgency === 'card-done' ? '#6b7280' : '#ef4444')) }};"></span>
                                <span class="timer-val" data-created-at="{{ $order->created_at }}" style="color:{{ $queueUrgency === 'card-fresh' ? '#16a34a' : ($queueUrgency === 'card-warning' ? '#d97706' : ($queueUrgency === 'card-done' ? '#6b7280' : '#dc2626')) }};">
                                    {{ $elapsedMin }} mnt
                                </span>
                            </div>
                        </div>

                        {{-- Meta: tipe + meja --}}
                        <div class="card-meta" style="display:flex; align-items:center; gap:6px; margin-bottom:5px;">
                            <span class="badge {{ $badgeClass }}" style="font-size:10px; padding:2px 7px;">{{ $badgeLabel }}</span>
                            <span style="font-size:11px; color:#888;">{{ $secondaryId }}</span>
                        </div>

                        {{-- Item list (maks 2) --}}
                        <div style="margin-bottom:8px;">
                            @foreach($order->items->take(2) as $item)
                                <div style="font-size:11px; color:#444; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    <span style="color:#efb100; font-weight:600;">[{{ $item->quantity }}x]</span> {{ $item->menu_name }}
                                </div>
                            @endforeach
                            @if($order->items->count() > 2)
                                <div style="font-size:10px; color:#aaa;">+{{ $order->items->count() - 2 }} item lainnya</div>
                            @endif
                        </div>

                        {{-- Footer: status pill + chevron --}}
                        <div class="card-foot" style="display:flex; justify-content:space-between; align-items:center; padding-top:7px; border-top:1px solid rgba(0,0,0,0.06);">
                            <span class="status-pill {{ $statusInfo['class'] }}" style="font-size:10px; padding:2px 8px; border-radius:20px; font-weight:600;">
                                {{ $statusInfo['label'] }}
                            </span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                                <path d="M9 18l6-6-6-6" stroke="#bbb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>

                @empty
                    <div style="display:flex; flex-direction:column; align-items:center; gap:8px; padding:24px 40px; color:#aaa; font-size:13px; min-width:200px;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" stroke="#ddd" stroke-width="1.8"/><rect x="9" y="3" width="6" height="4" rx="1" stroke="#ddd" stroke-width="1.8"/></svg>
                        <p>Belum ada antrian aktif</p>
                    </div>
                @endforelse
            </div>

            {{-- Arrow Kanan --}}
            <button class="nav-arrow right" id="queue-next" type="button" aria-label="Geser kanan">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18L15 12L9 6" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

        </div>

        <div class="pagination-dots" id="pagination-dots">
            <span class="dot active"></span>
        </div>
    </section>

    {{-- ============================================================
         SECTION MOOD/TAG FILTER — Tetap static (tidak ada di DB)
    ============================================================ --}}
    <section class="mood-section">
        <p class="section-label">Rekomendasi & Performa Menu:</p>
        <p class="section-labelmini">Saring menu terlaris atau promo aktif secara cepat</p>
        <div class="kategori-mood">
            <button class="mood active-mood" data-tag="semua">Semua</button>
            <button class="mood" data-tag="bestseller">
                <img src="{{ asset('image/bestseller.png') }}" alt="Best Seller" class="icon">
                Best Seller
            </button>
            <button class="mood" data-tag="promo">
                <img src="{{ asset('image/promo.png') }}" alt="promo" class="icon">
                Promo Active
            </button>
            <button class="mood" data-tag="rekomendasi">
                <img src="{{ asset('image/rekomendasi.png') }}" alt="rekomendasi" class="icon">
                Rekomendasi
            </button>
            <button class="mood" data-tag="baru">
                <img src="{{ asset('image/baru.png') }}" alt="menu baru" class="icon">
                Menu Baru
            </button>
        </div>
    </section>

    {{-- ============================================================
         SECTION MENU — Loop dari $categories (DB)
    ============================================================ --}}
    <section class="menu-choice">
        <div class="kategori-menu">
            <p class="section-label">Pilihan Menu:</p>
            <div class="kategori-menu-btn">
                <button class="menu-tab active-tab" data-filter="all">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Semua
                </button>
                @foreach($categories as $category)
                    @if($category->menus->count() > 0)
                    <button class="menu-tab" data-filter="{{ strtolower($category->name) }}">
                        {{ $category->name }}
                        <small>{{ $category->menus->count() }} Jenis</small>
                    </button>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="menu-grid">
            @foreach($categories as $category)
                @foreach($category->menus as $menu)
                <div class="menu-card"
                     data-kategori="{{ strtolower($category->name) }}"
                     data-tag="{{ $menu->tag ?? 'rekomendasi' }}"
                     data-id="{{ $menu->id }}"
                     data-open="popup-detail-menu-{{ $menu->id }}">
                    <div class="card-img">
                        <img src="{{ asset('image/' . $menu->image) }}" alt="{{ $menu->name }}">
                    </div>
                    <div class="card-body">
                        <p class="card-judul">{{ $menu->name }}</p>
                        <p class="card-desc">{{ Str::limit($menu->description, 35) }}</p>
                        <p class="card-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        <div class="qty-card">
                            <span class="qty-btn plus">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                                </svg>
                            </span>
                            <span class="qty-tambah">Tambah</span>
                        </div>
                    </div>
                </div>
                @endforeach
            @endforeach
        </div>
    </section>
</main>

{{-- ============================================================
     CART SIDEBAR — Tetap sama, dikendalikan JS
============================================================ --}}
<aside class="cart-sidebar">
    <div class="cart-header">
        <div class="cart-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="white" stroke-width="1.8" stroke-linejoin="round" />
                <path d="M3 6H21" stroke="white" stroke-width="1.8" />
                <path d="M16 10C16 12.2 14.2 14 12 14C9.8 14 8 12.2 8 10" stroke="white" stroke-width="1.8" stroke-linecap="round" />
            </svg>
            Pesanan Saat Ini
        </div>
        <button class="clear-btn" data-open="popup-hapus-keranjang">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M3 6H5H21" stroke="white" stroke-width="2" stroke-linecap="round" />
                <path d="M8 6V4H16V6M19 6V20C19 21.1 18.1 22 17 22H7C5.9 22 5 21.1 5 20V6H19Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    <div class="cart-content">
        <div class="customer-section">
            <p class="cart-section-label">Informasi Pelanggan</p>
            <input type="text" class="customer-input" id="customer-name-input" placeholder="Nama Pelanggan">
        </div>

        <div class="order-details">
            <p class="cart-section-label">Detail Pesanan</p>
            <div id="cart-items-container">
                <div class="cart-empty" id="cart-empty-msg" style="display:flex; flex-direction:column; align-items:center; gap:10px; padding:24px 0; color:#aaa; font-size:13px;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                        <path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="#ddd" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M3 6H21" stroke="#ddd" stroke-width="1.8"/>
                    </svg>
                    <p>Keranjang masih kosong</p>
                </div>
            </div>
        </div>
    </div>

    <div class="summary-section">
        <p class="summary-label">Ringkasan Order</p>
        <div class="summary-card">
            <div class="summary-row">
                <span class="label">Subtotal</span>
                <span class="value" id="cart-subtotal">Rp 0</span>
            </div>
            <div class="summary-row">
                <span class="label">Tax (10%)</span>
                <span class="value" id="cart-tax">Rp 0</span>
            </div>
            <div class="garis"></div>
            <div class="summary-row total">
                <span class="label total-label">Total</span>
                <span class="value total-value" id="cart-total-price">Rp 0</span>
            </div>
        </div>
    </div>

    <div class="cart-footer">
        <button class="order-btn" id="btn-order-now">Order Now</button>
    </div>
</aside>
@endsection

@section('page_popups')
{{-- ============================================================
     POPUP SEE ALL ANTRIAN — Daftar lengkap dengan filter
============================================================ --}}
<div class="popup" id="popup-see-all-antrian" style="max-width:560px; width:95vw;">
    <div class="popup-header popup-header-yellow">
        <div style="display:flex; align-items:center; gap:10px; flex:1; min-width:0;">
            <span>Semua Antrian</span>
            {{-- Indikator realtime --}}
            <span id="sa-live-dot" style="display:flex; align-items:center; gap:5px; font-size:11px; color:rgba(255,255,255,.75); font-weight:500;">
                <span style="width:7px; height:7px; border-radius:50%; background:#4ade80; animation:pulseLive 1.5s infinite;"></span>
                Live
            </span>
        </div>
        <button class="popup-close popup-close-white" data-close>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/>
                <path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
    <div class="popup-body" style="padding:0;">

        {{-- Filter tabs — badge count diupdate JS --}}
        <div style="display:flex; gap:6px; padding:14px 16px 10px; border-bottom:1px solid #f0f0f0; overflow-x:auto; scrollbar-width:none;">
            <button class="sa-tab sa-tab-active" data-sa-filter="all"
                    style="white-space:nowrap; padding:5px 12px; border-radius:20px; border:none; cursor:pointer; font-size:12px; font-weight:600; background:#efb100; color:#fff;">
                Semua <span class="sa-badge" id="sa-badge-all">0</span>
            </button>
            <button class="sa-tab" data-sa-filter="pending"
                    style="white-space:nowrap; padding:5px 12px; border-radius:20px; border:1.5px solid #e5e7eb; cursor:pointer; font-size:12px; font-weight:600; background:#fff; color:#555;">
                Pending <span class="sa-badge" id="sa-badge-pending">0</span>
            </button>
            <button class="sa-tab" data-sa-filter="dine"
                    style="white-space:nowrap; padding:5px 12px; border-radius:20px; border:1.5px solid #e5e7eb; cursor:pointer; font-size:12px; font-weight:600; background:#fff; color:#555;">
                Dine In <span class="sa-badge" id="sa-badge-dine">0</span>
            </button>
            <button class="sa-tab" data-sa-filter="takeaway"
                    style="white-space:nowrap; padding:5px 12px; border-radius:20px; border:1.5px solid #e5e7eb; cursor:pointer; font-size:12px; font-weight:600; background:#fff; color:#555;">
                Take Away <span class="sa-badge" id="sa-badge-takeaway">0</span>
            </button>
            <button class="sa-tab" data-sa-filter="openbill"
                    style="white-space:nowrap; padding:5px 12px; border-radius:20px; border:1.5px solid #e5e7eb; cursor:pointer; font-size:12px; font-weight:600; background:#fff; color:#555;">
                Open Bill <span class="sa-badge" id="sa-badge-openbill">0</span>
            </button>
        </div>

        {{-- Timestamp terakhir update --}}
        <div id="sa-last-update" style="font-size:10px; color:#bbb; text-align:right; padding:6px 16px 0;">
            Memuat data...
        </div>

        {{-- List antrian — dirender JS --}}
        <div id="sa-list" style="max-height:60vh; overflow-y:auto; padding:8px 12px 12px; display:flex; flex-direction:column; gap:8px;">
            {{-- Skeleton loading awal --}}
            @for($i = 0; $i < 3; $i++)
            <div class="sa-skeleton" style="height:62px; border-radius:12px; background:linear-gradient(90deg,#f0f0f0 25%,#e8e8e8 50%,#f0f0f0 75%); background-size:200% 100%; animation:shimmer 1.4s infinite;"></div>
            @endfor
        </div>

        {{-- Empty state --}}
        <div id="sa-empty" style="display:none; text-align:center; padding:40px 0; color:#bbb; font-size:13px;">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" style="margin:0 auto 8px; display:block;">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" stroke="#ddd" stroke-width="1.8"/>
                <rect x="9" y="3" width="6" height="4" rx="1" stroke="#ddd" stroke-width="1.8"/>
            </svg>
            Tidak ada antrian untuk filter ini
        </div>

    </div>
</div>

<style>
@keyframes pulseLive {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.5; transform:scale(1.3); }
}
@keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>

{{-- ============================================================
     POPUP DETAIL ANTRIAN — Satu popup dinamis (diisi JS)
============================================================ --}}
<div class="popup" id="popup-detail-antrian">
    <div class="popup-header popup-header-yellow">
        <span id="popup-antrian-title">Detail Pesanan</span>
        <button class="popup-close popup-close-white" data-close>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
    </div>
    <div class="popup-body">

        {{-- Identitas --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;">
            <div>
                <div style="font-size:20px; font-weight:800; color:#1a1a1a; line-height:1.2;" id="popup-antrian-order-id">—</div>
                <div style="font-size:12px; color:#888; margin-top:4px;" id="popup-antrian-meta">—</div>
            </div>
            <span class="status-pill" id="popup-antrian-status-pill" style="font-size:11px; padding:4px 12px; margin-top:4px;">—</span>
        </div>

        {{-- Label detail pesanan --}}
        <div style="font-size:11px; font-weight:700; color:#aaa; letter-spacing:.06em; text-transform:uppercase; margin-bottom:8px;">
            Detail Pesanan
        </div>

        {{-- Daftar item lengkap --}}
        <div id="popup-antrian-items" style="display:flex; flex-direction:column; gap:0; border:1.5px solid #f0f0f0; border-radius:10px; overflow:hidden;"></div>

        {{-- Total --}}
        <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 0 4px; border-top:2px dashed #f0f0f0; margin-top:12px;">
            <span style="font-size:13px; font-weight:700; color:#555;">Total Tagihan</span>
            <span style="font-size:16px; font-weight:800; color:#efb100;" id="popup-antrian-total">Rp 0</span>
        </div>

        {{-- Actions --}}
        <div style="display:flex; flex-direction:column; gap:8px; margin-top:12px;">
            <button class="popup-btn popup-btn-green btn-selesaikan-order" id="popup-antrian-btn-selesai" data-order-id="">
                ✅ Tandai Sebagai Selesai
            </button>
            <button class="popup-btn popup-btn-danger btn-batal-order" id="popup-antrian-btn-batal"
                    data-order-id="" data-order-code="" data-table="">
                ❌ Batalkan Pesanan
            </button>
        </div>
    </div>
</div>

{{-- ============================================================
     POPUP DETAIL MENU — Satu popup per menu (dari DB)
============================================================ --}}
@foreach($categories as $category)
    @foreach($category->menus as $menu)
    <div class="popup popup-menu-detail" id="popup-detail-menu-{{ $menu->id }}">
        <button class="popup-close popup-close-float" data-close>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <div class="menu-detail-inner">
            <div class="menu-detail-img-wrap">
                <img src="{{ asset('image/' . $menu->image) }}" alt="{{ $menu->name }}" class="menu-detail-img">
            </div>
            <div class="menu-detail-info">
                <h2 class="menu-detail-name">{{ $menu->name }}</h2>
                <p class="menu-detail-price">Rp {{ number_format($menu->price, 0, ',', '.') }},-</p>
                <p class="menu-detail-desc" style="font-size:12px; color:#666; margin-bottom:12px;">{{ $menu->description }}</p>
                <div class="menu-detail-row">
                    <span class="menu-detail-label">Jumlah Porsi</span>
                    <div class="menu-detail-qty">
                        <button class="qty-btn minus pd-btn-min">-</button>
                        <span class="qty-val pd-qty-val">1</span>
                        <button class="qty-btn plus pd-btn-plus">+</button>
                    </div>
                </div>
                <div class="menu-detail-section">
                    <p class="menu-detail-label">Level Pedas</p>
                    <p class="menu-detail-wajib">Wajib memilih level</p>
                    <div class="level-list">
                        @for($i = 0; $i <= 5; $i++)
                        <label class="level-option">
                            <span>LEVEL {{ $i }}</span>
                            <input type="radio" name="level-pedas-{{ $menu->id }}" class="level-check" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}>
                        </label>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        <div class="menu-detail-catatan">
            <p class="menu-detail-label">Catatan Khusus</p>
            <input type="text" class="catatan-input" id="catatan-{{ $menu->id }}" placeholder="Contoh: Sayur banyakin, telornya di acak">
        </div>
        <div class="menu-detail-footer">
            <button class="popup-btn btn-add-from-detail"
                    data-id="{{ $menu->id }}"
                    data-name="{{ $menu->name }}"
                    data-price="{{ $menu->price }}"
                    data-image="{{ asset('image/' . $menu->image) }}">
                Tambahkan ke Keranjang
            </button>
        </div>
    </div>
    @endforeach
@endforeach

{{-- ============================================================
     POPUP PEMBAYARAN
============================================================ --}}
<div class="popup" id="popup-payment">
    <div class="popup-header">
        <span>Pilih Metode Pembayaran</span>
        <button class="popup-close" data-close>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
    </div>
    <div class="popup-body">
        <p class="popup-total-label">Total Tagihan:</p>
        <p class="popup-total-amount" id="popup-total-display">Rp 0</p>
        <button class="payment-option" id="btn-pay-qris">
            <div class="payment-option-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1" stroke="#eab308" stroke-width="1.8"/><rect x="14" y="3" width="7" height="7" rx="1" stroke="#eab308" stroke-width="1.8"/><rect x="3" y="14" width="7" height="7" rx="1" stroke="#eab308" stroke-width="1.8"/><rect x="5" y="5" width="3" height="3" fill="#eab308"/><rect x="16" y="5" width="3" height="3" fill="#eab308"/><rect x="5" y="16" width="3" height="3" fill="#eab308"/><path d="M14 14H17M17 14V17M17 17H20M20 17V20M14 17H15M15 20H20" stroke="#eab308" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <span class="payment-option-text">QRIS</span>
            <div class="payment-option-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </button>
        <button class="payment-option" id="btn-pay-tunai">
            <div class="payment-option-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><rect x="2" y="6" width="20" height="13" rx="2" stroke="#eab308" stroke-width="1.8"/><path d="M2 10H22" stroke="#eab308" stroke-width="1.8"/><circle cx="12" cy="15" r="2" stroke="#eab308" stroke-width="1.5"/></svg>
            </div>
            <span class="payment-option-text">CASH</span>
            <div class="payment-option-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </button>
    </div>
</div>

{{-- POPUP QRIS --}}
<div class="popup popup-confirm" id="popup-qris-confirm">
    <button class="popup-close popup-close-float" data-close>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
    </button>
    <div class="popup-body popup-body-confirm">
        <h2 class="confirm-title">Lanjutkan Pembayaran via QRIS?</h2>
        <div class="konfir-qris-image">
            <img src="{{ asset('image/img_konfirqris.png') }}" alt="QRIS konfir" style="height:40%;">
        </div>
        <p class="confirm-desc">Pastikan pesanan dan metode pembayaran sudah tepat.</p>
        <button class="popup-btn" id="btn-konfir-qris-lanjut">Lanjutkan Pembayaran</button>
    </div>
</div>

<div class="popup" id="popup-qris-code">
    <div class="popup-header popup-header-yellow">
        <span>Pembayaran QRIS</span>
        <button class="popup-close popup-close-white" data-close>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
    </div>
    <div class="popup-body">
        <p class="popup-total-label">Total Tagihan:</p>
        <p class="popup-total-amount" id="popup-qris-total-display">Rp 0</p>
        <div class="qris-logo">
            <img src="{{ asset('image/Logo_QRIS.png') }}" alt="QRIS Logo" style="height:40px;">
        </div>
        <div class="qr-code-box" id="qris-code-wrap">
            {{-- QR di-generate JS setelah total diketahui --}}
            <img src="" alt="QR Code" class="qr-img" id="qris-img">
        </div>
        <p class="qris-waiting">Menunggu pelanggan scan kode...</p>
        <button class="popup-btn" id="btn-qris-sukses">Cek Status / Konfirmasi Bayar</button>
    </div>
</div>

{{-- POPUP TUNAI --}}
<div class="popup" id="popup-tunai">
    <div class="popup-header popup-header-yellow">
        <span>Pembayaran Tunai</span>
        <button class="popup-close popup-close-white" data-close>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
    </div>
    <div class="popup-body">
        <p class="popup-total-label">Total Tagihan:</p>
        <p class="popup-total-amount" id="popup-tunai-total-display">Rp 0</p>
        <p class="tunai-label">Uang Diterima:</p>
        <input type="number" class="tunai-input" id="tunai-diterima" placeholder="Masukkan nominal...">
        <div class="tunai-presets">
            <button class="preset-btn" data-preset="10000">Rp10.000,-</button>
            <button class="preset-btn" data-preset="20000">Rp20.000,-</button>
            <button class="preset-btn" data-preset="50000">Rp50.000,-</button>
            <button class="preset-btn" data-preset="100000">Rp100.000,-</button>
        </div>
        <p class="tunai-label">Total kembali:</p>
        <div class="tunai-kembalian" id="tunai-kembalian-display">Rp 0</div>
        <button class="popup-btn" id="btn-bayar-tunai">Bayar</button>
    </div>
</div>

{{-- POPUP SUCCESS --}}
<div class="popup" id="popup-success">
    <button class="popup-close popup-close-float" data-close>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
    </button>
    <div class="popup-body">
        <h2 class="success-title">Pembayaran Berhasil!</h2>
        <div class="success-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div class="struk-divider"></div>
        <div class="struk-rows">
            <div class="struk-row"><span class="struk-label">ID Transaksi:</span><span class="struk-value" id="success-order-code">#-</span></div>
            <div class="struk-row"><span class="struk-label">Tipe:</span><span class="struk-value" id="success-order-type">-</span></div>
            <div class="struk-row"><span class="struk-label">Waktu Pemesanan:</span><span class="struk-value" id="success-waktu">-</span></div>
        </div>
        <div class="struk-divider"></div>
        <div class="struk-rows">
            <div class="struk-row"><span class="struk-label">Subtotal:</span><span class="struk-value" id="success-subtotal">-</span></div>
            <div class="struk-row"><span class="struk-label">Tax (10%):</span><span class="struk-value" id="success-tax">-</span></div>
            <div class="struk-row"><span class="struk-label">Total Tagihan:</span><span class="struk-value struk-bold" id="success-total">-</span></div>
            <div class="struk-row" id="success-tunai-row" style="display:none;"><span class="struk-label">Uang Tunai:</span><span class="struk-value struk-bold" id="success-tunai">-</span></div>
        </div>
        <div class="struk-divider"></div>
        <div class="struk-row struk-kembalian" id="success-kembalian-row" style="display:none;">
            <span class="struk-label">Uang Kembali:</span>
            <span class="struk-value struk-bold" id="success-kembalian">Rp 0</span>
        </div>
        <p class="struk-printing">Sedang Mencetak Struk....</p>
        <button class="popup-btn popup-btn-selesai" id="btn-selesai-transaksi">Selesai</button>
    </div>
</div>

{{-- POPUP HAPUS KERANJANG --}}
<div class="popup popup-warning" id="popup-hapus-keranjang">
    <button class="popup-close popup-close-float" data-close>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
    </button>
    <div class="popup-body popup-body-warning">
        <div class="warning-icon-wrap">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M12 9V13M12 17H12.01" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
        </div>
        <h3 class="warning-title">Hapus semua item di keranjang?</h3>
        <p class="warning-desc">Semua item yang telah dipilih akan dihapus dari keranjang.</p>
        <div class="warning-actions">
            <button class="btn-batal-warning" data-close>Batal</button>
            <button class="btn-confirm-warning" id="btn-confirm-hapus-keranjang">Ya, Hapus Semua</button>
        </div>
    </div>
</div>

{{-- POPUP BATALKAN ORDER (dari antrian) --}}
<div class="popup popup-warning" id="popup-batal-pesanan">
    <div class="popup-body popup-body-warning">
        <div class="warning-icon-wrap">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M12 9V13M12 17H12.01" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
        </div>
        <h3 class="warning-title" id="batal-pesanan-title">Batalkan pesanan ini?</h3>
        <p class="warning-desc">Tindakan ini tidak dapat dikembalikan.</p>
        <div class="warning-actions">
            <button class="btn-batal-warning" data-close>Tidak</button>
            <button class="btn-confirm-warning" id="btn-konfir-batal-order">Ya, Batalkan</button>
        </div>
    </div>
</div>

<div class="popup" id="popup-alasan-batal">
    <div class="popup-header popup-header-yellow">
        <span>Konfirmasi Batalkan Pesanan</span>
        <button class="popup-close popup-close-white" data-close>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
    </div>
    <div class="popup-body">
        <p class="alasan-intro" id="alasan-batal-intro">Pesanan akan dibatalkan sepenuhnya. Mohon pilih alasan:</p>
        <div class="alasan-list">
            <label class="alasan-option"><input type="radio" name="alasan" value="stok"><span class="alasan-text">Stok Habis / Bahan Kosong</span></label>
            <label class="alasan-option"><input type="radio" name="alasan" value="ganti" checked><span class="alasan-text">Pelanggan Minta Ganti Menu</span></label>
            <label class="alasan-option"><input type="radio" name="alasan" value="salah"><span class="alasan-text">Kesalahan Input</span></label>
            <label class="alasan-option"><input type="radio" name="alasan" value="lain"><span class="alasan-text">Lainnya</span></label>
            <input type="text" class="alasan-input" id="alasan-input-teks" placeholder="Ketikkan Alasan Di Sini">
        </div>
        <p class="alasan-note">Uang yang telah diterima akan dikembalikan manual.</p>
        <button class="popup-btn popup-btn-danger" id="btn-submit-batal-order">Batalkan Pesanan</button>
        <button class="btn-kembali" data-close>Kembali</button>
    </div>
</div>
@endsection

@section('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
  window.ROUTES = Object.assign(window.ROUTES || {}, {
    apiAntrian : '{{ url("/kasir/api/antrian") }}',
    csrfToken  : '{{ csrf_token() }}',
  });
</script>
<script src="{{ asset('js/pos.js') }}"></script>
@endsection