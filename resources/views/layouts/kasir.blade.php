<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Sistem Kasir - Warkop Tskuy')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/kasir-global.css') }}" />
    @yield('styles')
</head>
<body>
    <div class="app-container">
        
        @include('partials.header')

        <div class="main-layout">
            
            @include('partials.sidebar')

            @yield('content')

        </div>
    </div>
    @include('partials.popups')
    @yield('page_popups')

    <script src="{{ asset('js/kasir-core.js') }}"></script>
    @yield('scripts')
</body>
</html>