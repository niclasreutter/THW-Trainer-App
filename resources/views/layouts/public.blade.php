@php
    use App\Helpers\DomainHelper;

    $home = DomainHelper::landingHomeUrl();
    $isTestHost = app()->environment('testing') || str_contains(request()->getHost(), 'test.') || config('app.environment_type') === 'testing';
    // Inline-@section-Inhalte sind bereits escaped; dekodieren, sonst doppelt escaped im Head
    $title = html_entity_decode(trim($__env->yieldContent('title')), ENT_QUOTES) ?: 'THW-Trainer – THW-Theorie kostenlos lernen und die Grundausbildung bestehen';
    $description = html_entity_decode(trim($__env->yieldContent('description')), ENT_QUOTES) ?: 'Alle Prüfungsfragen der THW-Grundausbildung kostenlos online lernen: Spaced Repetition, Prüfungssimulation und Lernfortschritt. Werbefrei, als App installierbar.';
    $canonical = trim($__env->yieldContent('canonical')) ?: url()->current();
    $robots = trim($__env->yieldContent('robots')) ?: 'index, follow';
    $logo = asset('logo-thw-trainer.png').'?v='.filemtime(public_path('logo-thw-trainer.png'));
    $nav = [
        ['route' => 'landing.thw-theorie', 'label' => 'Lernabschnitte'],
        ['route' => 'landing.thw-theoriepruefung', 'label' => 'Prüfung'],
        ['route' => 'landing.thw-pruefungsfragen', 'label' => 'Fragen'],
        ['route' => 'landing.statistics', 'label' => 'Statistik'],
        ['route' => 'landing.wiki.index', 'label' => 'Hilfe', 'active' => 'landing.wiki.*'],
    ];
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="author" content="Niclas Reutter">
    @if($isTestHost)
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    @else
        <meta name="robots" content="{{ $robots }}">
    @endif
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="alternate" hreflang="de" href="{{ $canonical }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="THW-Trainer">
    <meta property="og:locale" content="de_DE">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ $logo }}">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $logo }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v={{ filemtime(public_path('apple-touch-icon.png')) }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#00337F">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="format-detection" content="telephone=no">
    <meta name="application-name" content="THW-Trainer">

    <script type="application/ld+json">{!! json_encode([
        '@@context' => 'https://schema.org',
        '@graph' => [
            ['@type' => 'Organization', '@id' => DomainHelper::landingUrl('/').'#organization', 'name' => 'THW-Trainer', 'url' => DomainHelper::landingUrl('/'), 'logo' => $logo, 'areaServed' => ['@type' => 'Country', 'name' => 'Deutschland']],
            ['@type' => 'WebSite', 'name' => 'THW-Trainer', 'alternateName' => ['THW Trainer', 'THW-Trainer.de'], 'url' => DomainHelper::landingUrl('/'), 'inLanguage' => 'de', 'publisher' => ['@id' => DomainHelper::landingUrl('/').'#organization']],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @stack('head')

    @vite(['resources/css/public.css', 'resources/js/app.js', 'resources/js/public.js'])
    <script>document.documentElement.classList.add('js');</script>
</head>
<body @class(['has-local' => $__env->hasSection('localnav')])>
<a class="d-skip" href="#inhalt">Zum Inhalt springen</a>

<header class="d-nav" x-data="{ open: false }" @keydown.escape.window="open = false; document.body.style.overflow = ''">
    <div class="d-wide">
        <a class="d-logo" href="{{ $home }}"><img class="on-light" src="{{ $logo }}" alt="" width="22" height="22"><img class="on-dark" src="{{ asset('logo-thw-trainer_w.png') }}?v={{ filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="" width="22" height="22">THW-Trainer</a>
        <nav class="d-links" aria-label="Hauptnavigation">
            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}" @if(request()->routeIs($item['active'] ?? $item['route'])) aria-current="page" @endif>{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <a class="d-nav-login" href="{{ route('login') }}">Anmelden</a>
        <a class="d-pill d-pill--sm" href="{{ route('register') }}">Starten</a>
        <button class="d-menu" type="button" @click="open = !open; document.body.style.overflow = open ? 'hidden' : ''" :aria-expanded="open.toString()" aria-controls="d-overlay" aria-label="Menü"><span></span></button>
    </div>
    <nav class="d-overlay" id="d-overlay" x-show="open" style="display:none" aria-label="Mobile Navigation"
         x-transition:enter="d-t-enter" x-transition:enter-start="d-t-from" x-transition:leave="d-t-leave" x-transition:leave-end="d-t-from">
        @foreach($nav as $item)<a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>@endforeach
        <a href="{{ route('landing.guest.practice.menu') }}">Ohne Konto üben</a>
        <a href="{{ route('login') }}">Anmelden</a>
        <a class="d-pill" href="{{ route('register') }}">Kostenlos starten</a>
    </nav>
</header>
@yield('localnav')

<main id="inhalt">
    @yield('content')
</main>

<footer class="d-footer">
    <div class="d-wide">
        <p class="note">Der THW-Trainer ist eine private, ehrenamtliche Initiative zur Prüfungsvorbereitung und kein offizielles Angebot der Bundesanstalt Technisches Hilfswerk.</p>
        <div class="cols">
            <div><h2>Themen</h2><ul>
                <li><a href="{{ route('landing.thw-theorie') }}">THW Theorie</a></li>
                <li><a href="{{ route('landing.thw-grundausbildung') }}">THW Grundausbildung</a></li>
                <li><a href="{{ route('landing.thw-pruefungsfragen') }}">THW Prüfungsfragen</a></li>
                <li><a href="{{ route('landing.thw-theoriepruefung') }}">THW Theorieprüfung</a></li>
            </ul></div>
            <div><h2>Lernen</h2><ul>
                <li><a href="{{ route('landing.guest.practice.menu') }}">Ohne Konto üben</a></li>
                <li><a href="{{ route('register') }}">Konto erstellen</a></li>
                <li><a href="{{ route('login') }}">Anmelden</a></li>
            </ul></div>
            <div><h2>Über</h2><ul>
                <li><a href="{{ route('landing.statistics') }}">Statistik</a></li>
                <li><a href="{{ route('landing.wiki.index') }}">Hilfe & Wiki</a></li>
                <li><a href="{{ route('landing.wiki.show', 'changelog') }}">Changelog</a></li>
            </ul></div>
            <div><h2>Rechtliches</h2><ul>
                <li><a href="{{ route('landing.impressum') }}">Impressum</a></li>
                <li><a href="{{ route('landing.datenschutz') }}">Datenschutz</a></li>
                <li><a href="https://bero-host.de/spenden/ks14llyclh8q" rel="noopener noreferrer" target="_blank">Entwicklung unterstützen</a></li>
            </ul></div>
        </div>
        <div class="legal"><span>© {{ date('Y') }} THW-Trainer · Entwickelt von Niclas Reutter · Kostenlos und werbefrei</span><x-version-badge /></div>
    </div>
</footer>

@include('components.cookie-banner')

<script>
    if ('serviceWorker' in navigator) {
        addEventListener('load', () => navigator.serviceWorker.register('/sw.js').catch(() => {}));
    }
</script>
@stack('scripts')
</body>
</html>
