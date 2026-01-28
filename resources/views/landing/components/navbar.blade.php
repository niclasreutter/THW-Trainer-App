{{-- Landing Page Navbar - Light Mode --}}
<nav class="landing-navbar">
    <div class="landing-navbar-container">
        {{-- Logo --}}
        <a href="{{ route('landing.home') }}" class="landing-navbar-brand">
            <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" class="h-8 w-auto">
            <span class="font-bold text-xl text-thw-blue">THW-Trainer</span>
        </a>

        {{-- Desktop Navigation --}}
        <div class="landing-navbar-links hidden md:flex">
            <a href="{{ route('landing.home') }}#features" class="landing-nav-link">Features</a>
            <a href="{{ route('landing.home') }}#faq" class="landing-nav-link">FAQ</a>
            <a href="{{ route('landing.statistics') }}" class="landing-nav-link">Statistiken</a>
            <a href="{{ route('landing.contact.index') }}" class="landing-nav-link">Kontakt</a>
        </div>

        {{-- Auth Buttons (Desktop) --}}
        <div class="landing-navbar-auth hidden md:flex">
            @php
                $loginUrl = config('domains.development')
                    ? route('login')
                    : 'https://' . config('domains.app') . '/login';
                $registerUrl = config('domains.development')
                    ? route('register')
                    : 'https://' . config('domains.app') . '/register';
            @endphp
            <a href="{{ $loginUrl }}" class="landing-btn-ghost">Anmelden</a>
            <a href="{{ $registerUrl }}" class="landing-btn-primary">Registrieren</a>
        </div>

        {{-- Mobile Menu Button --}}
        <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="landing-mobile-menu-btn md:hidden"
            aria-label="Menü öffnen"
        >
            <i class="bi" :class="mobileMenuOpen ? 'bi-x-lg' : 'bi-list'"></i>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="landing-mobile-menu md:hidden"
        style="display: none;"
    >
        <div class="landing-mobile-menu-links">
            <a href="{{ route('landing.home') }}#features" class="landing-mobile-link" @click="mobileMenuOpen = false">Features</a>
            <a href="{{ route('landing.home') }}#faq" class="landing-mobile-link" @click="mobileMenuOpen = false">FAQ</a>
            <a href="{{ route('landing.statistics') }}" class="landing-mobile-link" @click="mobileMenuOpen = false">Statistiken</a>
            <a href="{{ route('landing.contact.index') }}" class="landing-mobile-link" @click="mobileMenuOpen = false">Kontakt</a>
        </div>
        <div class="landing-mobile-menu-auth">
            @php
                $loginUrl = config('domains.development')
                    ? route('login')
                    : 'https://' . config('domains.app') . '/login';
                $registerUrl = config('domains.development')
                    ? route('register')
                    : 'https://' . config('domains.app') . '/register';
            @endphp
            <a href="{{ $loginUrl }}" class="landing-btn-ghost w-full text-center">Anmelden</a>
            <a href="{{ $registerUrl }}" class="landing-btn-primary w-full text-center">Registrieren</a>
        </div>
    </div>
</nav>
