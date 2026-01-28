<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Theme initialization (prevents flash) -->
        <script>
            (function() {
                var theme = localStorage.getItem('theme');
                if (theme === 'light') {
                    document.documentElement.classList.add('light-mode');
                }
            })();
        </script>
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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex">
            <!-- Sidebar (Desktop) -->
            @auth
            <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 sidebar-glass">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-6 py-5 border-b border-glass-subtle">
                    <div class="w-10 h-10 rounded-xl bg-gradient-gold flex items-center justify-center">
                        <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW" class="w-7 h-7">
                    </div>
                    <span class="font-bold text-dark-primary tracking-tight">THW-Trainer</span>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    <a href="{{ route('dashboard') }}"
                       class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('practice.menu') }}"
                       class="sidebar-link {{ request()->routeIs('practice.*') ? 'active' : '' }}">
                        <i class="bi bi-book"></i>
                        Theorie Lernen
                    </a>

                    <a href="{{ route('bookmarks.index') }}"
                       class="sidebar-link {{ request()->routeIs('bookmarks.*') ? 'active' : '' }}">
                        <i class="bi bi-bookmark"></i>
                        Gespeicherte Fragen
                    </a>

                    <a href="{{ route('exam.index') }}"
                       class="sidebar-link {{ request()->routeIs('exam.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i>
                        Prüfung
                    </a>

                    <a href="{{ route('lehrgaenge.index') }}"
                       class="sidebar-link {{ request()->routeIs('lehrgaenge.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard"></i>
                        Lehrgänge
                    </a>

                    <a href="{{ route('statistics') }}"
                       class="sidebar-link {{ request()->routeIs('statistics') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i>
                        Statistiken
                    </a>

                    <a href="{{ route('gamification.leaderboard') }}"
                       class="sidebar-link {{ request()->routeIs('gamification.*') ? 'active' : '' }}">
                        <i class="bi bi-trophy"></i>
                        Rangliste
                    </a>

                    <div class="pt-4 mt-4 border-t border-glass-subtle">
                        <p class="px-3 mb-3 text-xs font-semibold text-dark-muted uppercase tracking-wider">Sonstiges</p>

                        <a href="{{ route('contact.index') }}"
                           class="sidebar-link {{ request()->routeIs('contact.*') ? 'active' : '' }}">
                            <i class="bi bi-envelope"></i>
                            Kontakt
                        </a>
                    </div>

                    @php
                        $userOV = auth()->user()->ortsverbände->first();
                    @endphp
                    @if($userOV)
                    <div class="pt-4 mt-4 border-t border-glass-subtle">
                        <p class="px-3 mb-3 text-xs font-semibold text-dark-muted uppercase tracking-wider">Ortsverband</p>

                        <a href="{{ route('ortsverband.index') }}"
                           class="sidebar-link {{ request()->routeIs('ortsverband.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i>
                            {{ $userOV->name }}
                        </a>
                    </div>
                    @endif

                    @if(auth()->user()->useroll === 'admin')
                    <div class="pt-4 mt-4 border-t border-glass-subtle">
                        <p class="px-3 mb-3 text-xs font-semibold text-dark-muted uppercase tracking-wider">Admin</p>

                        <a href="{{ route('admin.users.index') }}"
                           class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                            <i class="bi bi-gear"></i>
                            Verwaltung
                        </a>
                    </div>
                    @endif
                </nav>

                <!-- User Menu -->
                <div class="px-4 py-4 border-t border-glass-subtle">
                    <div class="flex items-center gap-3 px-3 py-2">
                        <div class="w-9 h-9 rounded-full bg-thw-blue/20 flex items-center justify-center">
                            <span class="text-sm font-semibold text-gold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-dark-primary truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-dark-muted truncate">Level {{ auth()->user()->level ?? 1 }}</p>
                        </div>
                        <button type="button" onclick="toggleTheme()" class="theme-toggle-sm" title="Farbschema">
                            <i class="bi bi-moon-fill icon-moon"></i>
                            <i class="bi bi-sun-fill icon-sun"></i>
                        </button>
                    </div>

                    <div class="flex items-center gap-2 mt-3 px-3">
                        <a href="{{ route('profile') }}" class="sidebar-link-sm flex-1">
                            <i class="bi bi-person"></i>
                            Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="sidebar-link-sm w-full text-left">
                                <i class="bi bi-box-arrow-right"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
            @endauth

            <!-- Main Content -->
            <div class="flex-1 @auth lg:pl-64 @endauth flex flex-col min-h-screen">
                <!-- Mobile Header -->
                <header class="lg:hidden sticky top-0 z-40 mobile-header-glass">
                    <div class="flex items-center justify-between px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-gradient-gold flex items-center justify-center">
                                <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW" class="w-5 h-5">
                            </div>
                            <span class="font-semibold text-dark-primary">THW-Trainer</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="toggleTheme()" class="theme-toggle-sm" title="Farbschema">
                                <i class="bi bi-moon-fill icon-moon"></i>
                                <i class="bi bi-sun-fill icon-sun"></i>
                            </button>
                            @auth
                            <button @click="sidebarOpen = true" class="p-2 text-dark-muted hover:text-dark-primary">
                                <i class="bi bi-list text-xl"></i>
                            </button>
                            @else
                            <a href="{{ route('login') }}" class="btn-ghost btn-sm">Anmelden</a>
                            @endauth
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 px-4 py-6 lg:px-8 lg:py-8 @auth pb-20 lg:pb-8 @endauth">
                    @yield('content')
                </main>

                <!-- Footer (Desktop only, within main content) -->
                <footer class="footer-glass mt-auto hidden lg:block">
                    <div class="max-w-7xl mx-auto px-4 py-6">
                        <div class="flex items-center justify-between text-sm text-dark-muted">
                            <div>
                                &copy; {{ date('Y') }} THW-Trainer &ndash;
                                <a href="{{ route('impressum') }}" class="text-gold hover:text-gold-light transition-colors">Impressum</a> &middot;
                                <a href="{{ route('datenschutz') }}" class="text-gold hover:text-gold-light transition-colors">Datenschutz</a>
                            </div>
                            <div>
                                <a href="https://paypal.me/reuttern" target="_blank" rel="noopener" class="text-gold hover:text-gold-light transition-colors">Unterstützen</a>
                            </div>
                        </div>
                    </div>
                </footer>

                <!-- Bottom Navigation (Mobile) -->
                @auth
                <nav class="lg:hidden fixed bottom-0 inset-x-0 bottom-nav-glass pb-safe z-40">
                    <div class="flex items-center justify-around py-2">
                        <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-house-door{{ request()->routeIs('dashboard') ? '-fill' : '' }}"></i>
                            <span>Home</span>
                        </a>

                        <a href="{{ route('practice.menu') }}" class="bottom-nav-item {{ request()->routeIs('practice.*') ? 'active' : '' }}">
                            <i class="bi bi-book{{ request()->routeIs('practice.*') ? '-fill' : '' }}"></i>
                            <span>Lernen</span>
                        </a>

                        <a href="{{ route('exam.index') }}" class="bottom-nav-item {{ request()->routeIs('exam.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard{{ request()->routeIs('exam.*') ? '-check-fill' : '' }}"></i>
                            <span>Prüfung</span>
                        </a>

                        <a href="{{ route('statistics') }}" class="bottom-nav-item {{ request()->routeIs('statistics') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart{{ request()->routeIs('statistics') ? '-fill' : '' }}"></i>
                            <span>Stats</span>
                        </a>

                        <a href="{{ route('profile') }}" class="bottom-nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                            <i class="bi bi-person{{ request()->routeIs('profile') ? '-fill' : '' }}"></i>
                            <span>Profil</span>
                        </a>
                    </div>
                </nav>
                @endauth
            </div>
        </div>

        <!-- Mobile Sidebar Overlay -->
        @auth
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm"
             @click="sidebarOpen = false"
             style="display: none;">
        </div>

        <!-- Mobile Sidebar -->
        <aside x-show="sidebarOpen"
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="translate-x-full"
               class="lg:hidden fixed inset-y-0 right-0 z-50 w-72 sidebar-glass"
               style="display: none;">
            <div class="flex items-center justify-between px-6 py-5 border-b border-glass-subtle">
                <span class="font-bold text-dark-primary">Menü</span>
                <button @click="sidebarOpen = false" class="p-2 text-dark-muted hover:text-dark-primary">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <nav class="px-4 py-6 space-y-1">
                <a href="{{ route('bookmarks.index') }}" class="sidebar-link {{ request()->routeIs('bookmarks.*') ? 'active' : '' }}">
                    <i class="bi bi-bookmark"></i>
                    Gespeicherte Fragen
                </a>

                <a href="{{ route('lehrgaenge.index') }}" class="sidebar-link {{ request()->routeIs('lehrgaenge.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i>
                    Lehrgänge
                </a>

                <a href="{{ route('gamification.leaderboard') }}" class="sidebar-link {{ request()->routeIs('gamification.*') ? 'active' : '' }}">
                    <i class="bi bi-trophy"></i>
                    Rangliste
                </a>

                @php
                    $userOV = auth()->user()->ortsverbände->first();
                @endphp
                @if($userOV)
                <a href="{{ route('ortsverband.index') }}" class="sidebar-link {{ request()->routeIs('ortsverband.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    Ortsverband
                </a>
                @endif

                <div class="pt-4 mt-4 border-t border-glass-subtle">
                    <p class="px-3 mb-3 text-xs font-semibold text-dark-muted uppercase tracking-wider">Sonstiges</p>
                    <a href="{{ route('contact.index') }}" class="sidebar-link {{ request()->routeIs('contact.*') ? 'active' : '' }}">
                        <i class="bi bi-envelope"></i>
                        Kontakt
                    </a>
                </div>

                @if(auth()->user()->useroll === 'admin')
                <div class="pt-4 mt-4 border-t border-glass-subtle">
                    <p class="px-3 mb-3 text-xs font-semibold text-dark-muted uppercase tracking-wider">Admin</p>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>
                        Verwaltung
                    </a>
                </div>
                @endif
            </nav>

            <div class="absolute bottom-0 inset-x-0 px-4 py-4 border-t border-glass-subtle">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-9 h-9 rounded-full bg-thw-blue/20 flex items-center justify-center">
                        <span class="text-sm font-semibold text-gold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-dark-primary truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-dark-muted">Level {{ auth()->user()->level ?? 1 }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="sidebar-link w-full">
                        <i class="bi bi-box-arrow-right"></i>
                        Abmelden
                    </button>
                </form>
            </div>
        </aside>
        @endauth

        <!-- Gamification Notifications -->
        @include('components.gamification-notifications')

        <!-- Achievement Popup -->
        @include('components.achievement-popup')

        <!-- Cookie Banner -->
        @include('components.cookie-banner')

        <!-- Theme Toggle Script -->
        <script>
            // Theme Management
            (function() {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'light') {
                    document.documentElement.classList.add('light-mode');
                    document.body.classList.add('light-mode');
                } else if (savedTheme === 'dark') {
                    document.documentElement.classList.remove('light-mode');
                    document.body.classList.remove('light-mode');
                }
            })();

            function toggleTheme() {
                const html = document.documentElement;
                const body = document.body;
                const isLightMode = html.classList.contains('light-mode');

                if (isLightMode) {
                    html.classList.remove('light-mode');
                    body.classList.remove('light-mode');
                    localStorage.setItem('theme', 'dark');
                } else {
                    html.classList.add('light-mode');
                    body.classList.add('light-mode');
                    localStorage.setItem('theme', 'light');
                }
            }
        </script>

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

        <!-- Page-specific scripts -->
        @stack('scripts')

    </body>
</html>
