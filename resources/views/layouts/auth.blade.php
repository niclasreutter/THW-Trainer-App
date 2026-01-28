<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer @endif</title>
        
        <!-- SEO Meta Tags -->
        <meta name="description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. @endif">
        <meta name="robots" content="noindex, nofollow">
        
        <!-- Favicons -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        
        <!-- Performance Meta Tags -->
        <meta name="theme-color" content="#00337F">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            html, body {
                min-height: 100%;
                overflow-x: hidden;
                display: flex;
                flex-direction: column;
            }
            
            body {
                font-family: 'Figtree', sans-serif;
            }

            .auth-navbar {
                display: none;
                background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
                padding: 1rem 2rem;
                box-shadow: 0 2px 8px rgba(0, 51, 127, 0.15);
            }

            .auth-navbar-content {
                max-width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .auth-navbar-brand {
                font-size: 1.3rem;
                font-weight: 800;
                color: white;
                text-decoration: none;
                letter-spacing: 1px;
            }

            .auth-navbar-brand:hover {
                opacity: 0.9;
            }

            .auth-main {
                flex: 1;
            }

            .auth-footer-bar {
                display: none;
                background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
                padding: 1.5rem 2rem;
                text-align: center;
                font-size: 0.85rem;
                color: white;
            }

            .auth-footer-bar a {
                color: white;
                text-decoration: none;
                transition: opacity 0.2s ease;
                margin: 0 0.75rem;
            }

            .auth-footer-bar a:hover {
                opacity: 0.8;
            }

            .auth-footer-divider {
                display: inline-block;
                color: rgba(255,255,255,0.5);
                margin: 0 0.5rem;
            }

            @media (max-width: 768px) {
                .auth-navbar {
                    display: block;
                }
                
                .auth-footer-bar {
                    display: block;
                }
            }
        </style>
        
        @stack('styles')
    </head>
    <body>
        <!-- Top Navbar -->
        <div class="auth-navbar">
            <div class="auth-navbar-content">
                <a href="{{ url('/') }}" class="auth-navbar-brand">THW-TRAINER</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="auth-main">
            @yield('content')
        </div>

        <!-- Bottom Footer Bar -->
        <div class="auth-footer-bar">
            <a href="{{ route('landing.datenschutz') }}">Datenschutz</a>
            <span class="auth-footer-divider">•</span>
            <a href="{{ route('landing.impressum') }}">Impressum</a>
        </div>
    </body>
</html>
