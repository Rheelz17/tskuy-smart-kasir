<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Sistem Kasir - Warkop Tskuy')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="{{ asset('css/kasir-global.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admincopy.css') }}" /> <!-- Pakai style admin biar konsisten -->
    @yield('styles')
</head>
<body>
    <div class="app-container">
        
        <!-- HEADER MOBILE (Muncul pas di HP aja) -->
        @include('partials.mobile-header')
        
        <!-- HEADER PC (Muncul pas di PC aja) -->
        @include('partials.header')
        
        <!-- DRAWER MOBILE (Tarik dari kanan) -->
        @include('partials.drawer')

        <div class="main-layout">
            <!-- SIDEBAR PC (Muncul di layar lebar) -->
            @include('partials.sidebar')

            <!-- AREA KONTEN UTAMA -->
            @yield('content')
        </div>
    </div>

    <!-- POPUPS -->
    @include('partials.popups')
    @yield('page_popups')

    <!-- JS GLOBAL -->
    <script src="{{ asset('js/kasir-core.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    @yield('scripts')
</body>
</html>