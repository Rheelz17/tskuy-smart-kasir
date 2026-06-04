<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Pesan Menu - Warkop Tskuy')</title>
    
    <meta name="is-logged-in" content="{{ Auth::check() ? 'true' : 'false' }}">
    <meta name="table-number" content="{{ $tableNumber ?? 'N/A' }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Panggil CSS Global Kasir biar layout sama persis -->
    <link rel="stylesheet" href="{{ asset('css/kasir-global.css') }}" />
    @yield('styles')
</head>
<body>
    <div class="app-container">
        
        <!-- Panggil Header Global -->
        @include('partials.header')

        <div class="main-layout">
            <!-- Panggil Sidebar Global -->
            @include('partials.sidebar')

            <!-- Area Konten Utama -->
            @yield('content')
        </div>
    </div>

    <!-- Panggil Popups Global & Khusus -->
    @include('partials.popups')
    @yield('page_popups')

    <!-- Panggil JS Khusus Pelanggan -->
    <script src="{{ asset('js/pelanggan.js') }}"></script>
    @yield('scripts')
</body>
</html>