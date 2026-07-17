@extends('layouts.startseite')

@section('title', $page['meta']['title'] . ' – THW-Trainer Wiki')
@section('description', $page['meta']['description'])
@section('canonical', $page['meta']['url'])

@section('content')
@php
    $loginUrl = config('domains.development') ? route('login') : 'https://' . config('domains.app') . '/login';
    $registerUrl = config('domains.development') ? route('register') : 'https://' . config('domains.app') . '/register';
    // Lokal überschreibt web.php die '/'-Route, daher existiert landing.home dort nicht
    $homeUrl = \Illuminate\Support\Facades\Route::has('landing.home') ? route('landing.home') : url('/home');
@endphp

{{-- Schema.org Breadcrumb --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "Wiki", "item": "{{ route('landing.wiki.index') }}" },
        { "@@type": "ListItem", "position": 2, "name": "{{ $page['meta']['section'] }}" },
        { "@@type": "ListItem", "position": 3, "name": "{{ $page['meta']['title'] }}", "item": "{{ $page['meta']['url'] }}" }
    ]
}
</script>

<div x-data="{ sidebarOpen: false }">

    {{-- ============ Topbar ============ --}}
    <header class="wiki-topbar">
        <div class="wiki-topbar-inner">
            <button class="wiki-burger lg:hidden" @click="sidebarOpen = true" aria-label="Navigation öffnen">
                <i class="bi bi-list"></i>
            </button>

            <a href="{{ route('landing.wiki.index') }}" class="wiki-brand">
                <img src="{{ asset('logo-thw-trainer_w.png') . '?v=' . filemtime(public_path('logo-thw-trainer_w.png')) }}" alt="THW-Trainer Logo" class="h-7 w-auto sp-logo-dark">
                <img src="{{ asset('logo-thw-trainer.png') . '?v=' . filemtime(public_path('logo-thw-trainer.png')) }}" alt="THW-Trainer Logo" class="h-7 w-auto sp-logo-light">
                <span class="wiki-brand-name">THW-Trainer</span>
                <span class="wiki-brand-badge">Wiki</span>
            </a>

            <nav class="wiki-topbar-links hidden md:flex" aria-label="Wiki-Navigation">
                <a href="{{ $homeUrl }}">Startseite</a>
                <a href="{{ route('landing.wiki.show', 'changelog') }}">Changelog</a>
            </nav>

            <div class="wiki-topbar-auth">
                <a href="{{ $loginUrl }}" class="wiki-topbar-login hidden sm:inline-flex">Anmelden</a>
                <a href="{{ $registerUrl }}" class="wiki-topbar-cta">Registrieren</a>
            </div>
        </div>
    </header>

    {{-- ============ Shell: Sidebar | Artikel | TOC ============ --}}
    <div class="wiki-shell">

        {{-- Mobile Overlay --}}
        <div class="wiki-overlay lg:hidden"
             x-show="sidebarOpen"
             x-transition.opacity
             @click="sidebarOpen = false"
             style="display: none;"></div>

        {{-- Sidebar Navigation --}}
        <aside class="wiki-sidebar" :class="{ 'open': sidebarOpen }" aria-label="Wiki-Seiten">
            <div class="wiki-sidebar-head lg:hidden">
                <span class="wiki-brand-badge">Wiki</span>
                <button @click="sidebarOpen = false" aria-label="Navigation schließen"><i class="bi bi-x-lg"></i></button>
            </div>

            @foreach ($navigation as $section)
                <div class="wiki-nav-section">
                    <div class="wiki-nav-title">
                        <i class="bi {{ $section['icon'] }}"></i>
                        <span>{{ $section['title'] }}</span>
                    </div>
                    <ul>
                        @foreach ($section['pages'] as $navPage)
                            <li>
                                <a href="{{ $navPage['url'] }}"
                                   @class(['wiki-nav-link', 'active' => $navPage['slug'] === $activeSlug])
                                   @if($navPage['slug'] === $activeSlug) aria-current="page" @endif>
                                    {{ $navPage['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </aside>

        {{-- Hauptinhalt --}}
        <main class="wiki-main">
            <nav class="wiki-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('landing.wiki.index') }}">Wiki</a>
                <i class="bi bi-chevron-right"></i>
                <span>{{ $page['meta']['section'] }}</span>
                <i class="bi bi-chevron-right"></i>
                <span class="wiki-breadcrumb-current">{{ $page['meta']['title'] }}</span>
            </nav>

            {{-- Mobiles Inhaltsverzeichnis --}}
            @if (count($page['toc']) > 1)
                <details class="wiki-toc-mobile xl:hidden">
                    <summary><i class="bi bi-list-ul"></i> In diesem Artikel</summary>
                    <ul>
                        @foreach ($page['toc'] as $entry)
                            <li @class(['wiki-toc-l3' => $entry['level'] === 3])>
                                <a href="#{{ $entry['id'] }}">{{ $entry['text'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif

            <article class="wiki-article" id="wiki-article">
                {!! $page['html'] !!}
            </article>

            {{-- Vor / Zurück --}}
            @if ($page['prev'] || $page['next'])
                <nav class="wiki-pager" aria-label="Seiten-Navigation">
                    @if ($page['prev'])
                        <a href="{{ $page['prev']['url'] }}" class="wiki-pager-link">
                            <span class="wiki-pager-label"><i class="bi bi-arrow-left"></i> Zurück</span>
                            <span class="wiki-pager-title">{{ $page['prev']['title'] }}</span>
                        </a>
                    @else
                        <span></span>
                    @endif
                    @if ($page['next'])
                        <a href="{{ $page['next']['url'] }}" class="wiki-pager-link wiki-pager-next">
                            <span class="wiki-pager-label">Weiter <i class="bi bi-arrow-right"></i></span>
                            <span class="wiki-pager-title">{{ $page['next']['title'] }}</span>
                        </a>
                    @endif
                </nav>
            @endif

            <p class="wiki-updated">Stand: {{ $page['updated_at']->format('d.m.Y') }}</p>
        </main>

        {{-- Rechte Spalte: In diesem Artikel --}}
        <aside class="wiki-toc hidden xl:block" aria-label="In diesem Artikel">
            @if (count($page['toc']) > 0)
                <div class="wiki-toc-sticky">
                    <p class="wiki-toc-heading">In diesem Artikel</p>
                    <ul id="wiki-toc-list">
                        @foreach ($page['toc'] as $entry)
                            <li @class(['wiki-toc-l3' => $entry['level'] === 3])>
                                <a href="#{{ $entry['id'] }}" data-id="{{ $entry['id'] }}">{{ $entry['text'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </div>

    {{-- ============ Footer ============ --}}
    <footer class="wiki-footer">
        <div class="wiki-footer-inner">
            <div class="wiki-footer-brand">
                <span class="font-semibold text-white">THW-Trainer <x-version-badge /></span>
                <span>Kostenlose Prüfungsvorbereitung für die THW-Grundausbildung</span>
            </div>
            <nav class="wiki-footer-links" aria-label="Footer">
                <a href="{{ $homeUrl }}">Startseite</a>
                <a href="{{ route('landing.statistics') }}">Statistiken</a>
                <a href="{{ route('landing.wiki.show', 'changelog') }}">Changelog</a>
                <a href="{{ route('landing.impressum') }}">Impressum</a>
                <a href="{{ route('landing.datenschutz') }}">Datenschutz</a>
            </nav>
        </div>
    </footer>

    {{-- Toast für kopierte Anker-Links --}}
    <div id="wiki-copy-toast" class="wiki-copy-toast" role="status">Link kopiert</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Anker-Link klicken = Deep-Link in Zwischenablage kopieren ---
    const toast = document.getElementById('wiki-copy-toast');
    let toastTimer = null;

    document.querySelectorAll('.wiki-article .wiki-anchor').forEach((anchor) => {
        anchor.addEventListener('click', () => {
            const url = window.location.origin + window.location.pathname + anchor.getAttribute('href');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    toast.classList.add('visible');
                    clearTimeout(toastTimer);
                    toastTimer = setTimeout(() => toast.classList.remove('visible'), 1800);
                }).catch(() => {});
            }
        });
    });

    // --- Scroll-Spy: aktuellen Abschnitt im Inhaltsverzeichnis markieren ---
    const tocLinks = document.querySelectorAll('#wiki-toc-list a[data-id]');
    if (tocLinks.length === 0) return;

    const headings = [...tocLinks]
        .map((link) => document.getElementById(link.dataset.id))
        .filter(Boolean);

    const setActive = (id) => {
        tocLinks.forEach((link) => link.classList.toggle('active', link.dataset.id === id));
    };

    const observer = new IntersectionObserver((entries) => {
        // Oberstes sichtbares Heading gewinnt
        const visible = entries
            .filter((e) => e.isIntersecting)
            .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
        if (visible.length > 0) {
            setActive(visible[0].target.id);
        }
    }, { rootMargin: '-80px 0px -70% 0px', threshold: 0 });

    headings.forEach((h) => observer.observe(h));

    // Initial: Heading aus URL-Hash aktivieren
    if (window.location.hash) {
        setActive(window.location.hash.slice(1));
    } else if (headings.length > 0) {
        setActive(headings[0].id);
    }
});
</script>
@endpush
