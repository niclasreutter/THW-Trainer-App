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
                                <span class="text-lg">📊</span>
                                <span>Dashboard</span>
                            </span>
                            @if(request()->routeIs('dashboard'))
                                <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-yellow-400"></div>
                            @else
                                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                            @endif
                        </a>
                    @endauth
                    
                    <!-- Öffentliche Statistik (für alle sichtbar) -->
                    <a href="{{ route('statistics') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-yellow-400 transition-colors duration-200 relative group {{ request()->routeIs('statistics') ? 'text-yellow-400' : '' }}">
                        <span class="flex items-center space-x-2">
                            <span class="text-lg">📈</span>
                            <span>Statistik</span>
                        </span>
                        @if(request()->routeIs('statistics'))
                            <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-yellow-400"></div>
                        @else
                            <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                        @endif
                    </a>
                    
                    @auth
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
                    @endauth
                    
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
                                        <a href="/admin/users" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">👥</span>
                                            <span>Nutzerverwaltung</span>
                                        </a>
                                        <a href="{{ route('admin.newsletter.create') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">📧</span>
                                            <span>Newsletter</span>
                                        </a>
                                        <a href="{{ route('admin.contact-messages.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-900 hover:text-yellow-400 transition-colors duration-200 flex items-center space-x-2">
                                            <span class="text-lg">📬</span>
                                            <span>Kontaktanfragen</span>
                                            @php
                                                $unreadCount = \App\Models\ContactMessage::where('is_read', false)->count();
                                            @endphp
                                            @if($unreadCount > 0)
                                                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Gamification Stats -->
                <div class="mr-4 flex items-center space-x-2 text-white">
                    <div class="flex items-center bg-yellow-500 rounded-full px-2 py-1 text-xs font-medium text-white">
                        ⭐ {{ Auth::user()->level ?? 1 }}
                    </div>
                    <div class="flex items-center bg-green-500 rounded-full px-2 py-1 text-xs font-medium text-white">
                        💎 {{ number_format(Auth::user()->points ?? 0) }}
                    </div>
                    @if((Auth::user()->streak_days ?? 0) > 0)
                        <div class="flex items-center bg-orange-500 rounded-full px-2 py-1 text-xs font-medium text-white">
                            🔥 {{ Auth::user()->streak_days }}
                        </div>
                    @endif
                </div>
                <div class="relative ml-4">
                    <button onclick="document.getElementById('userDropdown').classList.toggle('hidden')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-yellow-400 transition-colors duration-200 relative group">
                        <span class="flex items-center space-x-2">
                            <span class="text-lg">👤</span>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </span>
                        <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-200 group-hover:w-full"></div>
                    </button>
                    <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50 hidden border border-gray-200">
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
                    <span class="text-lg">📊</span>
                    <span>Dashboard</span>
                </a>
                
                <!-- Öffentliche Statistik (für alle sichtbar) -->
                <a href="{{ route('statistics') }}" 
                   class="block px-3 py-2 text-base font-medium text-white hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2 {{ request()->routeIs('statistics') ? 'text-yellow-400 bg-blue-800' : '' }}">
                    <span class="text-lg">📈</span>
                    <span>Statistik</span>
                </a>
                
                <!-- Kontakt (nur für eingeloggte User) -->
                <a href="{{ route('contact.index') }}" 
                   class="block px-3 py-2 text-base font-medium text-white hover:text-yellow-400 hover:bg-blue-800 rounded-md transition-colors duration-200 flex items-center space-x-2 {{ request()->routeIs('contact.*') ? 'text-yellow-400 bg-blue-800' : '' }}">
                    <span class="text-lg">📬</span>
                    <span>Kontakt & Feedback</span>
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
                                $unreadCount = \App\Models\ContactMessage::where('is_read', false)->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </div>
                @endif
            @endauth
            @guest
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
