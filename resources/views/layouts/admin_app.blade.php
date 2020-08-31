<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Papimami Booking') }}</title>
    <link rel="icon" type="image/png" href="{{ env('APP_ENV') === 'production' ? secure_asset('images/favicon.ico') : asset('images/favicon.ico') }}"/>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/admin.css') }}" rel="stylesheet">
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ config('app.google_id') }}');</script>
    <!-- End Google Tag Manager -->
    @yield('css')
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('app.google_id') }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="app">
        <div class="container-fluid p-0">
            <nav class="sidebar">
                <div class="sidebar-sticky">
                    <p class="logo-brand">
                        <img src="{{ asset('images/logo.png')}}" alt="Papimami Logo" class="logo-img img-fluid mx-auto">
                    </p>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/bookings*') ? 'active' : null }}" href="{{ route('admin.booking-list') }}">{{ __('labels.booking_list') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/events/*') ? 'active' : null }}" href="{{ route('admin.event-list') }}">{{ __('labels.event_list') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/calendar*') ? 'active' : null }}" href="{{ route('admin.calendar-list') }}">{{ __('labels.calendar') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.logout') }}">ログアウト</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="main-container">
                @include('layouts.components.alert.session_message', ['field' => 'exception_message'])
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
