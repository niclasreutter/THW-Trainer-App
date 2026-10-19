<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Theme initialization (prevents flash) -->
        <script>
            (function() {
                var theme = localStorage.getItem('theme');
                var isLight = theme === 'light' || (!theme && window.matchMedia('(prefers-color-scheme: light)').matches);
                if (isLight) document.documentElement.classList.add('light-mode');
                document.documentElement.setAttribute('data-theme-mode', theme || 'auto');
            })();
        </script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer - Dein digitaler Begleiter für THW Theorie @endif</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. Kostenlose Theoriefragen, Prüfungssimulation und Lernfortschritt. Jetzt anonym oder mit Account üben! @endif">
        <meta name="keywords" content="THW, THW Trainer, THW Trainer App, THW Theorie, THW Prüfungsfragen, THW Grundausbildung, THW GA Prüfung, THW Training, Theorieprüfung, THW Prüfung, Technisches Hilfswerk, THW online, Prüfungssimulation, kostenlos">
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
        <meta property="og:image" content="{{ asset('logo-thw-trainer.png') . '?v=' . filemtime(public_path('logo-thw-trainer.png')) }}">
        <meta property="og:locale" content="de_DE">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer - Dein digitaler Begleiter für THW-Theorie @endif">
        <meta property="twitter:description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. Kostenlose Theoriefragen, Prüfungssimulation und Lernfortschritt. @endif">
        <meta property="twitter:image" content="{{ asset('logo-thw-trainer.png') . '?v=' . filemtime(public_path('logo-thw-trainer.png')) }}">

        <!-- Favicons -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}" media="(prefers-color-scheme: light)">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon-white.ico') }}?v={{ filemtime(public_path('favicon-white.ico')) }}" media="(prefers-color-scheme: dark)">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v={{ filemtime(public_path('apple-touch-icon.png')) }}">

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
        <link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- PWA Manifest -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- Schema.org Organization Markup -->
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "Organization",
            "name": "THW-Trainer",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('logo-thw-trainer.png') . '?v=' . filemtime(public_path('logo-thw-trainer.png')) }}",
            "description": "Kostenlose THW Theorie Prüfungsvorbereitung für Grundausbildung, FüUF26 und weitere Lehrgänge. Lernen im Ortsverband mit eigenen Fragen möglich.",
            "areaServed": {
                "@@type": "Country",
                "name": "Deutschland"
            }
        }
        </script>

        <!-- Schema.org Course Markup für Bildungsinhalte -->
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "Course",
            "name": "THW Grundausbildung Theorie",
            "description": "Kostenlose Online-Vorbereitung für die THW Grundausbildung Theorie-Prüfung mit Prüfungssimulation und Lernfortschritt-Tracking",
            "provider": {
                "@@type": "Organization",
                "name": "THW-Trainer",
                "url": "{{ url('/') }}"
            },
            "offers": {
                "@@type": "Offer",
                "price": "0",
                "priceCurrency": "EUR",
                "availability": "https://schema.org/InStock",
                "category": "Online"
            },
            "educationalLevel": "Beginner",
            "inLanguage": "de",
            "availableLanguage": "de",
            "hasCourseInstance": {
                "@@type": "CourseInstance",
                "courseMode": "online",
                "courseWorkload": "PT10H"
            }
        }
        </script>

        <!-- Alpine Components (before Vite) -->
        @stack('alpine-components')

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/learn.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                var s = document.getElementById('pwa-splash');
                if (s) { s.style.opacity = '0'; setTimeout(function() { s.remove(); }, 300); }
            });
        </script>

        @auth
        @php
            $unreadLeagueNotifs = \App\Models\Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->whereIn('type', ['league_promotion', 'league_relegation'])
                ->count();
            $unopenedLootboxCount = \App\Models\Lootbox::where('user_id', auth()->id())->where('opened', false)->count();
            $userOV = auth()->user()->ortsverbände->first();
            $sidebarNotificationCount = auth()->user()->unreadNotifications()->count();
            $sidebarLevelProgress = app(\App\Services\GamificationService::class)->getLevelProgress(auth()->user());
        @endphp
        {{-- Seitenleiste (Desktop) --}}
        <aside class="l-side" aria-label="Seitenleiste">
            <a href="{{ route('dashboard') }}" class="l-logo">
                <img src="{{ asset('logo-thw-trainer.png') . '?v=' . filemtime(public_path('logo-thw-trainer.png')) }}" alt="" class="logo-for-light">
                <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="" class="logo-for-dark">
                THW-Trainer
            </a>
            <nav class="l-nav" aria-label="Hauptnavigation" data-tour-step="sidebar">
                @include('layouts.partials.app-nav')
            </nav>
            @include('layouts.partials.app-user')
        </aside>
        @endauth

        <div class="l-shell @auth l-shell--auth @endauth flex flex-col min-h-screen">
            {{-- Kopfzeile (Handy) --}}
            <header class="l-mtop">
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="l-logo">
                    <img src="{{ asset('logo-thw-trainer.png') . '?v=' . filemtime(public_path('logo-thw-trainer.png')) }}" alt="" class="logo-for-light">
                    <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="" class="logo-for-dark">
                    THW-Trainer
                </a>
                <span class="l-mtop-r">
                    @auth
                        <a href="{{ route('profile') }}" aria-label="Profil öffnen"><img src="{{ auth()->user()->avatar_url }}" alt="" class="l-avatar"></a>
                        <button type="button" class="l-round" @click="sidebarOpen = true" :aria-expanded="sidebarOpen.toString()" aria-controls="l-menu" aria-label="Menü" data-tour-step="mobile-menu">
                            <i class="bi bi-list" aria-hidden="true"></i>
                        </button>
                    @else
                        <button type="button" onclick="toggleTheme()" class="l-round l-theme" title="Farbschema" aria-label="Farbschema wechseln">
                            <i class="bi bi-moon-fill icon-moon" aria-hidden="true"></i>
                            <i class="bi bi-sun-fill icon-sun" aria-hidden="true"></i>
                            <i class="bi bi-circle-half icon-auto" aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('login') }}" class="l-mtop-login">Anmelden</a>
                    @endauth
                </span>
            </header>

            <!-- Page Content -->
            <main class="flex-1 px-4 lg:px-8 py-6 lg:py-8 @auth pb-28 lg:pb-8 @endauth" id="main-content" style="min-width:0;overflow-x:hidden;">
                @yield('content')
            </main>

            {{-- Fußzeile (Desktop) --}}
            <footer class="l-footer hidden lg:block">
                <div class="l-footer-in">
                    <span>&copy; {{ date('Y') }} THW-Trainer <x-version-badge target="_blank" rel="noopener" /></span>
                    <nav aria-label="Rechtliches">
                        <a href="{{ route('landing.impressum') }}">Impressum</a>
                        <a href="{{ route('landing.datenschutz') }}">Datenschutz</a>
                        <a href="https://thw-trainer.de/statistik" target="_blank">Statistik</a>
                        <a href="{{ route('contact.index') }}">Kontakt</a>
                        <a href="https://bero-host.de/spenden/ks14llyclh8q" target="_blank" rel="noopener">Unterstützen</a>
                    </nav>
                </div>
            </footer>
        </div>

        @auth
        {{-- Tab-Leiste (Handy) --}}
        <nav class="l-tabs" aria-label="Navigation" data-tour-step="bottom-nav">
            <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                <i class="bi bi-house-door{{ request()->routeIs('dashboard') ? '-fill' : '' }}" aria-hidden="true"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('practice.menu') }}" @if(request()->routeIs('practice.*')) aria-current="page" @endif>
                <i class="bi bi-book{{ request()->routeIs('practice.*') ? '-fill' : '' }}" aria-hidden="true"></i>
                <span>Lernen</span>
            </a>
            <a href="{{ route('exam.index') }}" @if(request()->routeIs('exam.*')) aria-current="page" @endif>
                <i class="bi bi-clipboard{{ request()->routeIs('exam.*') ? '-check-fill' : '' }}" aria-hidden="true"></i>
                <span>Prüfung</span>
            </a>
            <a href="{{ route('profile') }}" @if(request()->routeIs('profile')) aria-current="page" @endif>
                <i class="bi bi-person{{ request()->routeIs('profile') ? '-fill' : '' }}" aria-hidden="true"></i>
                <span>Profil</span>
            </a>
        </nav>

        {{-- Menü (Handy): Panel von rechts, schließt per Tippen daneben oder Escape --}}
        <div x-show="sidebarOpen" class="l-menu-wrap lg:hidden" @click.self="sidebarOpen = false" style="display: none;">
            <nav class="l-menu" id="l-menu" aria-label="Menü">
                <div class="l-menu-head">
                    <b>Menü</b>
                    <button type="button" class="l-round" @click="sidebarOpen = false" aria-label="Menü schließen">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="l-nav">
                    @include('layouts.partials.app-nav')
                </div>
                @include('layouts.partials.app-user')
                <div class="l-menu-legal">
                    <a href="{{ route('landing.impressum') }}">Impressum</a>
                    <a href="{{ route('landing.datenschutz') }}">Datenschutz</a>
                    <a href="https://thw-trainer.de/statistik" target="_blank">Statistik</a>
                    <x-version-badge target="_blank" rel="noopener" />
                </div>
            </nav>
        </div>
        @endauth

        <!-- Gamification Notifications -->
        @include('components.gamification-notifications')

        <!-- Achievement Popup -->
        @include('components.achievement-popup')

        <!-- Milestone Celebrations -->
        @include('components.milestone-celebration')

        <!-- Cookie Banner -->
        @include('components.cookie-banner')

        <!-- Theme Toggle Script -->
        <script>
            // Theme Management (Dark / Light / Auto)
            (function() {
                var savedTheme = localStorage.getItem('theme');

                function applyTheme(light) {
                    document.documentElement.classList.toggle('light-mode', light);
                    document.body.classList.toggle('light-mode', light);
                }

                function setMode(mode) {
                    document.documentElement.setAttribute('data-theme-mode', mode);
                }

                if (savedTheme === 'light') {
                    applyTheme(true);
                    setMode('light');
                } else if (savedTheme === 'dark') {
                    applyTheme(false);
                    setMode('dark');
                } else {
                    applyTheme(window.matchMedia('(prefers-color-scheme: light)').matches);
                    setMode('auto');
                }

                // Live auf Systemänderungen reagieren (nur im Auto-Modus)
                window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', function(e) {
                    if (!localStorage.getItem('theme')) {
                        applyTheme(e.matches);
                    }
                });
            })();

            // Zyklus: dark → light → auto → dark
            function toggleTheme() {
                var current = localStorage.getItem('theme');
                var next, isLight;

                if (current === 'dark') {
                    next = 'light';
                    isLight = true;
                } else if (current === 'light') {
                    next = null; // auto
                    isLight = window.matchMedia('(prefers-color-scheme: light)').matches;
                } else {
                    next = 'dark';
                    isLight = false;
                }

                if (next) {
                    localStorage.setItem('theme', next);
                } else {
                    localStorage.removeItem('theme');
                }

                document.documentElement.classList.toggle('light-mode', isLight);
                document.body.classList.toggle('light-mode', isLight);
                document.documentElement.setAttribute('data-theme-mode', next || 'auto');
            }
        </script>

        <!-- Push Opt-In Modal -->
        <x-push-opt-in-modal />

        <!-- Service Worker + Push Registration -->
        <script>
            function pushOptIn() {
                return {
                    showModal: false,
                    registration: null,

                    async init() {
                        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

                        try {
                            this.registration = await navigator.serviceWorker.register('/sw.js');
                            console.log('SW registered');
                        } catch (e) {
                            console.log('SW failed:', e);
                            return;
                        }

                        // Check if already subscribed
                        const subscription = await this.registration.pushManager.getSubscription();
                        if (subscription) return;

                        // Check dismiss cooldown (7 days)
                        const dismissed = localStorage.getItem('push_dismissed_at');
                        if (dismissed) {
                            const daysSince = (Date.now() - parseInt(dismissed)) / (1000 * 60 * 60 * 24);
                            if (daysSince < 7) return;
                        }

                        // Show modal after short delay
                        setTimeout(() => { this.showModal = true; }, 2000);
                    },

                    dismiss() {
                        this.showModal = false;
                        localStorage.setItem('push_dismissed_at', Date.now().toString());
                    },

                    async subscribe() {
                        this.showModal = false;
                        try {
                            const publicKey = @json(config('services.webpush.public_key'));

                            // Convert VAPID key to Uint8Array
                            const urlBase64ToUint8Array = (base64String) => {
                                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                                const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                                const raw = atob(base64);
                                const arr = new Uint8Array(raw.length);
                                for (let i = 0; i < raw.length; ++i) arr[i] = raw.charCodeAt(i);
                                return arr;
                            };

                            const subscription = await this.registration.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: urlBase64ToUint8Array(publicKey)
                            });

                            const sub = subscription.toJSON();
                            await fetch('{{ route("push.subscribe") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    endpoint: sub.endpoint,
                                    keys: sub.keys,
                                    content_encoding: 'aes128gcm'
                                })
                            });

                            console.log('Push subscription saved');
                        } catch (e) {
                            console.error('Push subscription failed:', e);
                        }
                    }
                };
            }
        </script>

        <!-- Page-specific scripts -->
        @stack('scripts')

    </body>
</html>
