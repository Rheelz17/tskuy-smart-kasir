<header class="main-header">
    
    <div class="header-left" style="display: flex; align-items: center;">
        <div class="logo">
            <img src="{{ asset('image/logo_warkop.png') }}" alt="Tskuy Kasir Logo" class="logo-img" style="height: 40px; width: auto;">
        </div>

        @if(Request::is('kasir/pos', 'pelanggan/orders')) 
        <div class="search-container" style="width: 100%; margin-left: 15px; position: relative;">
            <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);">
                <circle cx="11" cy="11" r="7" stroke="#fbbf24" stroke-width="2" />
                <path d="M16.5 16.5L21 21" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" />
            </svg>
            <input type="text" class="search-bar" placeholder="What do you want eat today..." style="width: 100%; padding-left: 35px; height: 40px; border-radius: 8px; border: 1px solid #eee; outline: none;" />
        </div>
        @else
        <div class="page-title-wrap" style="width: 100%; margin-left: 15px;">
            <div class="page-title" style="font-weight: 700; font-size: 18px; color: var(--teks);">@yield('header_title')</div>
            <div class="page-sub" style="font-size: 12px; color: var(--muted);">@yield('header_subtitle')</div>
        </div>
        @endif
    </div>

    <div class="header-right" style="display: flex; align-items: center; justify-content: flex-end; gap: 15px;">
        <!-- Tombol Notifikasi -->
        <button class="notif-wrapper" data-open="popup-notif-panel" style="position: relative; background: #f8f8f8; border: 1px solid #eee; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M6 10C6 6.686 8.686 4 12 4C15.314 4 18 6.686 18 10V14L20 16V17H4V16L6 14V10Z" stroke="#64748b" stroke-width="1.8" stroke-linejoin="round" />
                <path d="M10 17C10 18.105 10.895 19 12 19C13.105 19 14 18.105 14 17" stroke="#64748b" stroke-width="1.8" />
            </svg>
            <span class="notif-badge" style="position: absolute; top: 0px; right: 0px; background: #efb100; color: #fff; font-size: 10px; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #fff;"></span>
        </button>

        <!-- Kotak Profil -->
        <button class="user-card" data-open="popup-profil" style="display: flex; align-items: center; gap: 10px; padding: 6px 12px; border: 1px solid #eee; border-radius: 50px; background: #fafafa; cursor: pointer;">
            <div class="user-avatar" style="width: 34px; height: 34px; border-radius: 50%; overflow: hidden;">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Pelanggan') }}&background=eab308&color=fff" alt="User Avatar" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div class="user-info" style="text-align: left;">
                <p class="user-name" style="font-size: 12px; font-weight: 700; color: #1a1a1a; margin: 0;">{{ Auth::user()->name ?? 'Pelanggan Warkop' }}</p>
                <p class="user-role" style="font-size: 10px; color: #999; margin: 0;">
                    @if(Auth::check())
                        {{ Auth::user()->role_id == 1 ? 'Admin' : (Auth::user()->role_id == 2 ? 'Kasir' : (Auth::user()->role_id == 3 ? 'Pelanggan' : 'Koki')) }}
                    @else
                        Guest
                    @endif
                </p>
            </div>
        </button>
    </div>
</header>