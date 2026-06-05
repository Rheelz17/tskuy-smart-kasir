<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dapur — Tskuy POS')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/kasir-global.css') }}">
  <link rel="stylesheet" href="{{ asset('css/koki.css') }}">

  @yield('head')
</head>
<body>

  <div class="app-container">
      @include('partials.header')

      <div class="main-layout">
          @include('partials.sidebar')

          @yield('content')
      </div>
  </div>

  <div class="toast-wrap" id="toastWrap"></div>

  @yield('scripts')
</body>
</html>