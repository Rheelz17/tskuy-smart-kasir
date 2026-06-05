<div class="drawer-overlay" id="drawer-overlay"></div>

<nav class="drawer" id="drawer">
    
    <div class="drawer-profile" @auth data-open="popup-profil" @endauth style="cursor: pointer;">
        <div class="drawer-avatar">
            @auth
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=efb100&color=fff" alt="User" />
            @else
            <img src="https://ui-avatars.com/api/?name=Guest&background=ccc&color=fff" alt="Guest" />
            @endauth
        </div>
        <div class="drawer-user-info">
            <p class="drawer-user-name">{{ Auth::check() ? Auth::user()->name : 'Guest Pelanggan' }}</p>
            <p class="drawer-user-role">
                @if(Auth::check())
                    @if(Auth::user()->role_id == 1) Admin
                    @elseif(Auth::user()->role_id == 2) Kasir
                    @elseif(Auth::user()->role_id == 4) Koki {{-- <-- PERBAIKAN 1: Teks label koki mobile --}}
                    @else Pelanggan
                    @endif
                @else
                    Belum Login
                @endif
            </p>
        </div>
    </div>

    <div class="drawer-nav">
        {{-- ==================== MENU KHUSUS ADMIN ==================== --}}
        @if(auth()->check() && auth()->user()->role_id == 1)
            <a class="drawer-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                Dashboard
            </a>
            <a class="drawer-nav-item {{ request()->routeIs('admin.penjualan') ? 'active' : '' }}" href="{{ route('admin.penjualan') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Detail Penjualan
            </a>
            <a class="drawer-nav-item {{ request()->routeIs('admin.menu') ? 'active' : '' }}" href="{{ route('admin.menu') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Manajemen Menu
            </a>
            <a class="drawer-nav-item {{ request()->routeIs('admin.karyawan') ? 'active' : '' }}" href="{{ route('admin.karyawan') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Manajemen Karyawan
            </a>

        {{-- ==================== MENU KHUSUS KASIR ==================== --}}
        @elseif(auth()->check() && auth()->user()->role_id == 2)
            <a class="drawer-nav-item {{ request()->routeIs('kasir.pos') ? 'active' : '' }}" href="{{ route('kasir.pos') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                Kasir POS
            </a>
            <a class="drawer-nav-item {{ request()->routeIs('kasir.manajemen-menu') ? 'active' : '' }}" href="{{ route('kasir.manajemen-menu') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Manajemen Menu
            </a>

        {{-- ==================== MENU KHUSUS KOKI / DAPUR ==================== --}}
        @elseif(auth()->check() && auth()->user()->role_id == 4) {{-- <-- PERBAIKAN 2: Navigasi rute koki mobile --}}
            <a class="drawer-nav-item {{ request()->routeIs('koki.index') ? 'active' : '' }}" href="{{ route('koki.index') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Antrian Dapur
            </a>
        {{-- ==================== MENU KHUSUS PELANGGAN ==================== --}}
        @else
            <a class="drawer-nav-item {{ request()->routeIs('pelanggan.orders') ? 'active' : '' }}" href="{{ route('pelanggan.orders') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Beranda
            </a>
            <a class="drawer-nav-item" href="#">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Riwayat Pesanan
            </a>
        @endif
    </div>

    <div class="drawer-divider"></div>

    <div class="drawer-footer" style="padding-top: 10px;">
        @auth
        <form method="POST" action="{{ route('logout') }}" style="width: 100%; margin: 0;">
            @csrf
            <button type="submit" class="drawer-nav-item logout" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; color: #ef4444; font-family: inherit; font-size: inherit;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Keluar Akun
            </button>
        </form>
        @else
        <a href="{{ route('login') }}" class="drawer-nav-item" style="color: #efb100;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
            Masuk Aplikasi
        </a>
        @endauth
    </div>
</nav>