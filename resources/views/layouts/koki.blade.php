<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dapur — Tskuy POS')</title>

  {{-- Google Font Poppins (sama dengan halaman kasir & pelanggan) --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

  {{-- CSS Dapur — plain CSS, tanpa Tailwind --}}
  <link rel="stylesheet" href="{{ asset('css/koki.css') }}">

  {{-- Slot untuk CSS tambahan per halaman (opsional) --}}
  @yield('head')
</head>
<body>

  {{-- Konten utama halaman --}}
  @yield('content')

  {{-- Toast container — global, dipakai JS untuk notifikasi --}}
  <div class="toast-wrap" id="toastWrap"></div>

  {{-- Script per halaman --}}
  @yield('scripts')

</body>
</html>