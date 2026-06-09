@extends('layouts.pelanggan')

@section('title', 'Pilih Menu - Warkop Tskuy')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/style-dashboardKasir.css') }}" />
<link rel="stylesheet" href="{{ asset('css/pelanggan.css') }}" />
@endsection

@section('content')
<main class="content-area">
    <section class="mood-section">
        <p class="section-label">Rekomendasi & Performa Menu:</p>
        <p class="section-labelmini">Pilih menu berdasarkan mood atau suasana hatimu!</p>
        <div class="kategori-mood">
            <button class="mood active-mood" data-mood="all">
                🍽️ Semua
            </button>
            @if(isset($moods))
                @foreach($moods as $mood)
                <button class="mood" data-mood="{{ strtolower($mood->name) }}">
                    @if($mood->icon)
                    <img src="{{ asset('image/' . $mood->icon) }}" alt="{{ $mood->name }}" class="icon">
                    @endif
                    {{ $mood->name }}
                </button>
                @endforeach
            @endif
        </div>
    </section>

    <section class="menu-choice">
        <div class="kategori-menu">
            <p class="section-label">Pilihan Menu:</p>
            <div class="kategori-menu-btn">
                <button class="menu-tab active-tab" data-filter="all">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Semua
                </button>
                @if(isset($categories))
                    @foreach($categories as $category)
                    <button class="menu-tab" data-filter="{{ strtolower($category->name) }}">
                        {{ $category->name }} <small>{{ $category->menus->count() }} Jenis</small>
                    </button>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="menu-grid">
            @if(isset($categories))
                @foreach($categories as $category)
                    @foreach($category->menus as $menu)
                    <div class="menu-card" data-category="{{ strtolower($category->name) }}" data-mood="{{ implode(' ', $menu->moods->pluck('name')->map(fn($m) => strtolower($m))->toArray()) }}" data-id="{{ $menu->id }}" data-open="popup-detail-{{ $menu->id }}">
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
                                <span class="qty-tambah">{{ Auth::check() ? 'Tambah' : 'Pesan' }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            @endif
        </div>
    </section>
</main>

<aside class="cart-sidebar">
    <div class="cart-header">
        <div class="cart-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="white" stroke-width="1.8" stroke-linejoin="round" />
                <path d="M3 6H21" stroke="white" stroke-width="1.8" />
                <path d="M16 10C16 12.2 14.2 14 12 14C9.8 14 8 12.2 8 10" stroke="white" stroke-width="1.8" stroke-linecap="round" />
            </svg>
            Pesanan Kamu
        </div>
        <button class="clear-btn" id="btn-clear-cart">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M3 6H5H21" stroke="white" stroke-width="2" stroke-linecap="round" />
                <path d="M8 6V4H16V6M19 6V20C19 21.1 18.1 22 17 22H7C5.9 22 5 21.1 5 20V6H19Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    <div class="cart-content">
        <div class="order-details" id="cart-items-container">
            <div class="cart-empty" id="cart-empty-msg" style="display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 24px 0; color: #aaa; font-size: 13px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="#ddd" stroke-width="1.8" stroke-linejoin="round"/><path d="M3 6H21" stroke="#ddd" stroke-width="1.8"/></svg>
                <p>Keranjang masih kosong</p>
            </div>
        </div>
    </div>

    <div class="summary-section">
        <p class="summary-label">Ringkasan Order</p>
        <div class="summary-card">
            <div class="summary-row total">
                <span class="label total-label">Total (+Tax 10%)</span>
                <span class="value total-value" id="cart-total-price">Rp 0</span>
            </div>
        </div>
    </div>

    <div class="cart-footer">
        <button class="order-btn" data-open="popup-payment">Order Now</button>
    </div>
</aside>

<div class="bar-keranjang-hp" id="floating-cart-bar">
    <div class="info-keranjang-hp">
        <span class="label-total-hp">Total Pesanan</span>
        <span class="jumlah-total-hp" id="mobile-total-price">Rp 0</span>
    </div>
    <span class="teks-checkout-hp">
        LIHAT KERANJANG (<span id="mobile-item-count">0</span>)
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6" /></svg>
    </span>
</div>
@endsection

@section('page_popups')
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
                                    <input type="number" 
                                            class="pd-qty-val" 
                                            value="1" 
                                            min="1" 
                                            max="{{ $menu->stock }}" 
                                            oninput="this.value = !!this.value && Math.abs(this.value) >= 1 ? this.value : 1">
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
                                <span class="pd-note-subtitle">Opsional (Maksimal 150 Karakter)</span>
                            </div>
                            <input type="text" placeholder="Contoh: Jangan diaduk, telor acak" class="pd-note-input" id="note-{{ $menu->id }}" maxlength="150">
                        </div>

                        <div class="pd-qty-section mobile-qty">
                            <span class="pd-qty-label">Jumlah Porsi</span>
                            <div class="pd-qty-control">
                                <button type="button" class="pd-btn-min">-</button>
                                <input type="number" 
                                        class="pd-qty-val" 
                                        value="1" 
                                        min="1" 
                                        max="{{ $menu->stock }}" 
                                        oninput="this.value = !!this.value && Math.abs(this.value) >= 1 ? this.value : 1">
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

@push('script')
<script>
    
</script>
@endpush