@extends('layouts.kasir')

@section('title', 'Checkout - Warkop Tskuy')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/style-dashboardKasir.css') }}" />
<link rel="stylesheet" href="{{ asset('css/pelanggan.css') }}" />
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}" />
@endsection

@section('content')
<main class="content-area checkout-page">

    {{-- ======================= DESKTOP LAYOUT ======================= --}}
    <div class="checkout-desktop-wrap">

        {{-- ===== KIRI: DETAIL ORDER ===== --}}
        <div class="checkout-left">

            {{-- HEADER KIRI --}}
            <div class="checkout-section-header">
                <a href="{{ route('kasir.orders') }}" class="checkout-back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
                </a>
                <div>
                    <h1 class="checkout-page-title">Pesanan Saat Ini</h1>
                    <p class="checkout-page-sub">Meja {{ $tableNumber ?? 'N/A' }} &bull; <span id="co-item-count">0</span> item</p>
                </div>
            </div>

            {{-- REKOMENDASI TAMBAHAN --}}
            <div class="checkout-section-box">
                <p class="checkout-section-label">Rekomendasi tambahan</p>
                <div class="checkout-rekomendasi-scroll">
                    @if(isset($categories))
                        @foreach($categories as $category)
                            @foreach($category->menus->take(6) as $menu)
                            <div class="checkout-reko-card" data-id="{{ $menu->id }}" data-open="popup-detail-{{ $menu->id }}">
                                <img src="{{ asset('image/' . $menu->image) }}" alt="{{ $menu->name }}" class="checkout-reko-img">
                                <div class="checkout-reko-info">
                                    <p class="checkout-reko-name">{{ $menu->name }}</p>
                                    <p class="checkout-reko-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                </div>
                                <button class="checkout-reko-add" data-id="{{ $menu->id }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                            @endforeach
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- RINGKASAN PESANAN (ITEMS) --}}
            <div class="checkout-section-box">
                <div class="checkout-ringkasan-header">
                    <p class="checkout-section-label">Ringkasan Pesanan (<span id="co-item-count-2">0</span>)</p>
                    <a href="{{ route('kasir.orders') }}" class="checkout-tambah-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                        Tambah
                    </a>
                </div>

                {{-- LIST ITEM DARI KERANJANG --}}
                <div class="checkout-item-list" id="co-item-list">
                    {{-- Rendered dynamically from JS cart data --}}
                    <div class="checkout-empty-state" id="co-empty-state">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none"><path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="#ddd" stroke-width="1.8" stroke-linejoin="round"/><path d="M3 6H21" stroke="#ddd" stroke-width="1.8"/></svg>
                        <p>Keranjang masih kosong.</p>
                        <a href="{{ route('kasir.orders') }}" class="checkout-tambah-btn" style="margin-top: 8px;">Tambah Menu</a>
                    </div>
                </div>
            </div>

            {{-- RINGKASAN PEMBAYARAN --}}
            <div class="checkout-section-box">
                <p class="checkout-section-label">Ringkasan Pesanan</p>
                <div class="checkout-summary-rows">
                    <div class="checkout-summary-row">
                        <span class="co-sum-label">Subtotal</span>
                        <span class="co-sum-val" id="co-subtotal">Rp 0,-</span>
                    </div>
                    <div class="checkout-summary-row">
                        <span class="co-sum-label">Tax (10%)</span>
                        <span class="co-sum-val" id="co-tax">Rp 0,-</span>
                    </div>
                    <div class="checkout-summary-divider"></div>
                    <div class="checkout-summary-row checkout-summary-total">
                        <span class="co-sum-label-total">Total Tagihan</span>
                        <span class="co-sum-val-total" id="co-total">Rp 0,-</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== KANAN: OPSI LAYANAN & BAYAR (Desktop only) ===== --}}
        <div class="checkout-right">
            <div class="checkout-section-box">
                <p class="checkout-section-label">Detail Pesanan</p>

                {{-- NAMA PEMESAN --}}
                <div class="co-field-group">
                    <label class="co-field-label">Nama Pemesan</label>
                    <input type="text" class="co-field-input" id="co-nama" value="{{ Auth::user()->name ?? '' }}" placeholder="Masukkan nama kamu...">
                </div>

                {{-- OPSI MAKAN --}}
                <div class="co-field-group">
                    <label class="co-field-label">Opsi Layanan</label>
                    <div class="co-option-grid">
                        <label class="co-option-card {{ ($tableNumber ?? false) ? 'selected' : '' }}">
                            <input type="radio" name="eating_option" value="dine in" {{ ($tableNumber ?? false) ? 'checked' : '' }}>
                            <div class="co-option-icon">🍽️</div>
                            <div class="co-option-text">
                                <p class="co-option-title">Dine In</p>
                                <p class="co-option-desc">Makan di tempat</p>
                            </div>
                            <div class="co-option-check">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 12l5 5L20 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                            </div>
                        </label>
                        <label class="co-option-card">
                            <input type="radio" name="eating_option" value="take away">
                            <div class="co-option-icon">🛍️</div>
                            <div class="co-option-text">
                                <p class="co-option-title">Take Away</p>
                                <p class="co-option-desc">Dibungkus bawa pulang</p>
                            </div>
                            <div class="co-option-check">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 12l5 5L20 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- NOMOR MEJA --}}
                <div class="co-field-group" id="co-meja-group">
                    <label class="co-field-label">Nomor Meja</label>
                    <input type="text" class="co-field-input" id="co-meja" value="{{ $tableNumber ?? '' }}" placeholder="Contoh: 4" readonly>
                    <p class="co-field-hint">Nomor meja otomatis terisi dari QR Code meja.</p>
                </div>

                {{-- CATATAN TAMBAHAN --}}
                <div class="co-field-group">
                    <label class="co-field-label">Catatan ke Dapur <span class="co-label-optional">Opsional</span></label>
                    <textarea class="co-field-input co-textarea" id="co-catatan" placeholder="Contoh: Jangan terlalu pedas, alergi seafood..."></textarea>
                </div>
            </div>

            {{-- METODE PEMBAYARAN --}}
            <div class="checkout-section-box">
                <p class="checkout-section-label">Metode Pemesanan</p>
                <div class="co-payment-options">
                    <button class="co-payment-btn selected" id="co-pay-openbill">
                        <span class="co-pay-icon">📝</span>
                        <div class="co-pay-text">
                            <p class="co-pay-title">Open Bill</p>
                            <p class="co-pay-desc">Kirim ke dapur, bayar nanti pas mau balik</p>
                        </div>
                        <div class="co-pay-badge">Rekomendasi</div>
                    </button>
                    <button class="co-payment-btn" id="co-pay-now">
                        <span class="co-pay-icon">💳</span>
                        <div class="co-pay-text">
                            <p class="co-pay-title">Bayar Langsung</p>
                            <p class="co-pay-desc">Bayar lunas via QRIS sekarang</p>
                        </div>
                    </button>
                </div>
            </div>

            {{-- TOMBOL BUAT PESANAN --}}
            <div class="checkout-action-wrap">
                <div class="checkout-total-mini">
                    <span class="co-total-mini-label">Total Tagihan</span>
                    <span class="co-total-mini-val" id="co-total-desktop">Rp 0</span>
                </div>
                <button class="co-btn-order" id="co-btn-submit" data-open="popup-payment">
                    Buat Pesanan
                </button>
            </div>
        </div>

    </div>

    {{-- ======================= MOBILE BOTTOM BAR ======================= --}}
    <div class="checkout-mobile-footer">
        <div class="co-mobile-total">
            <span class="co-mobile-total-label">Total Tagihan</span>
            <span class="co-mobile-total-val" id="co-total-mobile">Rp 0</span>
        </div>
        <button class="co-btn-order" id="co-btn-submit-mobile" data-open="popup-payment">
            Buat Pesanan
        </button>
    </div>

</main>
@endsection

@section('page_popups')

{{-- ==================== POPUP PILIH METODE PEMESANAN ==================== --}}
<div class="popup" id="popup-payment">
    <div class="popup-header popup-header-yellow">
        <span>Pilih Metode Pemesanan</span>
        <button class="popup-close popup-close-white" data-close>&times;</button>
    </div>
    <div class="popup-body" style="text-align: center; padding: 24px 20px;">
        <p style="font-size: 13px; color: #666; margin-bottom: 20px;">Silakan pilih bagaimana lu ingin memproses pesanan di <strong>Meja {{ $tableNumber ?? 'ini' }}</strong>:</p>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <button class="popup-btn" id="choice-open-bill" style="background: #222; color: #fff; padding: 15px; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; border: none; cursor: pointer;">
                <span style="font-size: 20px;">📝</span>
                <div style="text-align: left;">
                    <p style="font-size: 13px; font-weight: 700; margin: 0;">Buka Sesi Open Bill</p>
                    <p style="font-size: 11px; color: #aaa; margin: 0; font-weight: 400;">Kirim ke dapur dulu, tambahkan pesanan lagi nanti, bayar pas mau balik</p>
                </div>
            </button>
            <button class="popup-btn" id="choice-pay-now" style="background: #efb100; color: #fff; padding: 15px; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; border: none; cursor: pointer;">
                <span style="font-size: 20px;">💳</span>
                <div style="text-align: left;">
                    <p style="font-size: 13px; font-weight: 700; margin: 0;">Bayar Langsung Lunas</p>
                    <p style="font-size: 11px; color: #fff7d6; margin: 0; font-weight: 400;">Langsung bayar lunas seluruh item di keranjang via QRIS sekarang</p>
                </div>
            </button>
        </div>
    </div>
</div>

{{-- ==================== POPUP QRIS ==================== --}}
<div class="popup" id="popup-qris-payment">
    <div class="popup-header popup-header-yellow">
        <span>Bayar Instan QRIS</span>
        <button class="popup-close popup-close-white" data-close>&times;</button>
    </div>
    <div class="popup-body" style="text-align: center; padding: 20px;">
        <p style="font-size: 13px; color: #666; margin-bottom: 5px;">Total Transaksi:</p>
        <p class="popup-total-amount" id="popup-qris-total" style="font-size: 24px; font-weight: 800; color: #e07b2a; margin-bottom: 15px;">Rp 0</p>
        <div class="qr-code-box" style="display: inline-block; padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 10px; margin-bottom: 15px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TskuyInstantPay" alt="QR Code">
        </div>
        <p style="font-size: 11px; color: #ef4444; font-weight: 600; margin-bottom: 10px;">🔴 Menunggu verifikasi pembayaran otomatis...</p>
        <button class="popup-btn" id="btn-instant-paid" style="background: #22c55e; border: none; width: 100%; padding: 12px; border-radius: 8px; color: #fff; font-weight: 700; cursor: pointer;">Simulasi Bayar Sukses (Testing)</button>
    </div>
</div>

{{-- ==================== POPUP HAPUS ITEM KERANJANG ==================== --}}
<div class="popup popup-warning" id="popup-delete-confirm">
    <div class="popup-body popup-body-warning">
        <div class="warning-icon-wrap">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M12 9v4M12 17h.01" stroke="white" stroke-width="2.5" stroke-linecap="round"/></svg>
        </div>
        <p class="warning-title">Apakah kamu yakin ingin menghapus item dari keranjang?</p>
        <p class="warning-desc">Item akan dihapus</p>
        <div class="warning-actions">
            <button class="btn-batal-warning" data-close>Tidak</button>
            <button class="btn-confirm-warning" id="btn-confirm-delete">Ya, Hapus Pesanan</button>
        </div>
    </div>
</div>

{{-- ==================== POPUP LOGIN WARNING ==================== --}}
<div class="popup" id="popup-login-warning">
    <div class="popup-body" style="text-align: center; padding: 40px 25px;">
        <div style="font-size: 60px; margin-bottom: 15px;">🔒</div>
        <h3 style="font-weight: 800; color: #111; font-size: 20px;">Login Dulu Yuk!</h3>
        <p style="font-size: 13px; color: #888; margin-bottom: 25px;">Silakan masuk ke akunmu untuk memesan di Meja {{ $tableNumber ?? 'ini' }}.</p>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="{{ route('login') }}" class="popup-btn" style="text-decoration: none;">Masuk / Daftar</a>
            <button data-close class="popup-btn popup-btn-danger" style="background: none; border: 1px solid #ccc; color: #555;">Nanti Saja</button>
        </div>
    </div>
</div>

{{-- ==================== POPUP DETAIL MENU (untuk rekomendasi) ==================== --}}
@if(isset($categories))
    @foreach($categories as $category)
        @foreach($category->menus as $menu)
        <div class="popup popup-detail-card" id="popup-detail-{{ $menu->id }}">
            <div class="pd-content">
                <button class="pd-close" data-close>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
                <div class="pd-top">
                    <div class="pd-img-wrap">
                        <img src="{{ asset('image/' . $menu->image) }}" alt="{{ $menu->name }}">
                    </div>
                    <div class="pd-info-wrap">
                        <h2 class="pd-title">{{ $menu->name }}</h2>
                        <p class="pd-price">Rp {{ number_format($menu->price, 0, ',', '.') }},-</p>
                        <p class="pd-desc">{{ $menu->description }}</p>
                        <div class="pd-qty-section desktop-qty">
                            <span class="pd-qty-label">Jumlah Porsi</span>
                            <div class="pd-qty-control">
                                <button type="button" class="pd-btn-min">-</button>
                                <span class="pd-qty-val">1</span>
                                <button type="button" class="pd-btn-plus">+</button>
                            </div>
                        </div>
                        <div class="pd-options">
                            <div class="pd-opt-header">
                                <span class="pd-opt-title">Level Pedas <span style="color:#ef4444;">*</span></span>
                                <span class="pd-opt-subtitle">Wajib memilih level</span>
                            </div>
                            <div class="pd-radio-group">
                                @for($i = 0; $i <= 3; $i++)
                                <label class="pd-radio">
                                    <span>LEVEL {{ $i }}</span>
                                    <input type="radio" name="level_{{ $menu->id }}" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }}>
                                    <div class="radio-indicator"></div>
                                </label>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pd-bottom">
                    <div class="pd-note">
                        <div class="pd-note-header">
                            <span class="pd-note-title">Catatan Tambahan</span>
                            <span class="pd-note-subtitle">Opsional</span>
                        </div>
                        <input type="text" placeholder="Contoh: Jangan diaduk, telor acak" class="pd-note-input" id="note-{{ $menu->id }}">
                    </div>
                    <div class="pd-qty-section mobile-qty">
                        <span class="pd-qty-label">Jumlah Porsi</span>
                        <div class="pd-qty-control">
                            <button type="button" class="pd-btn-min">-</button>
                            <span class="pd-qty-val">1</span>
                            <button type="button" class="pd-btn-plus">+</button>
                        </div>
                    </div>
                    <button class="pd-btn-add popup-btn btn-add-from-detail-v2" data-id="{{ $menu->id }}">Tambahkan ke Keranjang</button>
                </div>
            </div>
        </div>
        @endforeach
    @endforeach
@endif

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // =================== LOAD CART ===================
    let cart = JSON.parse(localStorage.getItem('tskuy_cart') || '[]');
    let pendingDeleteId = null;

    function formatRupiah(num) {
        return 'Rp ' + num.toLocaleString('id-ID') + ',-';
    }

    function renderCheckoutItems() {
        const list = document.getElementById('co-item-list');
        const emptyState = document.getElementById('co-empty-state');
        const countEls = document.querySelectorAll('#co-item-count, #co-item-count-2');

        // Hapus item lama kecuali empty state
        list.querySelectorAll('.checkout-item-row').forEach(el => el.remove());

        countEls.forEach(el => el.textContent = cart.length);

        if (cart.length === 0) {
            emptyState.style.display = 'flex';
            updateTotals(0, 0, 0);
            return;
        }
        emptyState.style.display = 'none';

        let subtotal = 0;
        cart.forEach((item, idx) => {
            subtotal += item.price * item.qty;
            const row = document.createElement('div');
            row.className = 'checkout-item-row';
            row.dataset.idx = idx;
            row.innerHTML = `
                <img src="${item.image || '/image/default.jpg'}" class="co-item-img" alt="${item.name}">
                <div class="co-item-info">
                    <p class="co-item-name">${item.name}</p>
                    <p class="co-item-desc">${item.desc || ''}</p>
                    ${item.note ? `<span class="co-item-badge">📋 ${item.note}</span>` : ''}
                    <p class="co-item-price">Rp. ${(item.price).toLocaleString('id-ID')}</p>
                </div>
                <div class="co-item-actions">
                    <button class="co-edit-btn" data-idx="${idx}" title="Edit item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button class="co-delete-btn" data-idx="${idx}" title="Hapus item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>
                    </button>
                </div>
                <div class="co-item-qty-row">
                    <div class="co-qty-control">
                        <button class="co-qty-btn minus" data-idx="${idx}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                        </button>
                        <span class="co-qty-val">${item.qty}</span>
                        <button class="co-qty-btn plus" data-idx="${idx}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                </div>
            `;
            list.appendChild(row);
        });

        const tax = Math.round(subtotal * 0.1);
        const total = subtotal + tax;
        updateTotals(subtotal, tax, total);

        // Bind qty buttons
        list.querySelectorAll('.co-qty-btn.minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.idx);
                if (cart[idx].qty > 1) {
                    cart[idx].qty--;
                    saveCart();
                    renderCheckoutItems();
                } else {
                    showDeleteConfirm(idx);
                }
            });
        });
        list.querySelectorAll('.co-qty-btn.plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.idx);
                cart[idx].qty++;
                saveCart();
                renderCheckoutItems();
            });
        });

        // Bind delete buttons
        list.querySelectorAll('.co-delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                showDeleteConfirm(parseInt(this.dataset.idx));
            });
        });
    }

    function updateTotals(subtotal, tax, total) {
        const fmt = n => 'Rp ' + n.toLocaleString('id-ID') + ',-';
        const fmtShort = n => 'Rp ' + n.toLocaleString('id-ID');
        document.getElementById('co-subtotal').textContent = fmt(subtotal);
        document.getElementById('co-tax').textContent = fmt(tax);
        document.getElementById('co-total').textContent = fmt(total);
        ['co-total-desktop', 'co-total-mobile'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = fmtShort(total);
        });
        // Sync QRIS popup total
        const qrisTotal = document.getElementById('popup-qris-total');
        if (qrisTotal) qrisTotal.textContent = fmtShort(total);
    }

    function saveCart() {
        localStorage.setItem('tskuy_cart', JSON.stringify(cart));
    }

    // =================== DELETE CONFIRM ===================
    function showDeleteConfirm(idx) {
        pendingDeleteId = idx;
        openPopup('popup-delete-confirm');
    }

    document.getElementById('btn-confirm-delete').addEventListener('click', function() {
        if (pendingDeleteId !== null) {
            cart.splice(pendingDeleteId, 1);
            saveCart();
            pendingDeleteId = null;
            closeAllPopups();
            renderCheckoutItems();
        }
    });

    // =================== OPSI LAYANAN (dine in / takeaway) ===================
    document.querySelectorAll('.co-option-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.co-option-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const val = this.querySelector('input').value;
            document.getElementById('co-meja-group').style.display = val === 'dine in' ? 'block' : 'none';
        });
        card.querySelector('input').addEventListener('change', function() {
            document.querySelectorAll('.co-option-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
        });
    });

    // =================== METODE PEMBAYARAN ===================
    document.querySelectorAll('#co-pay-openbill, #co-pay-now').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#co-pay-openbill, #co-pay-now').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // =================== REKOMENDASI TAMBAH ===================
    document.querySelectorAll('.checkout-reko-add').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.dataset.id;
            // Buka popup detail menu
            openPopup('popup-detail-' + id);
        });
    });

    
    document.querySelectorAll('#co-btn-submit, #co-btn-submit-mobile').forEach(btn => {
        btn.addEventListener('click', function() {
            if (cart.length === 0) {
                alert('Keranjang masih kosong!');
                return;
            }

            const payload = {
                _token:         '{{ csrf_token() }}',
                items:          cart,
                eating_option:  document.querySelector('[name="eating_option"]:checked')?.value || 'dine in',
                payment_method: document.getElementById('co-pay-openbill')?.classList.contains('selected') 
                                ? 'open_bill' : 'pay_now',
                table_number:   document.getElementById('co-meja')?.value || '',
                catatan:        document.getElementById('co-catatan')?.value || '',
            };

            fetch('{{ route("kasir.checkout.submit") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem('tskuy_cart'); // Kosongkan cart
                    window.location.href = data.redirect;
                }
            })
            .catch(err => console.error(err));
        });
    });

    // =================== POPUP SYSTEM ===================
    function openPopup(id) {
        const overlay = document.getElementById('popup-overlay');
        const popup = document.getElementById(id);
        if (overlay) overlay.classList.add('is-open');
        if (popup) popup.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeAllPopups() {
        document.querySelectorAll('.popup.is-open').forEach(p => p.classList.remove('is-open'));
        const overlay = document.getElementById('popup-overlay');
        if (overlay) overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-open]').forEach(el => {
        el.addEventListener('click', function() {
            openPopup(this.dataset.open);
        });
    });
    document.querySelectorAll('[data-close]').forEach(el => {
        el.addEventListener('click', closeAllPopups);
    });
    document.addEventListener('click', function(e) {
        const overlay = document.getElementById('popup-overlay');
        if (overlay && e.target === overlay) closeAllPopups();
    });

    // =================== INITIAL RENDER ===================
    renderCheckoutItems();
});
</script>
@endsection