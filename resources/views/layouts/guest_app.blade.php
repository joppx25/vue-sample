<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script>
        @if (!empty(getReservationId()))
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
              'orderid': "{{ getReservationId() }}"
            });
        @endif
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Papimami Booking') }}</title>
    <link rel="icon" type="image/png" href="{{ env('APP_ENV') === 'production' ? secure_asset('images/favicon.ico') : asset('images/favicon.ico') }}"/>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/style.css') }}" rel="stylesheet">
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WGK3GDK');</script>
    <!-- End Google Tag Manager -->
    @yield('css')
    <script>
        window.onpageshow = function (event) {
            if (event.persisted) {
                window.location.reload()
            }
        };
    </script>

</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WGK3GDK"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <img src="{{ asset('images/logo.png')}}" alt="Papimami Logo" class="logo-img img-fluid mx-auto">
            </div>
        </nav>
        <div class="container-fluid">
            <main role="main" class="main-container">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
