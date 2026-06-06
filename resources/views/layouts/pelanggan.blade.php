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
    
    <link rel="stylesheet" href="{{ asset('css/kasir-global.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admincopy.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/pelanggan.css') }}" />
    @yield('styles')
</head>
<body>
    <div class="app-container">
        
        @include('partials.mobile-header')
        
        @include('partials.header')
        
        @include('partials.drawer')

        <div class="main-layout">
            @include('partials.sidebar')

            @yield('content')
        </div>
    </div>

    @include('partials.popups')
    @yield('page_popups')

    <script src="{{ asset('js/pelanggan.js') }}"></script>
    @yield('scripts')
</body>
</html>