<header class="main-header">
    <div class="header-left">
        <div class="logo">
            <img src="{{ asset('image/logo_warkop.png') }}" alt="Tskuy Kasir Logo" class="logo-img">
        </div>

        @if(Request::is('kasir/pos', 'pelanggan/orders')) {{-- ini buat di pelanggan juga ada fitur search --}}
        <div class="search-container">
            <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="7" stroke="#fbbf24" stroke-width="2" />
                <path d="M16.5 16.5L21 21" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" />
            </svg>
            <input type="text" class="search-bar" placeholder="What do you want eat today..." />
        </div>
        @else
        <div class="page-title-wrap" style="margin-left: 16px;">
            <div class="page-title" style="font-weight: 700; font-size: 18px; color: var(--teks);">@yield('header_title')</div>
            <div class="page-sub" style="font-size: 12px; color: var(--muted);">@yield('header_subtitle')</div>
        </div>
        @endif
    </div>

    <div class="header-right">
        <button class="notif-wrapper" data-open="popup-notif-panel">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M6 10C6 6.686 8.686 4 12 4C15.314 4 18 6.686 18 10V14L20 16V17H4V16L6 14V10Z" stroke="#64748b" stroke-width="1.8" stroke-linejoin="round" />
                <path d="M10 17C10 18.105 10.895 19 12 19C13.105 19 14 18.105 14 17" stroke="#64748b" stroke-width="1.8" />
            </svg>
            <span class="notif-badge">2</span>
        </button>

        <button class="user-card" data-open="popup-profil">
            <div class="user-avatar">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Staff') }}&background=eab308&color=fff" alt="User">
            </div>
            <div class="user-info">
                <p class="user-name">{{ Auth::user()->name }}</p>
                <p class="user-role">Cashier</p>
            </div>
        </button>
    </div>
</header>