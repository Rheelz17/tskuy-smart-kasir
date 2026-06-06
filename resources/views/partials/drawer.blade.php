<div class="drawer-overlay" id="drawer-overlay"></div>

<nav class="drawer" id="drawer">
    <!-- INFO PROFIL -->
    <div class="drawer-profile" data-open="popup-profil">
        <div class="drawer-avatar">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Guest') }}&background=efb100&color=fff" alt="User" />
        </div>
        <div class="drawer-user-info">
            <p class="drawer-user-name">{{ Auth::user()->name ?? 'Guest' }}</p>
            <p class="drawer-user-role">
                @if(Auth::check())
                    {{ Auth::user()->role_id == 1 ? 'Admin' : (Auth::user()->role_id == 2 ? 'Kasir' : (Auth::user()->role_id == 4 ? 'Koki' : 'Pelanggan')) }}
                @else
                    Guest
                @endif
            </p>
        </div>
    </div>

    <!-- LIST MENU BERDASARKAN ROLE -->
    <div class="drawer-nav">
        
        {{-- ==================== 👑 MENU ADMIN ==================== --}}
        @if(auth()->check() && auth()->user()->role_id == 1)
            <a class="drawer-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <svg width="20" height="20" viewBox="0 0 43 43" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.059 18.061H12.181a2.72 2.72 0 00-2.721 2.676V30.866a2.72 2.72 0 002.721 2.675h4.878a2.72 2.72 0 002.721-2.675V20.737a2.72 2.72 0 00-2.721-2.676z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M17.059 7.741H12.181A2.72 2.72 0 009.46 10.31v1.744a2.72 2.72 0 002.721 2.568h4.878a2.72 2.72 0 002.721-2.568V10.31a2.72 2.72 0 00-2.721-2.568z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M25.941 23.221h4.876a2.72 2.72 0 002.723-2.676V10.418a2.72 2.72 0 00-2.723-2.677h-4.876a2.72 2.72 0 00-2.721 2.677v10.127a2.72 2.72 0 002.721 2.676z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M25.941 33.541h4.876a2.72 2.72 0 002.723-2.568v-1.744a2.72 2.72 0 00-2.723-2.568h-4.876a2.72 2.72 0 00-2.721 2.568v1.744a2.72 2.72 0 002.721 2.568z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Dashboard
            </a>
            <a class="drawer-nav-item {{ request()->routeIs('admin.penjualan') ? 'active' : '' }}" href="{{ route('admin.penjualan') }}">
                <svg width="20" height="20" viewBox="0 0 33 33" fill="none"><path d="M8.662 6.875H28.875L26.125 16.5H10.143M27.5 22H11L8.25 4.125H4.125M12.375 27.5a1.375 1.375 0 11-2.75 0 1.375 1.375 0 012.75 0zm15.125 0a1.375 1.375 0 11-2.75 0 1.375 1.375 0 012.75 0z" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Detail Penjualan
            </a>
            <a class="drawer-nav-item {{ request()->routeIs('admin.karyawan*') ? 'active' : '' }}" href="{{ route('admin.karyawan') }}">
                <svg width="20" height="20" viewBox="0 0 33 33" fill="none"><path d="M24.75 9.845C22.591 9.776 21.079 8.222 21.079 6.298c0-1.967 1.581-3.548 3.547-3.548 1.967 0 3.548 1.595 3.548 3.547-.014 1.925-1.526 3.478-3.424 3.548zM23.334 19.855c1.884.316 3.96-.014 5.417-.99 1.939-1.292 1.939-3.41 0-4.702-1.471-.976-3.575-1.306-5.459-.976M8.209 9.845c.082-.014.178-.014.26 0C10.368 9.776 11.88 8.222 11.88 6.298c0-1.967-1.581-3.548-3.547-3.548C6.366 2.75 4.785 4.345 4.785 6.298c.014 1.925 1.526 3.478 3.424 3.547zM9.625 19.855c-1.884.316-3.96-.014-5.418-.99C2.269 17.573 2.269 15.455 4.207 14.163c1.472-.976 3.575-1.306 5.46-.976M16.5 20.116c-0.082-.013-.178-.013-.261 0-1.898-.068-3.411-1.622-3.411-3.547 0-1.966 1.581-3.548 3.547-3.548 1.967 0 3.548 1.595 3.548 3.548-.014 1.925-1.526 3.478-3.424 3.547zM12.499 24.448C10.56 25.74 10.56 27.858 12.499 29.15c2.2 1.471 5.802 1.471 8.002 0 1.939-1.292 1.939-3.41 0-4.703-2.186-1.457-5.802-1.457-8.002 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Manajemen Karyawan
            </a>

        {{-- ==================== 🛒 MENU KASIR ==================== --}}
        @elseif(auth()->check() && auth()->user()->role_id == 2)
            <a class="drawer-nav-item {{ request()->routeIs('kasir.pos') ? 'active' : '' }}" href="{{ route('kasir.pos') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                Kasir POS
            </a>
            <a class="drawer-nav-item {{ request()->routeIs('kasir.manajemen-menu') ? 'active' : '' }}" href="{{ route('kasir.manajemen-menu') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Manajemen Menu
            </a>

        {{-- ==================== 🧑‍🍳 MENU KOKI ==================== --}}
        @elseif(auth()->check() && auth()->user()->role_id == 4)
            <a class="drawer-nav-item {{ request()->routeIs('koki.index') ? 'active' : '' }}" href="{{ route('koki.index') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Antrian Dapur
            </a>

        {{-- ==================== 🛵 MENU PELANGGAN ==================== --}}
        @else
            <a class="drawer-nav-item {{ request()->routeIs('pelanggan.orders') ? 'active' : '' }}" href="{{ route('pelanggan.orders') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Beranda Menu
            </a>
            <a class="drawer-nav-item" href="{{ route('pelanggan.riwayat') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Riwayat Pesanan
            </a>
            <button class="drawer-nav-item" data-open="popup-profil" style="width: 100%; border: none; background: none; text-align: left;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Lihat Profil
            </button>
        @endif
    </div>

    <div class="drawer-divider"></div>

    <div class="drawer-footer">
        @if(auth()->check() && auth()->user()->role_id == 1)
        <a class="drawer-nav-item" href="#">
            <svg width="20" height="20" viewBox="0 0 36 39" fill="none"><path d="M30.333 21.426l2.324 1.336a3.97 3.97 0 011.717 5.022l-.824 1.421a3.98 3.98 0 01-5.561 1.497l-2.314-1.33c-1.01.81-2.141 1.46-3.352 1.925V33.825A3.675 3.675 0 0118.797 37.5h-1.647a3.675 3.675 0 01-3.685-3.675V31.142c-1.21-.465-2.341-1.115-3.352-1.926l-2.19 1.261a3.98 3.98 0 01-5.524-1.496l-.678-1.17a3.97 3.97 0 011.073-5.4l2.195-1.261a11.55 11.55 0 010-2.85l-2.195-1.26a3.97 3.97 0 01-1.073-5.4l.678-1.17a3.98 3.98 0 015.524-1.496l2.19 1.26c1.01-.81 2.141-1.46 3.352-1.925V5.675A3.675 3.675 0 0117.15 2h1.647a3.675 3.675 0 013.685 3.675V7.86c1.21.465 2.341 1.115 3.352 1.925l2.314-1.33a3.98 3.98 0 015.561 1.497l.824 1.422a3.97 3.97 0 01-1.717 5.022l-2.324 1.336a11.55 11.55 0 010 2.694z" stroke="#6a7282" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Pengaturan
        </a>
        @endif

        @auth
        <form method="POST" action="{{ route('logout') }}" style="display: block; width: 100%;">
            @csrf
            <button type="submit" class="drawer-nav-item logout" style="width: 100%; border: none; background: none; text-align: left; cursor: pointer;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Keluar
            </button>
        </form>
        @else
        <a class="drawer-nav-item" href="{{ route('login') }}" style="color: #efb100;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
            Masuk Aplikasi
        </a>
        @endauth
    </div>
</nav>

<!-- 🔥 SCRIPT GLOBAL UNTUK MENGHIDUPKAN DRAWER DI SEMUA ROLE 🔥 -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hamburgerBtn = document.getElementById("hamburger-btn");
    const drawer = document.getElementById("drawer");
    const drawerOverlay = document.getElementById("drawer-overlay");

    function toggleDrawer() {
        const isOpen = drawer.classList.contains("is-open");
        if(isOpen) {
            drawer.classList.remove("is-open");
            drawerOverlay.classList.remove("is-open");
            if(hamburgerBtn) hamburgerBtn.classList.remove("is-open");
            document.body.style.overflow = "";
        } else {
            drawer.classList.add("is-open");
            drawerOverlay.classList.add("is-open");
            if(hamburgerBtn) hamburgerBtn.classList.add("is-open");
            document.body.style.overflow = "hidden";
        }
    }

    if(hamburgerBtn) hamburgerBtn.addEventListener("click", toggleDrawer);
    if(drawerOverlay) drawerOverlay.addEventListener("click", toggleDrawer);
    
    // Auto-tutup drawer kalau ada tombol/link di dalamnya yang di-klik
    const drawerLinks = drawer.querySelectorAll('.drawer-nav-item, .drawer-profile');
    drawerLinks.forEach(link => {
        link.addEventListener('click', () => {
            if(drawer.classList.contains("is-open")) toggleDrawer();
        });
    });
});
</script>