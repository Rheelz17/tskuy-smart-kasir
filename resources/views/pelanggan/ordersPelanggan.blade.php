<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Warkop Tskuy - Pesan Menu</title>
    
    <meta name="is-logged-in" content="{{ Auth::check() ? 'true' : 'false' }}">
    <meta name="table-number" content="{{ $tableNumber ?? 'N/A' }}">
    <meta name="login-url" content="{{ route('login') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style-dashboardKasir.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/pelanggan.css') }}" />
</head>

<body>
    <div class="app-container">
        <header class="main-header">
            <div class="header-left">
                <div class="logo">
                    <img src="{{ asset('image/logo_warkop.png') }}" alt="Tskuy Logo" class="logo-img">
                </div>
                <div class="search-container">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <circle cx="11" cy="11" r="7" stroke="#fbbf24" stroke-width="2" />
                        <path d="M16.5 16.5L21 21" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <input type="text" class="search-bar" placeholder="Cari menu favoritmu..." />
                </div>
            </div>

            <div class="header-right">
                <div class="lencana-meja" style="background: #FFF8E7; color: #894B00; border: 1.5px solid #EFB100; padding: 6px 15px; border-radius: 50px; font-weight: 700; font-size: 13px;">
                    Meja: {{ $tableNumber ?? 'N/A' }}
                </div>

                @auth
                <button class="user-card" data-open="popup-profil">
                    <div class="user-avatar">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=efb100&color=fff" alt="User">
                    </div>
                    <div class="user-info">
                        <p class="user-name">{{ Auth::user()->name }}</p>
                        <p class="user-role">Pelanggan</p>
                    </div>
                </button>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="nav-item logout" title="Keluar" style="width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #fee2e2; border: 1.5px solid #ef4444; color: #ef4444; cursor: pointer;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><polyline points="16 17 21 12 16 7" /><line x1="21" y1="12" x2="9" y2="12" /></svg>
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="order-btn" style="width: auto; padding: 8px 20px; text-decoration: none; font-size: 13px;">Masuk</a>
                @endauth
            </div>
        </header>

        <div class="main-layout">
            <main class="content-area">
                <section class="mood-section">
                    <p class="section-label">Menu berdasarkan mood:</p>
                    <p class="section-labelmini">Pilih menu berdasarkan suasana hatimu!</p>
                    <div class="kategori-mood">
                        <button class="mood active-mood" data-mood="all">🍽️ Semua</button>
                        @if(isset($moods))
                            @foreach($moods as $mood)
                            <button class="mood" data-mood="{{ strtolower($mood->name) }}">
                                <img src="{{ asset('image/' . $mood->icon) }}" alt="{{ $mood->name }}" class="icon">
                                {{ $mood->name }}
                            </button>
                            @endforeach
                        @endif
                    </div>
                </section>

                <section class="menu-choice">
                    <div class="kategori-menu">
                        <div class="kategori-menu-btn">
                            @if(isset($categories))
                                @foreach($categories as $category)
                                <button class="menu-tab {{ $loop->first ? 'active-tab' : '' }}" data-filter="{{ strtolower($category->name) }}">
                                    {{ $category->name }} <small>{{ $category->menus->count() }}</small>
                                </button>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="menu-grid">
                        @if(isset($categories))
                            @foreach($categories as $category)
                                @foreach($category->menus as $menu)
                                <div class="menu-card" data-id="{{ $menu->id }}" data-category="{{ strtolower($category->name) }}" data-mood="{{ implode(' ', $menu->moods->pluck('name')->map(fn($m) => strtolower($m))->toArray()) }}" data-open="popup-detail-{{ $menu->id }}">
                                    <div class="card-img">
                                        <img src="{{ asset('image/' . $menu->image) }}" alt="{{ $menu->name }}">
                                    </div>
                                    <div class="card-body">
                                        <p class="card-judul">{{ $menu->name }}</p>
                                        <p class="card-desc">{{ Str::limit($menu->description, 35) }}</p>
                                        <p class="card-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                        <div class="qty-card">
                                            @auth
                                            <span class="qty-tambah">Tambah</span>
                                            @else
                                            <span class="qty-tambah">Pesan</span>
                                            @endauth
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
                    <div class="cart-title">Pesanan Kamu</div>
                    <button class="clear-btn" id="btn-clear-cart">Hapus</button>
                </div>

                <div class="cart-content">
                    <div class="cart-empty" id="cart-empty-msg">
                        <p>Belum ada menu yang dipilih.</p>
                    </div>
                    <div class="order-details" id="cart-items-container"></div>
                </div>

                <div class="summary-section">
                    <div class="summary-card">
                        <div class="summary-row total">
                            <span class="label total-label">Total Bayar</span>
                            <span class="value total-value" id="cart-total-price">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="cart-footer">
                    <button class="order-btn" data-open="popup-payment">Order Now</button>
                </div>
            </aside>
        </div>

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
    </div>

    <div class="popup-overlay" id="popup-overlay"></div>

    <div class="popup" id="popup-login-warning">
        <div class="popup-body" style="text-align: center; padding: 40px 25px;">
            <div style="font-size: 60px; margin-bottom: 15px;">🔒</div>
            <h3 style="font-weight: 800; color: #111; font-size: 20px;">Login Dulu Yuk!</h3>
            <p style="font-size: 13px; color: #888; line-height: 1.6; margin-bottom: 25px;">
                Silakan masuk ke akunmu untuk memesan di <strong>Meja {{ $tableNumber ?? 'ini' }}</strong>.
            </p>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('login') }}" class="popup-btn" style="text-decoration: none; background: #EFB100; color: white; padding: 15px; border-radius: 12px; font-weight: 700;">Masuk / Daftar</a>
                <button data-close style="background: none; border: none; color: #999; font-weight: 600; cursor: pointer; padding: 10px;">Nanti Saja</button>
            </div>
        </div>
    </div>

    @if(isset($categories))
        @foreach($categories as $category)
            @foreach($category->menus as $menu)
            <div class="popup popup-menu-detail" id="popup-detail-{{ $menu->id }}">
                <button class="popup-close popup-close-float" data-close style="border:none; border-radius:50%; width:30px; height:30px; cursor:pointer;">&times;</button>
                <div class="menu-detail-inner">
                    <div class="menu-detail-img-wrap">
                        <img src="{{ asset('image/' . $menu->image) }}" class="menu-detail-img">
                    </div>
                    <div class="menu-detail-info">
                        <h2 class="menu-detail-name">{{ $menu->name }}</h2>
                        <p class="menu-detail-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        <div style="height: 1px; background: #eee; margin: 15px 0;"></div>
                        <p class="menu-detail-desc" style="font-size: 13px; color: #777; line-height: 1.6;">{{ $menu->description }}</p>
                    </div>
                </div>
                <div class="menu-detail-footer">
                    <button class="popup-btn btn-add-from-detail" data-id="{{ $menu->id }}" data-close>Tambah ke Keranjang</button>
                </div>
            </div>
            @endforeach
        @endforeach
    @endif

    <script src="{{ asset('js/popup.js') }}"></script>
    <script src="{{ asset('js/pelanggan.js') }}"></script>
</body>
</html>