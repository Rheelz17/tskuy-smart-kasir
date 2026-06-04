@extends('layouts.pelanggan')

@section('title', 'Pilih Menu - Warkop Tskuy')

@section('styles')
<!-- Panggil CSS Kasir POS -->
<link rel="stylesheet" href="{{ asset('css/style-dashboardKasir.css') }}" />
<!-- Panggil CSS tambahan buat Pelanggan (Floating mobile cart dll) -->
<link rel="stylesheet" href="{{ asset('css/pelanggan.css') }}" />
@endsection

@section('content')
<main class="content-area">
    <!-- REKOMENDASI & MOOD -->
    <section class="mood-section">
        <p class="section-label">Rekomendasi & Performa Menu:</p>
        <p class="section-labelmini">Pilih menu berdasarkan mood atau suasana hatimu!</p>
        <div class="kategori-mood">
            <button class="mood active-mood" data-tag="semua">
                🍽️ Semua
            </button>
            @if(isset($moods))
                @foreach($moods as $mood)
                <button class="mood" data-tag="{{ strtolower($mood->name) }}">
                    @if($mood->icon)
                    <img src="{{ asset('image/' . $mood->icon) }}" alt="{{ $mood->name }}" class="icon">
                    @endif
                    {{ $mood->name }}
                </button>
                @endforeach
            @endif
        </div>
    </section>

    <!-- PILIHAN KATEGORI & GRID MENU -->
    <section class="menu-choice">
        <div class="kategori-menu">
            <p class="section-label">Pilihan Menu:</p>
            <div class="kategori-menu-btn">
                <button class="menu-tab active-tab" data-kategori="all">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Semua
                </button>
                @if(isset($categories))
                    @foreach($categories as $category)
                    <button class="menu-tab" data-kategori="{{ strtolower($category->name) }}">
                        {{ $category->name }} <small>{{ $category->menus->count() }} Jenis</small>
                    </button>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- GRID MENU KARTU -->
        <div class="menu-grid">
            @if(isset($categories))
                @foreach($categories as $category)
                    @foreach($category->menus as $menu)
                    <div class="menu-card" data-kategori="{{ strtolower($category->name) }}" data-tag="{{ implode(' ', $menu->moods->pluck('name')->map(fn($m) => strtolower($m))->toArray()) }}" data-id="{{ $menu->id }}" data-open="popup-detail-{{ $menu->id }}">
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

<!-- KERANJANG KANAN (CART SIDEBAR) -->
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
        <button class="clear-btn" id="btn-clear-cart" data-open="popup-hapus-keranjang">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M3 6H5H21" stroke="white" stroke-width="2" stroke-linecap="round" />
                <path d="M8 6V4H16V6M19 6V20C19 21.1 18.1 22 17 22H7C5.9 22 5 21.1 5 20V6H19Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    <div class="cart-content">
        <!-- Hilangkan Input Customer Section karena ini untuk Pelanggan -->
        <div class="order-details" id="cart-items-container">
            <div class="cart-empty" id="cart-empty-msg" style="display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 24px 0; color: #aaa; font-size: 13px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="#ddd" stroke-width="1.8" stroke-linejoin="round"/><path d="M3 6H21" stroke="#ddd" stroke-width="1.8"/></svg>
                <p>Keranjang masih kosong</p>
            </div>
            <!-- Item akan dirender oleh JS di sini -->
        </div>
    </div>

    <div class="summary-section">
        <p class="summary-label">Ringkasan Order</p>
        <div class="summary-card">
            <!-- <div class="summary-row"><span class="label">Subtotal</span><span class="summary-subtotal value">Rp0</span></div> -->
            <!-- <div class="summary-row"><span class="label">Tax (10%)</span><span class="summary-tax value">Rp0</span></div> -->
            <!-- <div class="garis"></div> -->
            <div class="summary-row total"><span class="label total-label">Total</span><span class="value total-value" id="cart-total-price">Rp 0</span></div>
        </div>
    </div>

    <div class="cart-footer">
        <button class="order-btn" data-open="popup-payment">Order Now</button>
    </div>
</aside>

<!-- Floating Bar Khusus HP -->
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
    <!-- Modals Login Warning -->
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

    <!-- Modals Detail Menu -->
    @if(isset($categories))
        @foreach($categories as $category)
            @foreach($category->menus as $menu)
            <div class="popup popup-menu-detail" id="popup-detail-{{ $menu->id }}">
                <button class="popup-close popup-close-float" data-close>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
                <div class="menu-detail-inner">
                    <div class="menu-detail-img-wrap">
                        <img src="{{ asset('image/' . $menu->image) }}" class="menu-detail-img">
                    </div>
                    <div class="menu-detail-info">
                        <h2 class="menu-detail-name">{{ $menu->name }}</h2>
                        <p class="menu-detail-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        <p class="menu-detail-desc">{{ $menu->description }}</p>
                        <div class="menu-detail-row">
                            <span class="menu-detail-label">Jumlah Porsi</span>
                            <div class="menu-detail-qty">
                                <button class="qty-btn minus">-</button>
                                <span class="qty-val">1</span>
                                <button class="qty-btn plus">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="menu-detail-footer">
                    <button class="popup-btn btn-add-from-detail" data-id="{{ $menu->id }}">Tambah ke Keranjang</button>
                </div>
            </div>
            @endforeach
        @endforeach
    @endif
@endsection