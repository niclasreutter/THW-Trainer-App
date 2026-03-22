<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title')@else THW-Trainer - THW Theorie kostenlos lernen 2026 @endif</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="@hasSection('description')@yield('description')@else THW Theorie kostenlos lernen: Alle aktuellen Prüfungsfragen für die THW Grundausbildung 2026. Prüfungssimulation, Spaced Repetition & Lernfortschritt. @endif">
        <meta name="keywords" content="THW Theorie, THW Prüfung, THW Grundausbildung, THW Theoriefragen, Technisches Hilfswerk Prüfungsvorbereitung, THW Trainer, THW lernen kostenlos">
        <meta name="author" content="Niclas Reutter">
        @if(app()->environment('testing') || str_contains(request()->getHost(), 'test.') || config('app.environment_type') === 'testing')
            <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
            <meta name="googlebot" content="noindex, nofollow">
        @else
            <meta name="robots" content="@hasSection('robots')@yield('robots')@else index, follow @endif">
        @endif

        <!-- Open Graph -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="THW-Trainer">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@hasSection('title')@yield('title')@else THW-Trainer @endif">
        <meta property="og:description" content="@hasSection('description')@yield('description')@else THW Theorie kostenlos lernen @endif">
        <meta property="og:image" content="{{ asset('logo-thwtrainer.png') }}">
        <meta property="og:locale" content="de_DE">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:title" content="@hasSection('title')@yield('title')@else THW-Trainer @endif">
        <meta property="twitter:description" content="@hasSection('description')@yield('description')@else THW Theorie kostenlos lernen @endif">
        <meta property="twitter:image" content="{{ asset('logo-thwtrainer.png') }}">

        <!-- Favicons -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="alternate" hreflang="de" href="{{ url()->current() }}">
        <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

        <!-- Performance -->
        <meta name="theme-color" content="#0a0a0b">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
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
            "description": "Kostenlose THW Theorieprüfung Vorbereitung online. Alle Prüfungsfragen für Grundausbildung und weitere Lehrgänge.",
            "areaServed": {
                "@@type": "Country",
                "name": "Deutschland"
            }
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

        <!-- Service Worker -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }
        </script>

        @stack('scripts')
    </body>
</html>
