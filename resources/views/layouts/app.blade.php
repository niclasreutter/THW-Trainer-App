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

        <title>@hasSection('title')@yield('title') - THW-Trainer@else THW-Trainer - Dein digitaler Begleiter für THW Theorie @endif</title>

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
        <meta property="og:image" content="{{ asset('logo-thwtrainer.png') . '?v=' . filemtime(public_path('logo-thwtrainer.png')) }}">
        <meta property="og:locale" content="de_DE">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="@hasSection('title')@yield('title') - THW-Trainer @else THW-Trainer - Dein digitaler Begleiter für THW-Theorie @endif">
        <meta property="twitter:description" content="@hasSection('description')@yield('description')@else THW-Trainer: Bereite dich optimal auf deine THW-Prüfung vor. Kostenlose Theoriefragen, Prüfungssimulation und Lernfortschritt. @endif">
        <meta property="twitter:image" content="{{ asset('logo-thwtrainer.png') . '?v=' . filemtime(public_path('logo-thwtrainer.png')) }}">

        <!-- Favicons -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
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

        <!-- PWA Manifest -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- Schema.org Organization Markup -->
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "Organization",
            "name": "THW-Trainer",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('logo-thwtrainer.png') . '?v=' . filemtime(public_path('logo-thwtrainer.png')) }}",
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
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex">
            <!-- Sidebar (Desktop) -->
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
            <aside class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 sidebar-glass" style="width: 264px;">
                <div class="sidebar-border"></div>

                <!-- Logo -->
                <div class="sidebar-header">
                    <div class="sidebar-logo-mark">
                        <img src="{{ asset('logo-thwtrainer.png') . '?v=' . filemtime(public_path('logo-thwtrainer.png')) }}" alt="THW" class="w-8 h-8 relative logo-for-light" style="z-index:1;">
                        <img src="{{ asset('logo-thwtrainer_w.png') . '?v=' . filemtime(public_path('logo-thwtrainer_w.png')) }}" alt="THW" class="w-8 h-8 relative logo-for-dark" style="z-index:1;">
                    </div>
                    <span class="sidebar-brand">THW-Trainer</span>
                </div>

                <!-- Navigation -->
                <nav class="sidebar-nav" data-tour-step="sidebar">
                    <div class="nav-section">Navigation</div>

                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                    <a href="{{ route('statistics') }}" class="sidebar-link {{ request()->routeIs('statistics') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i> Statistiken
                    </a>
                    <a href="{{ route('practice.menu') }}" class="sidebar-link {{ request()->routeIs('practice.*') ? 'active' : '' }}">
                        <i class="bi bi-book"></i> Theorie Lernen
                    </a>
                    <a href="{{ route('bookmarks.index') }}" class="sidebar-link {{ request()->routeIs('bookmarks.*') ? 'active' : '' }}">
                        <i class="bi bi-bookmark"></i> Gespeicherte Fragen
                    </a>
                    <a href="{{ route('exam.index') }}" class="sidebar-link {{ request()->routeIs('exam.index') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i> Prüfung
                    </a>
                    <a href="{{ route('exam.history') }}" class="sidebar-link {{ request()->routeIs('exam.history*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line"></i> Prüfungshistorie
                    </a>
                    <a href="{{ route('lehrgaenge.index') }}" class="sidebar-link {{ request()->routeIs('lehrgaenge.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard"></i> Lehrgänge
                    </a>
                    <a href="{{ route('contact.index') }}" class="sidebar-link {{ request()->routeIs('contact.*') ? 'active' : '' }}">
                        <i class="bi bi-envelope"></i> Kontakt
                    </a>

                    <div class="nav-section">Community</div>

                    <a href="{{ route('gamification.leaderboard') }}" class="sidebar-link {{ request()->routeIs('gamification.leaderboard') ? 'active' : '' }}">
                        <i class="bi bi-trophy"></i> Liga
                        @if($unreadLeagueNotifs > 0)
                            <span class="badge-error text-xs ml-auto">{{ $unreadLeagueNotifs }}</span>
                        @endif
                    </a>
                    <a href="{{ route('gamification.achievements') }}" class="sidebar-link {{ request()->routeIs('gamification.achievements') ? 'active' : '' }}">
                        <i class="bi bi-award"></i> Achievements
                    </a>
                    <a href="{{ route('lernsession.index') }}" class="sidebar-link {{ request()->routeIs('lernsession.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Lernsessions
                    </a>
                    <a href="{{ route('shop.index') }}" class="sidebar-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">
                        <i class="bi bi-shop"></i> Shop
                        @if($unopenedLootboxCount > 0)
                            <span class="badge-error text-xs ml-auto">{{ $unopenedLootboxCount }}</span>
                        @endif
                    </a>

                    <div class="nav-section">Ortsverband</div>

                    <a href="{{ $userOV ? route('ortsverband.show', $userOV) : route('ortsverband.index') }}" class="sidebar-link {{ request()->routeIs('ortsverband.show') ? 'active' : '' }}">
                        <i class="bi bi-building"></i> {{ $userOV ? $userOV->name : 'Ortsverband' }}
                    </a>
                    @if($userOV && $userOV->isAusbildungsbeauftragter(auth()->user()))
                        <a href="{{ route('ortsverband.dashboard', $userOV) }}" class="sidebar-link {{ request()->routeIs('ortsverband.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-gear"></i> Verwalten
                        </a>
                    @endif

                    @if(auth()->user()->useroll === 'admin')
                    <div class="nav-section nav-section-admin">Admin</div>

                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.statistics') }}" class="sidebar-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Statistiken
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Nutzer
                    </a>
                    <a href="{{ route('admin.questions.index') }}" class="sidebar-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                        <i class="bi bi-question-circle"></i> Fragen
                    </a>
                    <a href="{{ route('admin.lehrgaenge.index') }}" class="sidebar-link {{ request()->routeIs('admin.lehrgaenge.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard"></i> Lehrgänge
                    </a>
                    <a href="{{ route('admin.ortsverband.index') }}" class="sidebar-link {{ request()->routeIs('admin.ortsverband.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i> Ortsverbände
                    </a>
                    <a href="{{ route('admin.leagues.index') }}" class="sidebar-link {{ request()->routeIs('admin.leagues.*') ? 'active' : '' }}">
                        <i class="bi bi-trophy"></i> Ligen
                    </a>
                    <a href="{{ route('admin.shop-analytics') }}" class="sidebar-link {{ request()->routeIs('admin.shop-analytics') ? 'active' : '' }}">
                        <i class="bi bi-cart3"></i> Shop Analytics
                    </a>
                    <a href="{{ route('admin.newsletter.index') }}" class="sidebar-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
                        <i class="bi bi-megaphone"></i> Newsletter
                    </a>
                    <a href="{{ route('admin.issues.index') }}" class="sidebar-link {{ request()->routeIs('admin.issues.*') ? 'active' : '' }}">
                        <i class="bi bi-exclamation-triangle"></i> Fehlermeldungen
                        @php
                            $openIssuesCount = cache()->remember('admin_open_issues_count', 300, fn() => \App\Models\LehrgangQuestionIssue::where('status', 'open')->count() + \App\Models\QuestionIssue::where('status', 'open')->count());
                        @endphp
                        @if($openIssuesCount > 0)
                            <span class="badge-error text-xs ml-auto">{{ $openIssuesCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.contact-messages.index') }}" class="sidebar-link {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
                        <i class="bi bi-envelope"></i> Kontaktanfragen
                        @php
                            $unreadContactCount = cache()->remember('admin_unread_messages_count', 300, fn() => \App\Models\ContactMessage::where('is_read', false)->count());
                        @endphp
                        @if($unreadContactCount > 0)
                            <span class="badge-error text-xs ml-auto">{{ $unreadContactCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.exam-feedback.index') }}" class="sidebar-link {{ request()->routeIs('admin.exam-feedback.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard"></i> Prüfungs-Feedback
                    </a>
                    <a href="{{ route('admin.logs.index', 'scheduler') }}" class="sidebar-link {{ request()->routeIs('admin.logs.*') && request()->route('type') === 'scheduler' ? 'active' : '' }}">
                        <i class="bi bi-terminal"></i> Scheduler-Logs
                    </a>
                    <a href="{{ route('admin.logs.index', 'worker') }}" class="sidebar-link {{ request()->routeIs('admin.logs.*') && request()->route('type') === 'worker' ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Worker-Logs
                    </a>
                    @if(!app()->environment('production'))
                    <a href="{{ route('admin.time-simulator') }}" class="sidebar-link {{ request()->routeIs('admin.time-simulator*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Zeitsimulator
                    </a>
                    @endif
                    @endif
                </nav>

                <!-- User Card (bottom) -->
                <div class="sidebar-user">
                    <a href="{{ route('profile') }}" class="sidebar-user-card">
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="sidebar-user-avatar">
                        <div class="sidebar-user-meta">
                            <p class="sidebar-user-name">{{ auth()->user()->name }}</p>
                            <div class="sidebar-level-row">
                                <span class="sidebar-level-text">Lvl {{ auth()->user()->level ?? 1 }}</span>
                                <div class="sidebar-level-track"><div class="sidebar-level-fill" style="width: {{ $sidebarLevelProgress }}%;"></div></div>
                            </div>
                        </div>
                    </a>
                    <div class="sidebar-actions">
                        <a href="{{ route('notifications.index') }}" class="sidebar-action-btn {{ request()->routeIs('notifications.*') ? 'active' : '' }}" title="Mitteilungen">
                            <i class="bi bi-bell"></i>
                            @if($sidebarNotificationCount > 0)
                                <span class="sidebar-notif-dot"></span>
                            @endif
                        </a>
                        <button type="button" onclick="toggleTheme()" class="sidebar-action-btn theme-toggle" title="Farbschema">
                            <i class="bi bi-moon-fill icon-moon"></i>
                            <i class="bi bi-sun-fill icon-sun"></i>
                            <i class="bi bi-circle-half icon-auto"></i>
                        </button>
                        <a href="{{ route('profile') }}" class="sidebar-action-btn" title="Profil">
                            <i class="bi bi-person"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="sidebar-action-form">
                            @csrf
                            <button type="submit" class="sidebar-action-btn" title="Abmelden">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
            @endauth

            <!-- Main Content -->
            <div class="flex-1 @auth lg:pl-[264px] @endauth flex flex-col min-h-screen" style="min-width:0;">
                <!-- Mobile Header -->
                <header class="lg:hidden sticky top-0 z-40 mobile-header-glass">
                    <div class="flex items-center justify-between px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 flex items-center justify-center">
                                <img src="{{ asset('logo-thwtrainer.png') . '?v=' . filemtime(public_path('logo-thwtrainer.png')) }}" alt="THW" class="w-8 h-8 logo-for-light">
                                <img src="{{ asset('logo-thwtrainer_w.png') . '?v=' . filemtime(public_path('logo-thwtrainer_w.png')) }}" alt="THW" class="w-8 h-8 logo-for-dark">
                            </div>
                            <span class="font-semibold text-dark-primary">THW-Trainer</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="toggleTheme()" class="theme-toggle-sm" title="Farbschema">
                                <i class="bi bi-moon-fill icon-moon"></i>
                                <i class="bi bi-sun-fill icon-sun"></i>
                                <i class="bi bi-circle-half icon-auto"></i>
                            </button>
                            @auth
                            <button @click="sidebarOpen = true" class="p-2 text-dark-muted hover:text-dark-primary" data-tour-step="mobile-menu">
                                <i class="bi bi-list text-xl"></i>
                            </button>
                            @else
                            <a href="{{ route('login') }}" class="btn-ghost btn-sm">Anmelden</a>
                            @endauth
                        </div>
                    </div>
                </header>


                <!-- Page Content -->
                <main class="flex-1 px-4 lg:px-8 py-6 lg:py-8 @auth pb-28 lg:pb-8 @endauth" id="main-content" style="min-width:0;overflow-x:hidden;">
                    @include('components.push-notification-banner')
                    @yield('content')
                </main>

                <!-- Footer (Desktop only, within main content) -->
                <footer class="footer-glass mt-auto hidden lg:block">
                    <div class="max-w-7xl mx-auto px-4 py-6">
                        <div class="flex items-center justify-between text-sm text-dark-muted">
                            <div>
                                &copy; {{ date('Y') }} THW-Trainer &ndash;
                                <a href="{{ route('landing.impressum') }}" class="text-gold hover:text-gold-light transition-colors">Impressum</a> &middot;
                                <a href="{{ route('landing.datenschutz') }}" class="text-gold hover:text-gold-light transition-colors">Datenschutz</a> &middot;
                                <a href="https://thw-trainer.de/statistik" class="text-gold hover:text-gold-light transition-colors" target="_blank">Statistik</a> &middot;
                                <a href="{{ route('contact.index') }}" class="text-gold hover:text-gold-light transition-colors">Kontakt</a>
                            </div>
                            <div>
                                <a href="https://bero-host.de/spenden/ks14llyclh8q" target="_blank" rel="noopener" class="text-gold hover:text-gold-light transition-colors">Unterstützen</a>
                            </div>
                        </div>
                    </div>
                </footer>

            </div>
        </div>

        <!-- Bottom Navigation (Mobile) - Outside flex container for reliable backdrop-filter -->
        @auth
        <nav class="lg:hidden bottom-nav-glass bottom-nav-pill z-40 bottom-nav-blue" data-tour-step="bottom-nav">
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

                <a href="{{ route('profile') }}" class="bottom-nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <i class="bi bi-person{{ request()->routeIs('profile') ? '-fill' : '' }}"></i>
                    <span>Profil</span>
                </a>
            </div>
        </nav>
        @endauth

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
               class="lg:hidden fixed inset-y-0 right-0 z-50 sidebar-glass mobile-sidebar-panel flex flex-col"
               style="display: none;">
            <div class="sidebar-border sidebar-border-left"></div>

            <!-- Header -->
            <div class="mobile-sidebar-head">
                <span class="font-bold text-dark-primary">Menü</span>
                <button @click="sidebarOpen = false" class="mobile-sidebar-close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Scrollable Navigation -->
            <nav class="sidebar-nav">
                <a href="{{ route('statistics') }}" class="sidebar-link {{ request()->routeIs('statistics') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i> Statistiken
                </a>
                <a href="{{ route('bookmarks.index') }}" class="sidebar-link {{ request()->routeIs('bookmarks.*') ? 'active' : '' }}">
                    <i class="bi bi-bookmark"></i> Gespeicherte Fragen
                </a>
                <a href="{{ route('exam.history') }}" class="sidebar-link {{ request()->routeIs('exam.history*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i> Prüfungshistorie
                </a>
                <a href="{{ route('lehrgaenge.index') }}" class="sidebar-link {{ request()->routeIs('lehrgaenge.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i> Lehrgänge
                </a>
                <a href="{{ route('contact.index') }}" class="sidebar-link {{ request()->routeIs('contact.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope"></i> Kontakt
                </a>
                <a href="{{ route('gamification.leaderboard') }}" class="sidebar-link {{ request()->routeIs('gamification.leaderboard') ? 'active' : '' }}">
                    <i class="bi bi-trophy"></i> Liga
                    @if($unreadLeagueNotifs > 0)
                        <span class="badge-error text-xs ml-auto">{{ $unreadLeagueNotifs }}</span>
                    @endif
                </a>
                <a href="{{ route('gamification.achievements') }}" class="sidebar-link {{ request()->routeIs('gamification.achievements') ? 'active' : '' }}">
                    <i class="bi bi-award"></i> Achievements
                </a>
                <a href="{{ route('lernsession.index') }}" class="sidebar-link {{ request()->routeIs('lernsession.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Lernsessions
                </a>
                <a href="{{ route('shop.index') }}" class="sidebar-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Shop
                    @if($unopenedLootboxCount > 0)
                        <span class="badge-error text-xs ml-auto">{{ $unopenedLootboxCount }}</span>
                    @endif
                </a>
                <a href="{{ $userOV ? route('ortsverband.show', $userOV) : route('ortsverband.index') }}" class="sidebar-link {{ request()->routeIs('ortsverband.show') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> {{ $userOV ? $userOV->name : 'Ortsverband' }}
                </a>
                @if($userOV && $userOV->isAusbildungsbeauftragter(auth()->user()))
                    <a href="{{ route('ortsverband.dashboard', $userOV) }}" class="sidebar-link {{ request()->routeIs('ortsverband.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Verwalten
                    </a>
                @endif

                @if(auth()->user()->useroll === 'admin')
                <div class="nav-section nav-section-admin">Admin</div>

                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.statistics') }}" class="sidebar-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Statistiken
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Nutzer
                </a>
                <a href="{{ route('admin.questions.index') }}" class="sidebar-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                    <i class="bi bi-question-circle"></i> Fragen
                </a>
                <a href="{{ route('admin.lehrgaenge.index') }}" class="sidebar-link {{ request()->routeIs('admin.lehrgaenge.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i> Lehrgänge
                </a>
                <a href="{{ route('admin.ortsverband.index') }}" class="sidebar-link {{ request()->routeIs('admin.ortsverband.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Ortsverbände
                </a>
                <a href="{{ route('admin.leagues.index') }}" class="sidebar-link {{ request()->routeIs('admin.leagues.*') ? 'active' : '' }}">
                    <i class="bi bi-trophy"></i> Ligen
                </a>
                <a href="{{ route('admin.shop-analytics') }}" class="sidebar-link {{ request()->routeIs('admin.shop-analytics') ? 'active' : '' }}">
                    <i class="bi bi-cart3"></i> Shop Analytics
                </a>
                <a href="{{ route('admin.newsletter.index') }}" class="sidebar-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i> Newsletter
                </a>
                <a href="{{ route('admin.issues.index') }}" class="sidebar-link {{ request()->routeIs('admin.issues.*') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i> Fehlermeldungen
                    @if($openIssuesCount > 0)
                        <span class="badge-error text-xs ml-auto">{{ $openIssuesCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.contact-messages.index') }}" class="sidebar-link {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope"></i> Kontaktanfragen
                    @if($unreadContactCount > 0)
                        <span class="badge-error text-xs ml-auto">{{ $unreadContactCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.exam-feedback.index') }}" class="sidebar-link {{ request()->routeIs('admin.exam-feedback.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i> Prüfungs-Feedback
                </a>
                <a href="{{ route('admin.logs.index', 'scheduler') }}" class="sidebar-link {{ request()->routeIs('admin.logs.*') && request()->route('type') === 'scheduler' ? 'active' : '' }}">
                    <i class="bi bi-terminal"></i> Scheduler-Logs
                </a>
                <a href="{{ route('admin.logs.index', 'worker') }}" class="sidebar-link {{ request()->routeIs('admin.logs.*') && request()->route('type') === 'worker' ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Worker-Logs
                </a>
                @if(!app()->environment('production'))
                <a href="{{ route('admin.time-simulator') }}" class="sidebar-link {{ request()->routeIs('admin.time-simulator*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Zeitsimulator
                </a>
                @endif
                @endif
            </nav>

            <!-- Footer -->
            <div class="mobile-sidebar-footer">
                <div class="mobile-footer-links">
                    <a href="{{ route('landing.impressum') }}">Impressum</a>
                    <a href="{{ route('landing.datenschutz') }}">Datenschutz</a>
                    <a href="https://thw-trainer.de/statistik" target="_blank">Statistik</a>
                </div>

                <a href="{{ route('profile') }}" class="sidebar-user-card">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="sidebar-user-avatar">
                    <div class="sidebar-user-meta">
                        <p class="sidebar-user-name">{{ auth()->user()->name }}</p>
                        <div class="sidebar-level-row">
                            <span class="sidebar-level-text">Lvl {{ auth()->user()->level ?? 1 }}</span>
                            <div class="sidebar-level-track"><div class="sidebar-level-fill" style="width: {{ $sidebarLevelProgress }}%;"></div></div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-dark-muted" style="font-size: 0.85rem;"></i>
                </a>

                <div class="mobile-action-row">
                    <a href="{{ route('notifications.index') }}" class="mobile-action-btn">
                        <i class="bi bi-bell"></i> Mitteilungen
                        @if($sidebarNotificationCount > 0)
                            <span class="badge-error text-xs ml-auto">{{ $sidebarNotificationCount }}</span>
                        @endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="mobile-action-btn w-full">
                            <i class="bi bi-box-arrow-right"></i> Abmelden
                        </button>
                    </form>
                </div>
            </div>
        </aside>
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

        <!-- Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', async () => {
                    try {
                        // Prüfe ob SW bereits registriert ist — NICHT erneut register() aufrufen
                        // iOS Safari Bug: Erneutes register() kann Push-Events brechen
                        let registration = await navigator.serviceWorker.getRegistration('/');
                        if (registration) {
                            console.log('[SW] Already registered, reusing');
                        } else {
                            registration = await navigator.serviceWorker.register('/sw.js');
                            console.log('[SW] Newly registered');
                        }
                        window.swRegistration = registration;
                        revalidatePushSubscription(registration);
                    } catch (error) {
                        console.log('[SW] Failed:', error);
                    }
                });
            }

            async function revalidatePushSubscription(registration) {
                if (Notification.permission !== 'granted') return;
                try {
                    const subscription = await registration.pushManager.getSubscription();
                    if (!subscription) {
                        console.log('[Push] Subscription lost, re-subscribing...');
                        const keyResponse = await fetch('/push/key');
                        const keyData = await keyResponse.json();
                        if (!keyData.success) return;

                        const padding = '='.repeat((4 - keyData.publicKey.length % 4) % 4);
                        const base64 = (keyData.publicKey + padding).replace(/-/g, '+').replace(/_/g, '/');
                        const rawData = window.atob(base64);
                        const applicationServerKey = new Uint8Array(rawData.length);
                        for (let i = 0; i < rawData.length; ++i) applicationServerKey[i] = rawData.charCodeAt(i);

                        const newSub = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: applicationServerKey,
                        });
                        const sub = newSub.toJSON();
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfMeta) return;
                        await fetch('/push/subscribe', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfMeta.content,
                            },
                            body: JSON.stringify({
                                endpoint: sub.endpoint,
                                keys: { p256dh: sub.keys.p256dh, auth: sub.keys.auth },
                                contentEncoding: (PushManager.supportedContentEncodings || ['aes128gcm'])[0],
                            }),
                        });
                        console.log('[Push] Re-subscription successful');
                    }
                } catch (e) {
                    console.error('[Push] Re-validation failed:', e);
                }
            }
        </script>

        <!-- Page-specific scripts -->
        @stack('scripts')

    </body>
</html>
