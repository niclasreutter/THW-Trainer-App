<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') - THW-Trainer@else THW-Trainer - Dein digitaler Begleiter für THW Theorie @endif</title>
        
        <!-- SEO Meta Tags -->
        <meta name="description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. Kostenlose Theoriefragen, Prüfungssimulation und Lernfortschritt. Jetzt anonym oder mit Account üben! @endif">
        <meta name="keywords" content="THW, Technisches Hilfswerk, Theorie, Prüfung, Übung, Lernfortschritt, kostenlos, Simulation">
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

        <!-- PWA Manifest -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- Schema.org EducationalOrganization Markup -->
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "EducationalOrganization",
            "name": "THW-Trainer",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('logo-thwtrainer.png') }}",
            "description": "Kostenlose THW Theorie Prüfungsvorbereitung für die Grundausbildung im Technischen Hilfswerk",
            "educationalCredentialAwarded": "THW Grundausbildung Theorie",
            "areaServed": "DE",
            "availableLanguage": "de",
            "offers": {
                "@@type": "Offer",
                "price": "0",
                "priceCurrency": "EUR",
                "description": "Kostenlose THW Theoriefragen und Prüfungssimulation"
            }
        }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Global Sticky Footer CSS -->
        <style>
            /* CACHE BUST v1.0 - GLOBAL STICKY FOOTER - 2025-10-21-17:15 */
            body {
                display: flex !important;
                flex-direction: column !important;
                min-height: 100vh !important;
            }
            
            main {
                flex: 1 0 auto !important;
            }
            
            footer {
                flex-shrink: 0 !important;
            }
        </style>
        
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-[#FDFDFC]">
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

        <!-- PWA Install Banner (nur Mobile) -->
        <div id="pwaInstallBanner" class="fixed bottom-0 left-0 right-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-2xl transform translate-y-full transition-transform duration-300 z-50 md:hidden">
            <div class="p-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-1">
                    <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW Trainer" class="w-12 h-12 rounded-lg shadow-lg">
                    <div class="flex-1">
                        <h3 class="font-bold text-sm">THW Trainer App</h3>
                        <p class="text-xs opacity-90">Als App installieren für schnelleren Zugriff</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button id="pwaInstallBtn" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-bold text-sm hover:bg-blue-50 transition-colors whitespace-nowrap">
                        Installieren
                    </button>
                    <button id="pwaCloseBanner" class="text-white hover:text-blue-200 px-2 text-lg">
                        ✕
                    </button>
                </div>
            </div>
        </div>

        <!-- Service Worker Registration & PWA Install Logic -->
        <script>
            // Service Worker Registration
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(registration => {
                            console.log('✅ ServiceWorker registered:', registration.scope);
                            
                            // Check for updates periodically
                            setInterval(() => {
                                registration.update();
                            }, 1000 * 60 * 60); // Check every hour
                        })
                        .catch(error => {
                            console.log('❌ ServiceWorker registration failed:', error);
                        });
                });
            }

            // PWA Install Banner Logic
            let deferredPrompt;
            const installBanner = document.getElementById('pwaInstallBanner');
            const installBtn = document.getElementById('pwaInstallBtn');
            const closeBanner = document.getElementById('pwaCloseBanner');

            // Check if already installed or dismissed
            const pwaInstalled = localStorage.getItem('pwa_installed') === 'true';
            const pwaDismissed = localStorage.getItem('pwa_banner_dismissed') === 'true';
            const dismissedTime = parseInt(localStorage.getItem('pwa_banner_dismissed_time') || '0');
            const daysSinceDismissal = (Date.now() - dismissedTime) / (1000 * 60 * 60 * 24);

            // Debug: Check PWA capabilities
            console.log('🔍 PWA Debug:', {
                userAgent: navigator.userAgent,
                standalone: window.matchMedia('(display-mode: standalone)').matches,
                isMobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
                hasBeforeInstallPrompt: 'onbeforeinstallprompt' in window,
                pwaInstalled: pwaInstalled,
                pwaDismissed: pwaDismissed
            });

            // Listen for the beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                console.log('✅ beforeinstallprompt event fired!');
                e.preventDefault();
                deferredPrompt = e;
                
                // Show banner if not installed and not recently dismissed (7 days)
                if (!pwaInstalled && (!pwaDismissed || daysSinceDismissal > 7)) {
                    console.log('📲 Showing install banner...');
                    setTimeout(() => {
                        if (installBanner) {
                            installBanner.classList.remove('translate-y-full');
                        }
                    }, 2000); // Show after 2 seconds
                } else {
                    console.log('⏸️ Banner not shown:', { pwaInstalled, pwaDismissed, daysSinceDismissal });
                }
            });

            // For iOS/Safari: Show manual install instructions
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            const isInStandaloneMode = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
            
            if (isIOS && !isInStandaloneMode && !pwaInstalled) {
                console.log('📱 iOS detected - showing manual install instructions');
                setTimeout(() => {
                    showIOSInstallInstructions();
                }, 3000);
            }

            // Install button click
            if (installBtn) {
                installBtn.addEventListener('click', async () => {
                    if (!deferredPrompt) return;
                    
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    
                    if (outcome === 'accepted') {
                        console.log('✅ PWA installed');
                        localStorage.setItem('pwa_installed', 'true');
                        if (installBanner) {
                            installBanner.classList.add('translate-y-full');
                        }
                    } else {
                        console.log('❌ PWA installation dismissed');
                    }
                    
                    deferredPrompt = null;
                });
            }

            // Close banner button
            if (closeBanner) {
                closeBanner.addEventListener('click', () => {
                    if (installBanner) {
                        installBanner.classList.add('translate-y-full');
                    }
                    localStorage.setItem('pwa_banner_dismissed', 'true');
                    localStorage.setItem('pwa_banner_dismissed_time', Date.now().toString());
                });
            }

            // Check if app is already installed
            window.addEventListener('appinstalled', () => {
                console.log('✅ PWA installed via event');
                localStorage.setItem('pwa_installed', 'true');
                if (installBanner) {
                    installBanner.classList.add('translate-y-full');
                }
            });

            // Hide banner when running as PWA
            if (window.matchMedia('(display-mode: standalone)').matches || 
                window.navigator.standalone === true) {
                localStorage.setItem('pwa_installed', 'true');
                if (installBanner) {
                    installBanner.remove();
                }
            }

            /**
             * Show iOS install instructions
             */
            function showIOSInstallInstructions() {
                const banner = document.createElement('div');
                banner.id = 'iosInstallBanner';
                banner.className = 'fixed bottom-0 left-0 right-0 bg-blue-600 text-white shadow-2xl z-50 p-4 md:hidden';
                banner.innerHTML = `
                    <div class="max-w-lg mx-auto">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW Trainer" class="w-12 h-12 rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-bold mb-1">THW Trainer als App installieren</h3>
                                <p class="text-sm opacity-90 mb-2">
                                    Tippe auf <span class="inline-flex items-center px-2 py-1 bg-white bg-opacity-20 rounded">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </span> (Safari Menü) und dann auf 
                                    <strong>"Zum Home-Bildschirm"</strong>
                                </p>
                            </div>
                            <button onclick="this.parentElement.parentElement.parentElement.remove(); localStorage.setItem('ios_install_dismissed', 'true')" 
                                    class="text-white hover:text-gray-200 text-xl">✕</button>
                        </div>
                    </div>
                `;
                
                // Don't show if already dismissed
                if (localStorage.getItem('ios_install_dismissed') === 'true') {
                    return;
                }
                
                document.body.appendChild(banner);
            }

        </script>
    </body>
</html>
