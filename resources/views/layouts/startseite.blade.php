<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title')@else THW-Trainer - THW Theorie kostenlos lernen 2026 @endif</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="@hasSection('description')@yield('description')@else THW Theorie kostenlos lernen: Alle aktuellen Prüfungsfragen für die THW Grundausbildung 2026. Prüfungssimulation, Spaced Repetition & Lernfortschritt. @endif">
        <meta name="keywords" content="THW Theorie, THW Prüfung, THW Grundausbildung, THW Theoriefragen, Technisches Hilfswerk Prüfungsvorbereitung, THW Trainer, THW lernen kostenlos, THW Prüfungssimulation, THW online, THW Theorieprüfung, THW Prüfungsfragen App, THW online lernen">
        <meta name="author" content="Niclas Reutter">
        @if(app()->environment('testing') || str_contains(request()->getHost(), 'test.') || config('app.environment_type') === 'testing')
            <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
            <meta name="googlebot" content="noindex, nofollow">
        @else
            <meta name="robots" content="@hasSection('robots')@yield('robots')@else index, follow @endif">
        @endif

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="THW-Trainer">
        <meta property="og:url" content="@hasSection('canonical')@yield('canonical')@else {{ url()->current() }} @endif">
        <meta property="og:title" content="@hasSection('title')@yield('title')@else THW-Trainer - THW Theorie kostenlos lernen 2026 @endif">
        <meta property="og:description" content="@hasSection('description')@yield('description')@else THW Theorie kostenlos lernen: Alle aktuellen Prüfungsfragen für die THW Grundausbildung 2026. @endif">
        <meta property="og:image" content="{{ asset('logo-thwtrainer.png') }}">
        <meta property="og:locale" content="de_DE">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="@hasSection('canonical')@yield('canonical')@else {{ url()->current() }} @endif">
        <meta property="twitter:title" content="@hasSection('title')@yield('title')@else THW-Trainer - THW Theorie kostenlos lernen 2026 @endif">
        <meta property="twitter:description" content="@hasSection('description')@yield('description')@else THW Theorie kostenlos lernen: Alle aktuellen Prüfungsfragen für die THW Grundausbildung 2026. @endif">
        <meta property="twitter:image" content="{{ asset('logo-thwtrainer.png') }}">

        <!-- Favicons -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">

        <!-- Canonical URL -->
        <link rel="canonical" href="@hasSection('canonical')@yield('canonical')@else {{ url()->current() }} @endif">

        <!-- Language -->
        <link rel="alternate" hreflang="de" href="@hasSection('canonical')@yield('canonical')@else {{ url()->current() }} @endif">
        <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

        <!-- Performance Meta Tags -->
        <meta name="theme-color" content="#00337F">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
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
            "description": "Kostenlose THW Theorieprüfung Vorbereitung online. Alle Prüfungsfragen für Grundausbildung, FüUF26 und weitere Lehrgänge.",
            "areaServed": {
                "@@type": "Country",
                "name": "Deutschland"
            }
        }
        </script>

        <!-- Schema.org WebSite Markup (Sitelinks Search Box) -->
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "WebSite",
            "name": "THW-Trainer",
            "alternateName": ["THW Trainer", "THW-Trainer.de"],
            "url": "{{ url('/') }}",
            "description": "Kostenlose Online-Lernplattform für die THW Theorieprüfung. Alle Prüfungsfragen der Grundausbildung 2026 als App.",
            "inLanguage": "de"
        }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased" style="background-color: #0a0a0b; color: #f5f5f5;">
        <main>
            @yield('content')
        </main>

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
