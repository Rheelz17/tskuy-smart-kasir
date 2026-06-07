<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dapur — Tskuy Smart Kasir')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  {{-- kasir-global.css sebagai pondasi design system --}}
  <link rel="stylesheet" href="{{ asset('css/kasir-global.css') }}">
  {{-- koki.css untuk override & komponen spesifik dapur --}}
  <link rel="stylesheet" href="{{ asset('css/koki.css') }}">

  @yield('head')
</head>
<body>
  <div class="koki-wrap">

    {{-- ══════════════════ HEADER ══════════════════ --}}
    <header class="main-header">
      <div class="header-left">

        <div class="logo">
            <img src="{{ asset('image/logo_warkop.png') }}" alt="Tskuy Kasir Logo" class="logo-img">
        </div>

        <div class="koki-brand">
          <span class="koki-brand-title">Koki Display</span>
          <span class="koki-brand-sub">Dapur Tskuy</span>
        </div>

      </div>

      <div class="header-right">

        <span class="koki-clock" id="liveClock">--:--:--</span>

        {{-- Notifikasi --}}
        <button class="notif-wrapper" id="notifButton" type="button" aria-label="Notifikasi">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="notif-badge" id="notifBadge" style="display:none"></span>
        </button>

        {{-- User pill --}}
        <div class="user-card">
          <div class="koki-avatar">
            {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
          </div>
          <div>
            <span class="user-name">{{ Auth::user()->name ?? 'Koki' }}</span>
            <span class="user-role">Koki</span>
          </div>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="koki-logout-btn" title="Keluar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </form>

      </div>
    </header>
    {{-- ══════════════════ /HEADER ══════════════════ --}}

    {{-- Konten halaman --}}
    @yield('content')

  </div>

  {{-- Toast container --}}
  <div class="toast-wrap" id="toastWrap"></div>

  {{-- Overlay global --}}
  <div class="popup-overlay" id="globalOverlay"></div>

  @yield('scripts')
</body>
</html>