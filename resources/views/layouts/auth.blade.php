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
        <meta name="theme-color" content="#0a0a0b" media="(prefers-color-scheme: dark)">
        <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
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

            /* ── Dark Mode (default) ───────────────── */
            body {
                font-family: 'Figtree', sans-serif;
                background: linear-gradient(160deg, #0a0a0b 0%, #0d1117 40%, #0a0f1a 70%, #0a0a0b 100%);
                color: #f5f5f5;
            }

            .auth-orb {
                position: fixed;
                border-radius: 50%;
                filter: blur(80px);
                pointer-events: none;
                z-index: 0;
                opacity: 0.4;
            }

            .auth-orb-1 {
                width: 500px;
                height: 500px;
                background: radial-gradient(circle, rgba(0, 51, 127, 0.3) 0%, transparent 70%);
                top: -10%;
                right: -5%;
                animation: auth-float-1 20s ease-in-out infinite;
            }

            .auth-orb-2 {
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(59, 130, 246, 0.18) 0%, transparent 70%);
                bottom: -5%;
                left: -5%;
                animation: auth-float-2 25s ease-in-out infinite;
            }

            .auth-orb-3 {
                width: 300px;
                height: 300px;
                background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
                top: 40%;
                left: 30%;
                animation: auth-float-3 22s ease-in-out infinite;
            }

            @keyframes auth-float-1 {
                0%, 100% { transform: translate(0, 0) scale(1); }
                33% { transform: translate(-30px, 20px) scale(1.05); }
                66% { transform: translate(20px, -15px) scale(0.95); }
            }

            @keyframes auth-float-2 {
                0%, 100% { transform: translate(0, 0) scale(1); }
                33% { transform: translate(25px, -20px) scale(1.08); }
                66% { transform: translate(-15px, 25px) scale(0.92); }
            }

            @keyframes auth-float-3 {
                0%, 100% { transform: translate(0, 0) scale(1); }
                50% { transform: translate(-20px, -25px) scale(1.1); }
            }

            .auth-navbar {
                display: none;
                background: rgba(10, 10, 11, 0.8);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.06);
                padding: 1rem 2rem;
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
                color: #f5f5f5;
                text-decoration: none;
                letter-spacing: 1px;
            }

            .auth-navbar-brand:hover {
                opacity: 0.9;
            }

            .auth-main {
                flex: 1;
                display: flex;
                flex-direction: column;
                position: relative;
                z-index: 1;
                min-height: 0;
            }

            .auth-footer-bar {
                display: none;
                background: rgba(10, 10, 11, 0.8);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-top: 1px solid rgba(255, 255, 255, 0.06);
                padding: 1.5rem 2rem;
                text-align: center;
                font-size: 0.85rem;
                color: rgba(255, 255, 255, 0.6);
            }

            .auth-footer-bar a {
                color: rgba(255, 255, 255, 0.7);
                text-decoration: none;
                transition: color 0.2s ease;
                margin: 0 0.75rem;
            }

            .auth-footer-bar a:hover {
                color: #f5f5f5;
            }

            .auth-footer-divider {
                display: inline-block;
                color: rgba(255, 255, 255, 0.3);
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

            /* ── Light Mode ────────────────────────── */
            @media (prefers-color-scheme: light) {
                body {
                    background: #ffffff;
                    color: #0f172a;
                }

                .auth-orb {
                    opacity: 0.15;
                }

                .auth-orb-1 {
                    background: radial-gradient(circle, rgba(0, 51, 127, 0.2) 0%, transparent 70%);
                }

                .auth-orb-2 {
                    background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
                }

                .auth-orb-3 {
                    background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
                }

                .auth-navbar {
                    background: rgba(255, 255, 255, 0.85);
                    border-bottom: 1px solid rgba(0, 51, 127, 0.08);
                }

                .auth-navbar-brand {
                    color: #00337F;
                }

                .auth-footer-bar {
                    background: rgba(255, 255, 255, 0.85);
                    border-top: 1px solid rgba(0, 51, 127, 0.08);
                    color: #64748b;
                }

                .auth-footer-bar a {
                    color: #475569;
                }

                .auth-footer-bar a:hover {
                    color: #00337F;
                }

                .auth-footer-divider {
                    color: #cbd5e1;
                }
            }
        </style>

        @stack('styles')
    </head>
    <body>
        <!-- Floating Orbs -->
        <div class="auth-orb auth-orb-1"></div>
        <div class="auth-orb auth-orb-2"></div>
        <div class="auth-orb auth-orb-3"></div>

        <!-- Top Navbar (mobile) -->
        <div class="auth-navbar">
            <div class="auth-navbar-content">
                <a href="{{ url('/') }}" class="auth-navbar-brand">THW-TRAINER</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="auth-main">
            @yield('content')
        </div>

        <!-- Bottom Footer Bar (mobile) -->
        <div class="auth-footer-bar">
            <a href="{{ route('landing.datenschutz') }}">Datenschutz</a>
            <span class="auth-footer-divider">&middot;</span>
            <a href="{{ route('landing.impressum') }}">Impressum</a>
        </div>
    </body>
</html>
