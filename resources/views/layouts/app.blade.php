<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema de Stock') }}</title>
    <!-- CSS -->
    @yield('css')

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link rel="shortcut icon" href="favicon.ico">
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body style="margin:0;
box-sizing: border-box;
display: flex;
flex-direction: column;
min-height: 100vh;">
    @include('layouts/newNavbar')

    <main class="py-4" style="flex: 1;">
        <div>
            <div class="menu-trigger"></div>
            <h1 class="text-center pb-4">{{ isset($tittle) ? $tittle : ' ' }}</h1>
            @yield('content')
        </div>

    </main>
    @include('layouts/footer')

    <script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript"></script>
    @yield('js')

</body>

</html>
