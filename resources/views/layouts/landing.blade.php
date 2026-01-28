<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-mode">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer - Kostenlose THW Theorie Prüfungsvorbereitung 2026 @endif</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="@hasSection('description')@yield('description')@else THW-Trainer: Kostenlose Prüfungsvorbereitung für die THW Grundausbildung. Alle Theoriefragen, Prüfungssimulation und Lernfortschritt. @endif">
        <meta name="keywords" content="THW, Technisches Hilfswerk, Theorie, Prüfung, Grundausbildung, Übung, Lernfortschritt, kostenlos, Simulation">
        <meta name="author" content="Niclas Reutter">
        @if(app()->environment('testing') || str_contains(request()->getHost(), 'test.') || config('app.environment_type') === 'testing')
            <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
            <meta name="googlebot" content="noindex, nofollow">
        @else
            <meta name="robots" content="index, follow">
        @endif

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer - Kostenlose THW Theorie Prüfungsvorbereitung @endif">
        <meta property="og:description" content="@hasSection('description')@yield('description')@else THW-Trainer: Kostenlose Prüfungsvorbereitung für die THW Grundausbildung. @endif">
        <meta property="og:image" content="{{ asset('logo-thwtrainer.png') }}">
        <meta property="og:locale" content="de_DE">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer - Kostenlose THW Theorie Prüfungsvorbereitung @endif">
        <meta property="twitter:description" content="@hasSection('description')@yield('description')@else THW-Trainer: Kostenlose Prüfungsvorbereitung für die THW Grundausbildung. @endif">
        <meta property="twitter:image" content="{{ asset('logo-thwtrainer.png') }}">

        <!-- Favicons -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Performance Meta Tags -->
        <meta name="theme-color" content="#00337F">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="format-detection" content="telephone=no">
        <meta name="msapplication-TileColor" content="#00337F">
        <meta name="application-name" content="THW-Trainer">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- PWA Manifest -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- Schema.org Organization Markup -->
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "Organization",
            "name": "THW-Trainer",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('logo-thwtrainer.png') }}",
            "description": "Kostenlose THW Theorie Prüfungsvorbereitung für Grundausbildung, FüUF26 und weitere Lehrgänge.",
            "areaServed": {
                "@@type": "Country",
                "name": "Deutschland"
            }
        }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Force Light Mode Styles -->
        <style>
            /* Landing Page ist immer Light Mode */
            :root {
                --bg-base: #f8fafc !important;
                --bg-elevated: #ffffff !important;
                --bg-surface: #ffffff !important;
                --text-primary: #1e293b !important;
                --text-secondary: #475569 !important;
                --text-muted: #64748b !important;
            }

            body {
                background-color: #f8fafc !important;
                color: #1e293b !important;
            }
        </style>

        @stack('styles')
    </head>
    <body class="font-sans antialiased light-mode bg-slate-50" x-data="{ mobileMenuOpen: false }">
        <!-- Navbar -->
        @include('landing.components.navbar')

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        @include('landing.components.footer')

        <!-- Cookie Banner -->
        @include('components.cookie-banner')

        <!-- Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(registration => console.log('SW registered'))
                        .catch(error => console.log('SW failed:', error));
                });
            }
        </script>

        @stack('scripts')
    </body>
</html>
