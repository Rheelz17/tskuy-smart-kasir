<div class="drawer-overlay" id="drawer-overlay"></div>

<nav class="drawer" id="drawer">
<div class="drawer-profile" data-open="popup-profil">
    <div class="drawer-avatar">
    <img src="https://i.pravatar.cc/150?img=12" alt="Admin" />
    </div>
    <div class="drawer-user-info">
    <p class="drawer-user-name">Kurniawan Dwi S.</p>
    <p class="drawer-user-role">Admin</p>
    </div>
</div>

<div class="drawer-nav">
    <a class="drawer-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
    <svg width="20" height="20" viewBox="0 0 43 43" fill="none">
        <path
        fill-rule="evenodd"
        clip-rule="evenodd"
        d="M17.059 18.061H12.181a2.72 2.72 0 00-2.721 2.676V30.866a2.72 2.72 0 002.721 2.675h4.878a2.72 2.72 0 002.721-2.675V20.737a2.72 2.72 0 00-2.721-2.676z"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        />
        <path
        fill-rule="evenodd"
        clip-rule="evenodd"
        d="M17.059 7.741H12.181A2.72 2.72 0 009.46 10.31v1.744a2.72 2.72 0 002.721 2.568h4.878a2.72 2.72 0 002.721-2.568V10.31a2.72 2.72 0 00-2.721-2.568z"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        />
        <path
        fill-rule="evenodd"
        clip-rule="evenodd"
        d="M25.941 23.221h4.876a2.72 2.72 0 002.723-2.676V10.418a2.72 2.72 0 00-2.723-2.677h-4.876a2.72 2.72 0 00-2.721 2.677v10.127a2.72 2.72 0 002.721 2.676z"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        />
        <path
        fill-rule="evenodd"
        clip-rule="evenodd"
        d="M25.941 33.541h4.876a2.72 2.72 0 002.723-2.568v-1.744a2.72 2.72 0 00-2.723-2.568h-4.876a2.72 2.72 0 00-2.721 2.568v1.744a2.72 2.72 0 002.721 2.568z"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        />
    </svg>
    Dashboard
    </a>
    <a class="drawer-nav-item {{ request()->routeIs('admin.penjualan') ? 'active' : '' }}" href="{{ route('admin.penjualan') }}">
    <svg width="20" height="20" viewBox="0 0 33 33" fill="none">
        <g clip-path="url(#d1)">
        <path
            d="M8.662 6.875H28.875L26.125 16.5H10.143M27.5 22H11L8.25 4.125H4.125M12.375 27.5a1.375 1.375 0 11-2.75 0 1.375 1.375 0 012.75 0zm15.125 0a1.375 1.375 0 11-2.75 0 1.375 1.375 0 012.75 0z"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        </g>
        <defs>
        <clipPath id="d1">
            <rect width="33" height="33" fill="white" />
        </clipPath>
        </defs>
    </svg>
    Detail Penjualan
    </a>
    <a class="drawer-nav-item {{ request()->routeIs('admin.karyawan*') ? 'active' : '' }}" href="{{ route('admin.karyawan') }}">
    <svg width="20" height="20" viewBox="0 0 33 33" fill="none">
        <path
        d="M24.75 9.845C22.591 9.776 21.079 8.222 21.079 6.298c0-1.967 1.581-3.548 3.547-3.548 1.967 0 3.548 1.595 3.548 3.547-.014 1.925-1.526 3.478-3.424 3.548zM23.334 19.855c1.884.316 3.96-.014 5.417-.99 1.939-1.292 1.939-3.41 0-4.702-1.471-.976-3.575-1.306-5.459-.976M8.209 9.845c.082-.014.178-.014.26 0C10.368 9.776 11.88 8.222 11.88 6.298c0-1.967-1.581-3.548-3.547-3.548C6.366 2.75 4.785 4.345 4.785 6.298c.014 1.925 1.526 3.478 3.424 3.547zM9.625 19.855c-1.884.316-3.96-.014-5.418-.99C2.269 17.573 2.269 15.455 4.207 14.163c1.472-.976 3.575-1.306 5.46-.976M16.5 20.116c-0.082-.013-.178-.013-.261 0-1.898-.068-3.411-1.622-3.411-3.547 0-1.966 1.581-3.548 3.547-3.548 1.967 0 3.548 1.595 3.548 3.548-.014 1.925-1.526 3.478-3.424 3.547zM12.499 24.448C10.56 25.74 10.56 27.858 12.499 29.15c2.2 1.471 5.802 1.471 8.002 0 1.939-1.292 1.939-3.41 0-4.703-2.186-1.457-5.802-1.457-8.002 0z"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        />
    </svg>
    Manajemen Karyawan
    </a>
</div>

<div class="drawer-divider"></div>

<div class="drawer-footer">
    <a class="drawer-nav-item" href="#">
    <svg width="20" height="20" viewBox="0 0 36 39" fill="none">
        <path
        d="M30.333 21.426l2.324 1.336a3.97 3.97 0 011.717 5.022l-.824 1.421a3.98 3.98 0 01-5.561 1.497l-2.314-1.33c-1.01.81-2.141 1.46-3.352 1.925V33.825A3.675 3.675 0 0118.797 37.5h-1.647a3.675 3.675 0 01-3.685-3.675V31.142c-1.21-.465-2.341-1.115-3.352-1.926l-2.19 1.261a3.98 3.98 0 01-5.524-1.496l-.678-1.17a3.97 3.97 0 011.073-5.4l2.195-1.261a11.55 11.55 0 010-2.85l-2.195-1.26a3.97 3.97 0 01-1.073-5.4l.678-1.17a3.98 3.98 0 015.524-1.496l2.19 1.26c1.01-.81 2.141-1.46 3.352-1.925V5.675A3.675 3.675 0 0117.15 2h1.647a3.675 3.675 0 013.685 3.675V7.86c1.21.465 2.341 1.115 3.352 1.925l2.314-1.33a3.98 3.98 0 015.561 1.497l.824 1.422a3.97 3.97 0 01-1.717 5.022l-2.324 1.336a11.55 11.55 0 010 2.694z"
        stroke="#6a7282"
        stroke-width="2.2"
        stroke-linecap="round"
        stroke-linejoin="round"
        />
    </svg>
    Pengaturan
    </a>
    <a class="drawer-nav-item logout" href="/auth/login.html">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
        <path
        d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        />
    </svg>
    Keluar
    </a>
</div>
</nav>