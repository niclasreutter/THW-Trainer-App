<nav x-data="{ open: false }" style="background-color: #00337F;">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo/Text -->
                <div class="shrink-0 flex items-center">
                    <a href="/">
                        <img src="{{ asset('logo-thwtrainer_w.png') }}" alt="THW-Trainer Logo" style="height:100%;max-height:2.5rem;width:auto;" class="mr-2 inline-block align-middle" />
                        <!-- <span class="font-bold text-white text-xl hover:text-yellow-400 transition align-middle">THW-Trainer</span> -->
                    </a>
                    @if(app()->environment('testing') || str_contains(request()->getHost(), 'test.') || config('app.environment_type') === 'testing')
                        <span class="bg-red-600 text-white px-2 py-1 rounded text-sm font-bold ml-2">Test-System!</span>
                    @endif
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex items-center">
                    @auth
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-yellow-400 transition-colors duration-200 relative group {{ request()->routeIs('dashboard') ? 'text-yellow-400' : '' }}">
                            <span class="flex items-center space-x-2">
                                <span class="text-lg">🏠</span>
                                <span>Dashboard</span>
                            </span>
                            @if(request()->routeIs('dashboard'))
                                <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-yellow-400"></div>
                            @else
                                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                            @endif
                        </a>

                        <!-- Lernen Dropdown -->
                        <div class="relative">
                            <button onclick="document.getElementById('learningDropdown').classList.toggle('hidden')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-yellow-400 transition-colors duration-200 relative group">
                                <span class="flex items-center space-x-2">
                                    <span class="text-lg">📚</span>
                                    <span>Lernen</span>
                                    <svg class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </span>
                                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                            </button>
                            <div id="learningDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-50 hidden border border-gray-200">
                                <a href="{{ route('lehrgaenge.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                    <span class="text-lg">📚</span>
                                    <span>Lehrgänge</span>
                                </a>
                                <a href="{{ route('practice.menu') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                    <span class="text-lg">📝</span>
                                    <span>Übungsmenü</span>
                                </a>
                                <a href="{{ route('bookmarks.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                    <span class="text-lg">🔖</span>
                                    <span>Gespeicherte Fragen</span>
                                </a>
                                @php
                                    $failedArr = is_array(Auth::user()->exam_failed_questions ?? null) 
                                        ? Auth::user()->exam_failed_questions 
                                        : (is_string(Auth::user()->exam_failed_questions) ? json_decode(Auth::user()->exam_failed_questions, true) ?? [] : []);
                                @endphp
                                @if($failedArr && count($failedArr) > 0)
                                    <a href="{{ route('failed.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                        <span class="text-lg">🔄</span>
                                        <span>Fehler wiederholen</span>
                                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count($failedArr) }}</span>
                                    </a>
                                @endif
                                <a href="{{ route('exam.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                    <span class="text-lg">🎓</span>
                                    <span>Prüfung</span>
                                </a>
                            </div>
                        </div>

                        <!-- Gamification Dropdown -->
                        <div class="relative">
                            <button onclick="document.getElementById('gamificationDropdown').classList.toggle('hidden')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-yellow-400 transition-colors duration-200 relative group">
                                <span class="flex items-center space-x-2">
                                    <span class="text-lg">🎮</span>
                                    <span>Gamification</span>
                                    <svg class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </span>
                                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                            </button>
                            <div id="gamificationDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-50 hidden border border-gray-200">
                                <a href="{{ route('gamification.achievements') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                    <span class="text-lg">🏆</span>
                                    <span>Achievements</span>
                                </a>
                                <a href="{{ route('gamification.leaderboard') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                    <span class="text-lg">📊</span>
                                    <span>Leaderboard</span>
                                </a>
                                <a href="{{ route('statistics') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                    <span class="text-lg">📈</span>
                                    <span>Statistik</span>
                                </a>
                            </div>
                        </div>

                        <!-- Kontakt (nur für eingeloggte User) -->
                        <a href="{{ route('contact.index') }}" 
                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-yellow-400 transition-colors duration-200 relative group {{ request()->routeIs('contact.*') ? 'text-yellow-400' : '' }}">
                            <span class="flex items-center space-x-2">
                                <span class="text-lg">📬</span>
                                <span>Kontakt</span>
                            </span>
                            @if(request()->routeIs('contact.*'))
                                <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-yellow-400"></div>
                            @else
                                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                            @endif
                        </a>

                        <!-- Ortsverband -->
                        <a href="{{ route('ortsverband.index') }}" 
                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-yellow-400 transition-colors duration-200 relative group {{ request()->routeIs('ortsverband.*') ? 'text-yellow-400' : '' }}">
                            <span class="flex items-center space-x-2">
                                <span class="text-lg">🏠</span>
                                <span>Ortsverband</span>
                            </span>
                            @if(request()->routeIs('ortsverband.*'))
                                <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-yellow-400"></div>
                            @else
                                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                            @endif
                        </a>
                    @endauth
                    
                    @guest
                        <!-- Öffentliche Statistik (für Gäste) -->
                        <a href="{{ route('statistics') }}" 
                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-yellow-400 transition-colors duration-200 relative group {{ request()->routeIs('statistics') ? 'text-yellow-400' : '' }}">
                            <span class="flex items-center space-x-2">
                                <span class="text-lg">📬</span>
                                <span>Statistik</span>
                            </span>
                            @if(request()->routeIs('statistics'))
                                <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-yellow-400"></div>
                            @else
                                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                            @endif
                        </a>
                    @endguest
                    
                        @auth
                            @if(Auth::user()->useroll === 'admin')
                                <div class="relative ml-2">
                                    <button onclick="document.getElementById('adminDropdown').classList.toggle('hidden')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-yellow-400 transition-colors duration-200 relative group">
                                        <span class="flex items-center space-x-2">
                                            <span class="text-lg">⚙️</span>
                                            <span>Administration</span>
                                            <svg class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </span>
                                        <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                                    </button>
                                    <div id="adminDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50 hidden border border-gray-200">
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">📊</span>
                                            <span>Admin Dashboard</span>
                                        </a>
                                        <a href="/admin/questions" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">❓</span>
                                            <span>Fragen</span>
                                        </a>
                                        <a href="{{ route('admin.lehrgaenge.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">📚</span>
                                            <span>Lehrgänge</span>
                                        </a>
                                        <a href="/admin/users" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">👥</span>
                                            <span>Nutzerverwaltung</span>
                                        </a>
                                        <a href="{{ route('admin.newsletter.create') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">📧</span>
                                            <span>Newsletter</span>
                                        </a>
                                        <a href="{{ route('admin.lehrgang-issues.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">🐛</span>
                                            <span>Fehlermeldungen</span>
                                            @php
                                                // Cache open issues count for 5 minutes
                                                $openIssuesCount = cache()->remember('admin_open_issues_count', 300, function() {
                                                    return \App\Models\LehrgangQuestionIssue::where('status', 'open')->count();
                                                });
                                            @endphp
                                            @if($openIssuesCount > 0)
                                                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $openIssuesCount }}</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('admin.contact-messages.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">📬</span>
                                            <span>Kontaktanfragen</span>
                                            @php
                                                // Cache unread count for 5 minutes
                                                $unreadCount = cache()->remember('admin_unread_messages_count', 300, function() {
                                                    return \App\Models\ContactMessage::where('is_read', false)->count();
                                                });
                                            @endphp
                                            @if($unreadCount > 0)
                                                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('admin.ortsverband.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">🏢</span>
                                            <span>Ortsverbände</span>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            @auth
            @php
                // TEMPORÄR: Hardcoded count für Demo
                // TODO: Später durch echte Notifications aus DB ersetzen
                $notificationCount = 3;
            @endphp
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-1">
                <!-- Notifications Bell - Minimalistisch -->
                <div class="relative">
                    <button onclick="document.getElementById('notificationsDropdown').classList.toggle('hidden')" class="relative p-1 text-white hover:text-yellow-400 transition-colors duration-200">
                        <span class="text-xl">🔔</span>
                        @if($notificationCount > 0)
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">
                                {{ $notificationCount }}
                            </span>
                        @endif
                    </button>
                    <div id="notificationsDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 hidden border border-gray-200">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-700">🔔 Mitteilungen</h3>
                                <span class="text-xs text-gray-500">{{ $notificationCount }} neu</span>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <!-- TEMPORÄR: Demo Notifications -->
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors duration-200 border-b border-gray-100">
                                <div class="flex items-start space-x-3">
                                    <span class="text-2xl">🎉</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Neuer Meilenstein erreicht!</p>
                                        <p class="text-xs text-gray-600 mt-1">Du hast 100% Fortschritt erreicht.</p>
                                        <p class="text-xs text-gray-400 mt-1">vor 2 Stunden</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors duration-200 border-b border-gray-100">
                                <div class="flex items-start space-x-3">
                                    <span class="text-2xl">🏆</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Neues Achievement freigeschaltet!</p>
                                        <p class="text-xs text-gray-600 mt-1">"Prüfungsprofi" - 5 Prüfungen bestanden</p>
                                        <p class="text-xs text-gray-400 mt-1">vor 1 Tag</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-start space-x-3">
                                    <span class="text-2xl">⭐</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Level Up!</p>
                                        <p class="text-xs text-gray-600 mt-1">Du bist jetzt Level {{ Auth::user()->level ?? 1 }}</p>
                                        <p class="text-xs text-gray-400 mt-1">vor 2 Tagen</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="px-4 py-2 border-t border-gray-200 bg-gray-50">
                            <a href="#" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Alle Mitteilungen anzeigen →</a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative">
                    <button onclick="document.getElementById('userDropdown').classList.toggle('hidden')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-yellow-400 transition-colors duration-200 relative group">
                        <span class="flex items-center space-x-2">
                            <span class="text-lg">👤</span>
                            <span class="relative">
                                {{ Auth::user()->name }}
                                @if($notificationCount > 0)
                                    <span class="absolute -top-2 -right-6 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">
                                        {{ $notificationCount }}
                                    </span>
                                @endif
                            </span>
                            <svg class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </span>
                        <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                    </button>
                    <div id="userDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl z-50 hidden border border-gray-200">
                        <!-- Gamification Stats im Dropdown -->
                        <div class="px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                            <div class="text-xs font-semibold text-gray-600 mb-2">Deine Stats</div>
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between bg-yellow-500/10 rounded-lg px-3 py-1.5">
                                    <span class="text-sm font-medium text-gray-700">⭐ Level</span>
                                    <span class="text-sm font-bold text-yellow-600">{{ Auth::user()->level ?? 1 }}</span>
                                </div>
                                <div class="flex items-center justify-between bg-green-500/10 rounded-lg px-3 py-1.5">
                                    <span class="text-sm font-medium text-gray-700">💎 Punkte</span>
                                    <span class="text-sm font-bold text-green-600">{{ number_format(Auth::user()->points ?? 0) }}</span>
                                </div>
                                @if((Auth::user()->streak_days ?? 0) > 0)
                                    <div class="flex items-center justify-between bg-orange-500/10 rounded-lg px-3 py-1.5">
                                        <span class="text-sm font-medium text-gray-700">🔥 Streak</span>
                                        <span class="text-sm font-bold text-orange-600">{{ Auth::user()->streak_days }} Tage</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Mitteilungen Link -->
                        <button onclick="document.getElementById('userDropdown').classList.add('hidden'); document.getElementById('notificationsDropdown').classList.remove('hidden');" class="w-full text-left px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center justify-between border-b border-gray-100">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg">🔔</span>
                                <span>Mitteilungen</span>
                            </div>
                            @if($notificationCount > 0)
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $notificationCount }}</span>
                            @endif
                        </button>

                        <a href="{{ route('profile') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                            <span class="text-lg">⚙️</span>
                            <span>Profil</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                <span class="text-lg">🚪</span>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endauth

            <!-- Login/Register Links for Guests -->
            @guest
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-8">
                <a href="{{ route('login') }}" 
                   class="text-white hover:text-yellow-400 font-medium text-sm transition-colors duration-200 relative group">
                    <span class="flex items-center space-x-2">
                        <span class="text-lg">🔑</span>
                        <span>Anmelden</span>
                    </span>
                    <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                </a>
                <a href="{{ route('register') }}" 
                   class="text-white hover:text-yellow-400 font-medium text-sm transition-colors duration-200 relative group">
                    <span class="flex items-center space-x-2">
                        <span class="text-lg">📝</span>
                        <span>Registrieren</span>
                    </span>
                    <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                </a>
            </div>
            @endguest

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-yellow-400 hover:bg-blue-800 focus:outline-none focus:bg-blue-800 focus:text-yellow-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-blue-900">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @auth
                <a href="{{ route('dashboard') }}" 
                   class="block px-3 py-2 text-base font-medium text-white hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2 {{ request()->routeIs('dashboard') ? 'text-yellow-400 bg-blue-800' : '' }}">
                    <span class="text-lg">🏠</span>
                    <span>Dashboard</span>
                </a>
                
                <!-- Lernen Section -->
                <div class="px-3 py-2 text-base font-medium text-white flex items-center space-x-2">
                    <span class="text-lg">📚</span>
                    <span>Lernen</span>
                </div>
                <div class="ml-6 space-y-1">
                    <a href="{{ route('practice.menu') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                        <span class="text-lg">📝</span>
                        <span>Übungsmenü</span>
                    </a>
                    <a href="{{ route('bookmarks.index') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                        <span class="text-lg">🔖</span>
                        <span>Gespeicherte Fragen</span>
                    </a>
                    @php
                        $failedArr = is_array(Auth::user()->exam_failed_questions ?? null) 
                            ? Auth::user()->exam_failed_questions 
                            : (is_string(Auth::user()->exam_failed_questions) ? json_decode(Auth::user()->exam_failed_questions, true) ?? [] : []);
                    @endphp
                    @if($failedArr && count($failedArr) > 0)
                        <a href="{{ route('failed.index') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                            <span class="text-lg">🔄</span>
                            <span>Fehler wiederholen</span>
                            <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count($failedArr) }}</span>
                        </a>
                    @endif
                    <a href="{{ route('exam.index') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                        <span class="text-lg">🎓</span>
                        <span>Prüfung</span>
                    </a>
                </div>
                
                <!-- Gamification Section -->
                <div class="px-3 py-2 text-base font-medium text-white flex items-center space-x-2">
                    <span class="text-lg">🎮</span>
                    <span>Gamification</span>
                </div>
                <div class="ml-6 space-y-1">
                    <a href="{{ route('gamification.achievements') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                        <span class="text-lg">🏆</span>
                        <span>Achievements</span>
                    </a>
                    <a href="{{ route('gamification.leaderboard') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                        <span class="text-lg">📊</span>
                        <span>Leaderboard</span>
                    </a>
                    <a href="{{ route('statistics') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                        <span class="text-lg">📈</span>
                        <span>Statistik</span>
                    </a>
                </div>
                
                <!-- Kontakt (nur für eingeloggte User) -->
                <a href="{{ route('contact.index') }}" 
                   class="block px-3 py-2 text-base font-medium text-white hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2 {{ request()->routeIs('contact.*') ? 'text-yellow-400 bg-blue-800' : '' }}">
                    <span class="text-lg">📬</span>
                    <span>Kontakt & Feedback</span>
                </a>

                <!-- Ortsverband -->
                <a href="{{ route('ortsverband.index') }}" 
                   class="block px-3 py-2 text-base font-medium text-white hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2 {{ request()->routeIs('ortsverband.*') ? 'text-yellow-400 bg-blue-800' : '' }}">
                    <span class="text-lg">🏠</span>
                    <span>Ortsverband</span>
                </a>
                
                @if(Auth::user()->useroll === 'admin')
                    <div class="px-3 py-2 text-base font-medium text-white flex items-center space-x-2">
                        <span class="text-lg">⚙️</span>
                        <span>Administration</span>
                    </div>
                    <div class="ml-6 space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                            <span class="text-lg">📊</span>
                            <span>Admin Dashboard</span>
                        </a>
                        <a href="/admin/questions" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                            <span class="text-lg">❓</span>
                            <span>Fragen</span>
                        </a>
                        <a href="{{ route('admin.lehrgaenge.index') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                            <span class="text-lg">📚</span>
                            <span>Lehrgänge</span>
                        </a>
                        <a href="/admin/users" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                            <span class="text-lg">👥</span>
                            <span>Nutzerverwaltung</span>
                        </a>
                        <a href="{{ route('admin.newsletter.create') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                            <span class="text-lg">📧</span>
                            <span>Newsletter</span>
                        </a>
                        <a href="{{ route('admin.contact-messages.index') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                            <span class="text-lg">📬</span>
                            <span>Kontaktanfragen</span>
                            @php
                                // Cache unread count for 5 minutes
                                $unreadCount = cache()->remember('admin_unread_messages_count', 300, function() {
                                    return \App\Models\ContactMessage::where('is_read', false)->count();
                                });
                            @endphp
                            @if($unreadCount > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.ortsverband.index') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                            <span class="text-lg">🏢</span>
                            <span>Ortsverbände</span>
                        </a>
                    </div>
                @endif
            @endauth
            @guest
                <a href="{{ route('statistics') }}" 
                   class="block px-3 py-2 text-base font-medium text-white hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2 {{ request()->routeIs('statistics') ? 'text-yellow-400 bg-blue-800' : '' }}">
                    <span class="text-lg">📈</span>
                    <span>Statistik</span>
                </a>
                <a href="{{ route('login') }}" 
                   class="block px-3 py-2 text-base font-medium text-white hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                    <span class="text-lg">🔑</span>
                    <span>Anmelden</span>
                </a>
                <a href="{{ route('register') }}" 
                   class="block px-3 py-2 text-base font-medium text-white hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                    <span class="text-lg">📝</span>
                    <span>Registrieren</span>
                </a>
            @endguest
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1 px-4">
                <a href="{{ route('profile') }}" 
                   class="block px-3 py-2 text-base font-medium text-white hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                    <span class="text-lg">⚙️</span>
                    <span>Profil</span>
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full text-left px-3 py-2 text-base font-medium text-white hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2">
                        <span class="text-lg">🚪</span>
                        <span>Logout</span>
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
</script>
