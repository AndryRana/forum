<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.css" integrity="sha256-scOSmTNhvwKJmV7JQCuR7e6SQ3U9PcJ5rM/OMgL78X8=" crossorigin="anonymous" />
    <style>
        body { padding-bottom: 100px; }
        .level { display: flex; align-items: center; }
        .level-item {margin-right: 1em}
        .flex { flex: 1; }
        .mr-1 { margin-right: 1em; }
        [v-cloak] { display: none; }
        .ais-highlight > em { background: yellow; font-style: normal; }
        
    </style>

    <!-- Scripts-->
    <script>
        window.App = {!! json_encode([
            'csrfToken' => csrf_token(),
            'signedIn' => Auth::check(),
            'user' => Auth::user()
        ]) !!};
    </script>

    @yield('header')
</head>
<body style="padding-bottom: 100px">
    <div id="app">
        @include('layouts.nav')
        <main class="py-4">
            @yield('content')
        </main>
        <flash message="{{ session('flash') }}" ></flash>

    </div>
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('scripts')
</body>
</html>
