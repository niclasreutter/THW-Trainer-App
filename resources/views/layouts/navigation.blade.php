<nav x-data="{ open: false }" class="nav-glass">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo/Text -->
                <div class="shrink-0 flex items-center">
                    <a href="/">
                        <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW-Trainer Logo" style="height:100%;max-height:2.5rem;width:auto;" class="mr-2 inline-block align-middle" />
                    </a>
                    @if(app()->environment('testing') || str_contains(request()->getHost(), 'test.') || config('app.environment_type') === 'testing')
                        <span class="badge-error ml-2">Test-System!</span>
                    @endif
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex items-center">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="nav-link-glass {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            Dashboard
                        </a>

                        <!-- Lernen Dropdown -->
                        <div class="relative">
                            <button onclick="document.getElementById('learningDropdown').classList.toggle('hidden')"
                                    class="nav-link-glass flex items-center gap-1">
                                <span>Lernen</span>
                                <svg class="h-4 w-4 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div id="learningDropdown" class="absolute left-0 mt-2 w-56 dropdown-glass hidden z-50">
                                <a href="{{ route('lehrgaenge.index') }}" class="dropdown-item-glass">
                                    Lehrgänge
                                </a>
                                <a href="{{ route('practice.menu') }}" class="dropdown-item-glass">
                                    Übungsmenü
                                </a>
                                <a href="{{ route('bookmarks.index') }}" class="dropdown-item-glass">
                                    Gespeicherte Fragen
                                </a>
                                @php
                                    $failedArr = is_array(Auth::user()->exam_failed_questions ?? null)
                                        ? Auth::user()->exam_failed_questions
                                        : (is_string(Auth::user()->exam_failed_questions) ? json_decode(Auth::user()->exam_failed_questions, true) ?? [] : []);
                                @endphp
                                @if($failedArr && count($failedArr) > 0)
                                    <a href="{{ route('failed.index') }}" class="dropdown-item-glass flex items-center justify-between">
                                        <span>Fehler wiederholen</span>
                                        <span class="badge-error text-xs">{{ count($failedArr) }}</span>
                                    </a>
                                @endif
                                <a href="{{ route('exam.index') }}" class="dropdown-item-glass">
                                    Prüfung
                                </a>
                            </div>
                        </div>

                        <!-- Gamification Dropdown -->
                        <div class="relative">
                            <button onclick="document.getElementById('gamificationDropdown').classList.toggle('hidden')"
                                    class="nav-link-glass flex items-center gap-1">
                                <span>Gamification</span>
                                <svg class="h-4 w-4 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div id="gamificationDropdown" class="absolute left-0 mt-2 w-56 dropdown-glass hidden z-50">
                                <a href="{{ route('gamification.achievements') }}" class="dropdown-item-glass">
                                    Achievements
                                </a>
                                <a href="{{ route('gamification.leaderboard') }}" class="dropdown-item-glass">
                                    Leaderboard
                                </a>
                                <a href="{{ route('statistics') }}" class="dropdown-item-glass">
                                    Statistik
                                </a>
                            </div>
                        </div>

                        <!-- Kontakt -->
                        <a href="{{ route('contact.index') }}"
                           class="nav-link-glass {{ request()->routeIs('contact.*') ? 'active' : '' }}">
                            Kontakt
                        </a>

                        <!-- Ortsverband -->
                        <a href="{{ route('ortsverband.index') }}"
                           class="nav-link-glass {{ request()->routeIs('ortsverband.*') ? 'active' : '' }}">
                            Ortsverband
                        </a>
                    @endauth

                    @guest
                        <!-- Öffentliche Statistik (für Gäste) -->
                        <a href="{{ route('statistics') }}"
                           class="nav-link-glass {{ request()->routeIs('statistics') ? 'active' : '' }}">
                            Statistik
                        </a>
                    @endguest

                    @auth
                        @if(Auth::user()->useroll === 'admin')
                            <div class="relative ml-2">
                                <button onclick="document.getElementById('adminDropdown').classList.toggle('hidden')"
                                        class="nav-link-glass flex items-center gap-1">
                                    <span>Administration</span>
                                    <svg class="h-4 w-4 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div id="adminDropdown" class="absolute right-0 mt-2 w-48 dropdown-glass hidden z-50">
                                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item-glass">
                                        Admin Dashboard
                                    </a>
                                    <a href="/admin/questions" class="dropdown-item-glass">
                                        Fragen
                                    </a>
                                    <a href="{{ route('admin.lehrgaenge.index') }}" class="dropdown-item-glass">
                                        Lehrgänge
                                    </a>
                                    <a href="/admin/users" class="dropdown-item-glass">
                                        Nutzerverwaltung
                                    </a>
                                    <a href="{{ route('admin.newsletter.create') }}" class="dropdown-item-glass">
                                        Newsletter
                                    </a>
                                    <a href="{{ route('admin.lehrgang-issues.index') }}" class="dropdown-item-glass flex items-center justify-between">
                                        <span>Fehlermeldungen</span>
                                        @php
                                            $openIssuesCount = cache()->remember('admin_open_issues_count', 300, function() {
                                                return \App\Models\LehrgangQuestionIssue::where('status', 'open')->count();
                                            });
                                        @endphp
                                        @if($openIssuesCount > 0)
                                            <span class="badge-error text-xs">{{ $openIssuesCount }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.contact-messages.index') }}" class="dropdown-item-glass flex items-center justify-between">
                                        <span>Kontaktanfragen</span>
                                        @php
                                            $unreadCount = cache()->remember('admin_unread_messages_count', 300, function() {
                                                return \App\Models\ContactMessage::where('is_read', false)->count();
                                            });
                                        @endphp
                                        @if($unreadCount > 0)
                                            <span class="badge-error text-xs">{{ $unreadCount }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.ortsverband.index') }}" class="dropdown-item-glass">
                                        Ortsverbände
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Theme Toggle & Settings -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <!-- Theme Toggle -->
                <button type="button" onclick="toggleTheme()" class="theme-toggle" title="Farbschema wechseln">
                    <svg class="icon-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="icon-sun" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

            @auth
            @php
                $notificationCount = Auth::user()->unreadNotifications()->count();
            @endphp
                <!-- User Dropdown -->
                <div class="relative">
                    <button onclick="document.getElementById('userDropdown').classList.toggle('hidden')"
                            class="nav-link-glass flex items-center gap-1">
                        <span class="relative">
                            {{ Auth::user()->name }}
                            @if($notificationCount > 0)
                                <span class="absolute -top-2 -right-6 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-error rounded-full">
                                    {{ $notificationCount }}
                                </span>
                            @endif
                        </span>
                        <svg class="h-4 w-4 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div id="userDropdown" class="absolute right-0 mt-2 w-64 dropdown-glass hidden z-50">
                        <!-- Gamification Stats im Dropdown -->
                        <div class="px-4 py-3 border-b border-glass-subtle">
                            <div class="text-xs font-semibold text-dark-muted mb-2 uppercase tracking-wide">Deine Stats</div>
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between glass-subtle px-3 py-1.5 rounded-lg">
                                    <span class="text-sm font-medium text-dark-secondary">Level</span>
                                    <span class="text-sm font-bold text-gradient-gold">{{ Auth::user()->level ?? 1 }}</span>
                                </div>
                                <div class="flex items-center justify-between glass-subtle px-3 py-1.5 rounded-lg">
                                    <span class="text-sm font-medium text-dark-secondary">Punkte</span>
                                    <span class="text-sm font-bold text-success">{{ number_format(Auth::user()->points ?? 0) }}</span>
                                </div>
                                @if((Auth::user()->streak_days ?? 0) > 0)
                                    <div class="flex items-center justify-between glass-subtle px-3 py-1.5 rounded-lg">
                                        <span class="text-sm font-medium text-dark-secondary">Streak</span>
                                        <span class="text-sm font-bold text-warning">{{ Auth::user()->streak_days }} Tage</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Mitteilungen Link -->
                        <button onclick="document.getElementById('userDropdown').classList.add('hidden'); document.getElementById('notificationsDropdown').classList.remove('hidden');"
                                class="w-full dropdown-item-glass flex items-center justify-between border-b border-glass-subtle">
                            <span>Mitteilungen</span>
                            @if($notificationCount > 0)
                                <span class="badge-error text-xs">{{ $notificationCount }}</span>
                            @endif
                        </button>

                        <a href="{{ route('profile') }}" class="dropdown-item-glass">
                            Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full dropdown-item-glass text-left">
                                Logout
                            </button>
                        </form>
                    </div>

                    <!-- Notifications Dropdown -->
                    <div id="notificationsDropdown" class="absolute right-0 mt-2 w-80 dropdown-glass hidden z-50">
                        <div class="px-4 py-3 border-b border-glass-subtle">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-dark-primary">Mitteilungen</h3>
                                <span class="text-xs text-dark-muted">{{ $notificationCount }} neu</span>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @php
                                $recentNotifications = Auth::user()->notifications()->limit(10)->get();
                            @endphp
                            @forelse($recentNotifications as $notification)
                                <div class="block px-4 py-3 hover:bg-glass-white-5 transition-colors duration-200 border-b border-glass-subtle {{ $notification->is_read ? '' : 'bg-glass-thw-5' }}"
                                     onclick="markNotificationAsRead({{ $notification->id }})">
                                    <div class="flex items-start space-x-3">
                                        <span class="text-2xl text-dark-secondary"><i class="bi bi-bell"></i></span>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-dark-primary">{{ $notification->title }}</p>
                                            <p class="text-xs text-dark-secondary mt-1">{{ $notification->message }}</p>
                                            <p class="text-xs text-dark-muted mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(!$notification->is_read)
                                            <span class="w-2 h-2 bg-gold rounded-full"></span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center text-dark-muted">
                                    <p class="text-sm">Keine Mitteilungen vorhanden</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2 border-t border-glass-subtle flex items-center justify-between">
                            <button onclick="document.getElementById('notificationsDropdown').classList.add('hidden'); document.getElementById('userDropdown').classList.remove('hidden');"
                                    class="text-xs text-gold hover:text-gold-light font-medium transition-colors">
                                Zurück
                            </button>
                            @if($recentNotifications->count() > 0)
                                <a href="{{ route('notifications.index') }}" class="text-xs text-gold hover:text-gold-light font-medium transition-colors">
                                    Alle anzeigen
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endauth

            <!-- Login/Register Links for Guests -->
            @guest
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <!-- Theme Toggle for Guests -->
                <button type="button" onclick="toggleTheme()" class="theme-toggle" title="Farbschema wechseln">
                    <svg class="icon-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="icon-sun" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>
                <a href="{{ route('login') }}" class="nav-link-glass">
                    Anmelden
                </a>
                <a href="{{ route('register') }}" class="btn-primary btn-sm">
                    Registrieren
                </a>
            </div>
            @endguest

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-dark-primary hover:text-gold hover:bg-glass-white-5 focus:outline-none focus:bg-glass-white-10 focus:text-gold transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden" style="background: var(--bg-overlay);">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="block px-3 py-2 text-base font-medium text-dark-primary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-gold bg-glass-white-5' : '' }}">
                    Dashboard
                </a>

                <!-- Lernen Section -->
                <div class="px-3 py-2 text-xs font-semibold text-dark-muted uppercase tracking-wide">
                    Lernen
                </div>
                <div class="ml-4 space-y-1">
                    <a href="{{ route('lehrgaenge.index') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                        Lehrgänge
                    </a>
                    <a href="{{ route('practice.menu') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                        Übungsmenü
                    </a>
                    <a href="{{ route('bookmarks.index') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                        Gespeicherte Fragen
                    </a>
                    @php
                        $failedArr = is_array(Auth::user()->exam_failed_questions ?? null)
                            ? Auth::user()->exam_failed_questions
                            : (is_string(Auth::user()->exam_failed_questions) ? json_decode(Auth::user()->exam_failed_questions, true) ?? [] : []);
                    @endphp
                    @if($failedArr && count($failedArr) > 0)
                        <a href="{{ route('failed.index') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200 flex items-center justify-between">
                            <span>Fehler wiederholen</span>
                            <span class="badge-error text-xs">{{ count($failedArr) }}</span>
                        </a>
                    @endif
                    <a href="{{ route('exam.index') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                        Prüfung
                    </a>
                </div>

                <!-- Gamification Section -->
                <div class="px-3 py-2 text-xs font-semibold text-dark-muted uppercase tracking-wide">
                    Gamification
                </div>
                <div class="ml-4 space-y-1">
                    <a href="{{ route('gamification.achievements') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                        Achievements
                    </a>
                    <a href="{{ route('gamification.leaderboard') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                        Leaderboard
                    </a>
                    <a href="{{ route('statistics') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                        Statistik
                    </a>
                </div>

                <!-- Kontakt -->
                <a href="{{ route('contact.index') }}"
                   class="block px-3 py-2 text-base font-medium text-dark-primary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200 {{ request()->routeIs('contact.*') ? 'text-gold bg-glass-white-5' : '' }}">
                    Kontakt & Feedback
                </a>

                <!-- Ortsverband -->
                <a href="{{ route('ortsverband.index') }}"
                   class="block px-3 py-2 text-base font-medium text-dark-primary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200 {{ request()->routeIs('ortsverband.*') ? 'text-gold bg-glass-white-5' : '' }}">
                    Ortsverband
                </a>

                @if(Auth::user()->useroll === 'admin')
                    <div class="px-3 py-2 text-xs font-semibold text-dark-muted uppercase tracking-wide">
                        Administration
                    </div>
                    <div class="ml-4 space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                            Admin Dashboard
                        </a>
                        <a href="/admin/questions" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                            Fragen
                        </a>
                        <a href="{{ route('admin.lehrgaenge.index') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                            Lehrgänge
                        </a>
                        <a href="/admin/users" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                            Nutzerverwaltung
                        </a>
                        <a href="{{ route('admin.newsletter.create') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                            Newsletter
                        </a>
                        <a href="{{ route('admin.contact-messages.index') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200 flex items-center justify-between">
                            <span>Kontaktanfragen</span>
                            @php
                                $unreadCount = cache()->remember('admin_unread_messages_count', 300, function() {
                                    return \App\Models\ContactMessage::where('is_read', false)->count();
                                });
                            @endphp
                            @if($unreadCount > 0)
                                <span class="badge-error text-xs">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.ortsverband.index') }}" class="block px-3 py-2 text-sm text-dark-secondary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                            Ortsverbände
                        </a>
                    </div>
                @endif
            @endauth

            @guest
                <a href="{{ route('statistics') }}"
                   class="block px-3 py-2 text-base font-medium text-dark-primary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200 {{ request()->routeIs('statistics') ? 'text-gold bg-glass-white-5' : '' }}">
                    Statistik
                </a>
                <a href="{{ route('login') }}"
                   class="block px-3 py-2 text-base font-medium text-dark-primary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                    Anmelden
                </a>
                <a href="{{ route('register') }}"
                   class="block px-3 py-2 text-base font-medium text-dark-primary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                    Registrieren
                </a>
            @endguest
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-glass-subtle">
            <div class="px-4">
                <div class="font-medium text-base text-dark-primary">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-dark-muted">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1 px-4">
                <!-- Gamification Stats -->
                <div class="mb-3 p-3 glass rounded-lg">
                    <div class="text-xs font-semibold text-dark-muted mb-2 uppercase tracking-wide">Deine Stats</div>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div>
                            <div class="text-lg font-bold text-gradient-gold">{{ Auth::user()->level ?? 1 }}</div>
                            <div class="text-xs text-dark-muted">Level</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-success">{{ number_format(Auth::user()->points ?? 0) }}</div>
                            <div class="text-xs text-dark-muted">Punkte</div>
                        </div>
                        @if((Auth::user()->streak_days ?? 0) > 0)
                        <div>
                            <div class="text-lg font-bold text-warning">{{ Auth::user()->streak_days }}</div>
                            <div class="text-xs text-dark-muted">Streak</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Mitteilungen -->
                @php
                    $mobileNotificationCount = Auth::user()->unreadNotifications()->count();
                @endphp
                <a href="{{ route('notifications.index') }}"
                   class="block px-3 py-2 text-base font-medium text-dark-primary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200 flex items-center justify-between">
                    <span>Mitteilungen</span>
                    @if($mobileNotificationCount > 0)
                        <span class="badge-error text-xs">{{ $mobileNotificationCount }}</span>
                    @endif
                </a>

                <a href="{{ route('profile') }}"
                   class="block px-3 py-2 text-base font-medium text-dark-primary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                    Profil
                </a>

                <!-- Theme Toggle Mobile -->
                <button type="button" onclick="toggleTheme()"
                        class="w-full px-3 py-2 text-base font-medium text-dark-primary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200 flex items-center justify-between">
                    <span>Farbschema</span>
                    <span class="flex items-center gap-2 text-sm text-dark-muted">
                        <span class="light-mode-hidden">Dunkel</span>
                        <span class="dark-mode-hidden">Hell</span>
                        <svg class="w-5 h-5 icon-moon light-mode-hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg class="w-5 h-5 icon-sun dark-mode-hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </span>
                </button>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-3 py-2 text-base font-medium text-dark-primary hover:text-gold hover:bg-glass-white-5 rounded-md transition-colors duration-200">
                        Logout
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>

<script>
    // Close all dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = ['adminDropdown', 'userDropdown', 'learningDropdown', 'gamificationDropdown', 'notificationsDropdown'];

        dropdowns.forEach(function(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown && !dropdown.classList.contains('hidden')) {
                const button = dropdown.previousElementSibling;
                if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            }
        });
    });

    // Markiert eine Notification als gelesen
    function markNotificationAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
