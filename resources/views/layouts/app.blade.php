<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') - THW-Trainer@else THW-Trainer - Dein digitaler Begleiter für THW Theorie @endif</title>
        
        <!-- SEO Meta Tags -->
        <meta name="description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. Kostenlose Theoriefragen, Prüfungssimulation und Lernfortschritt. Jetzt anonym oder mit Account üben! @endif">
        <meta name="keywords" content="THW, Technisches Hilfswerk, Theorie, Prüfung, Übung, Lernfortschritt, kostenlos, Simulation">
        <meta name="author" content="Niclas Reutter">
        <meta name="robots" content="index, follow">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer - Dein digitaler Begleiter für THW-Theorie @endif">
        <meta property="og:description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. Kostenlose Theoriefragen, Prüfungssimulation und Lernfortschritt. @endif">
        <meta property="og:image" content="{{ asset('logo-thwtrainer.png') }}">
        <meta property="og:locale" content="de_DE">
        
        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer - Dein digitaler Begleiter für THW-Theorie @endif">
        <meta property="twitter:description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. Kostenlose Theoriefragen, Prüfungssimulation und Lernfortschritt. @endif">
        <meta property="twitter:image" content="{{ asset('logo-thwtrainer.png') }}">
        
        <!-- Favicons -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#FDFDFC]">
    <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

                        <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
        
        <!-- Gamification Notifications -->
        @include('components.gamification-notifications')
        
        <!-- Achievement Popup -->
        @include('components.achievement-popup')
        
        <!-- Cookie Banner -->
        @include('components.cookie-banner')
        
        <footer class="bg-white border-t border-gray-200 py-6 mt-8">
            <div class="max-w-7xl mx-auto px-4">
                <!-- Unterstützung -->
                <div class="text-center mb-4">
                    <p class="text-gray-600 text-sm mb-2">
                        Diese Webseite wird kostenlos zur Verfügung gestellt
                    </p>
                    <a href="https://paypal.me/reuttern" 
                       target="_blank" 
                       rel="noopener"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #00337F; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; text-decoration: none; transition: background-color 0.2s;"
                       onmouseover="this.style.backgroundColor='#002a66'"
                       onmouseout="this.style.backgroundColor='#00337F'">
                        <span>☕</span>
                        <span>Unterstütze mich</span>
                    </a>
                </div>
                
                <!-- Links -->
                <div class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} THW-Trainer &ndash; 
                    <a href="{{ route('impressum') }}" class="text-blue-900 hover:underline">Impressum</a> &middot; 
                    <a href="{{ route('datenschutz') }}" class="text-blue-900 hover:underline">Datenschutz</a>
                </div>
                
                <!-- Creator -->
                <div class="text-center text-gray-400 text-xs mt-2">
                    Mit 💙 erstellt von <a href="https://niclas-reutter.de" target="_blank" rel="noopener" class="text-blue-600 hover:underline">niclas-reutter.de</a>
                </div>
            </div>
        </footer>
    </body>
</html>
