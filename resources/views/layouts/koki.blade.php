<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  {{-- CSRF Token — wajib untuk semua AJAX POST request --}}
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>@yield('title', 'Dapur — Tskuy Smart Kasir')</title>

  {{-- Google Fonts: Poppins --}}
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

  {{-- CSS Utama Koki --}}
  <link rel="stylesheet" href="{{ asset('css/koki.css') }}" />

  @stack('styles')
</head>
<body>

{{-- ============================================================
     HEADER — sticky top, selalu terlihat
============================================================ --}}
<header class="koki-header">
  <div class="header-left">
    {{-- Logo --}}
    <div class="logo-circle">
      <img src="{{ asset('image/logo_tskuy.png') }}"
           alt="Tskuy Logo" />
    </div>

    <div>
      <div class="header-title"> Dapur Tskuy</div>
      <div class="header-subtitle">Display Sistem Koki</div>
    </div>
  </div>

  <div class="header-right">
    {{-- Jam digital live --}}
    <span class="live-clock" id="liveClock">--:--:--</span>

    {{-- Tombol Notifikasi --}}
    <button class="notif-button" id="notifButton" aria-label="Notifikasi">
      🔔
      <span class="notif-badge" id="notifBadge"></span>
    </button>

    {{-- Pill User yang login --}}
    <div class="user-pill">
      <div class="user-avatar">
        {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
      </div>
      <span>{{ Auth::user()->name ?? 'Koki' }}</span>
    </div>

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
      @csrf
      <button type="submit" class="back-btn" title="Keluar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
          <path d="M17 16L21 12M21 12L17 8M21 12H9M13 16v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Keluar
      </button>
    </form>
  </div>
</header>

{{-- ============================================================
     MAIN CONTENT
============================================================ --}}
<main class="koki-app">
  @yield('content')
</main>

{{-- ============================================================
     TOAST CONTAINER — posisi fixed kanan atas
============================================================ --}}
<div class="toast-container" id="toastContainer"></div>

{{-- ============================================================
     JS UTAMA
============================================================ --}}
<script src="{{ asset('js/koki.js') }}"></script>
@stack('scripts')

</body>
</html>