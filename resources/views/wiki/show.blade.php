@extends('layouts.public')

@section('title', $page['meta']['title'] . ' – THW-Trainer Wiki')
@section('description', $page['meta']['description'])
@section('canonical', $page['meta']['url'])

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array_values(array_filter([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => \App\Helpers\DomainHelper::landingUrl('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Wiki', 'item' => route('landing.wiki.index')],
        $activeSlug !== 'index' ? ['@type' => 'ListItem', 'position' => 3, 'name' => $page['meta']['title'], 'item' => $page['meta']['url']] : null,
    ])),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
<div class="d-wide">
    <div class="d-docs d-docs--toc">
        <nav class="d-dnav" aria-label="Wiki-Navigation">
            <details open x-data x-init="if (innerWidth < 860) $el.removeAttribute('open')">
                <summary>Alle Hilfeseiten</summary>
                @foreach($navigation as $section)
                    <h2>{{ $section['title'] }}</h2>
                    <ul>@foreach($section['pages'] as $p)<li><a href="{{ $p['url'] }}" @if($p['slug'] === $activeSlug) aria-current="page" @endif>{{ $p['title'] }}</a></li>@endforeach</ul>
                @endforeach
            </details>
        </nav>
        <article class="d-prose d-in">
            <nav aria-label="Brotkrumen"><ol class="d-crumbs"><li><a href="{{ \App\Helpers\DomainHelper::landingHomeUrl() }}">Startseite</a></li><li>@if($activeSlug === 'index')<span aria-current="page">Wiki</span>@else<a href="{{ route('landing.wiki.index') }}">Wiki</a>@endif</li>@if($activeSlug !== 'index')<li aria-current="page">{{ $page['meta']['title'] }}</li>@endif</ol></nav>
            {!! $page['html'] !!}
            <p class="d-small" style="margin-top:40px">Stand: {{ $page['updated_at']->format('d.m.Y') }}</p>
            @if($page['prev'] || $page['next'])
            <nav class="d-pager" aria-label="Seiten-Navigation">
                <div>@if($page['prev'])<a href="{{ $page['prev']['url'] }}"><span class="d-small">Zurück</span><b>{{ $page['prev']['title'] }}</b></a>@endif</div>
                <div style="text-align:right">@if($page['next'])<a href="{{ $page['next']['url'] }}"><span class="d-small">Weiter</span><b>{{ $page['next']['title'] }}</b></a>@endif</div>
            </nav>
            @endif
        </article>
        @if(count($page['toc']) > 1)
        <aside class="d-dtoc" aria-label="In diesem Artikel">
            <p>In diesem Artikel</p>
            <ul>@foreach($page['toc'] as $entry)<li @class(['l3' => $entry['level'] === 3])><a href="#{{ $entry['id'] }}" data-id="{{ $entry['id'] }}">{{ $entry['text'] }}</a></li>@endforeach</ul>
        </aside>
        @endif
    </div>
</div>
<div id="wiki-copy-toast" class="d-toast" role="status">Link kopiert</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Anker-Link klicken = Deep-Link in Zwischenablage kopieren
    const toast = document.getElementById('wiki-copy-toast');
    let toastTimer;
    document.querySelectorAll('.d-prose .wiki-anchor').forEach((anchor) => {
        anchor.addEventListener('click', () => {
            navigator.clipboard?.writeText(location.origin + location.pathname + anchor.getAttribute('href')).then(() => {
                toast.classList.add('is-on');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toast.classList.remove('is-on'), 1800);
            }).catch(() => {});
        });
    });

    // Scroll-Spy: aktuellen Abschnitt im Inhaltsverzeichnis markieren
    const tocLinks = document.querySelectorAll('.d-dtoc a[data-id]');
    const headings = [...tocLinks].map((link) => document.getElementById(link.dataset.id)).filter(Boolean);
    if (!headings.length) return;
    const setActive = (id) => tocLinks.forEach((link) => link.classList.toggle('is-active', link.dataset.id === id));
    const observer = new IntersectionObserver((entries) => {
        const visible = entries.filter((e) => e.isIntersecting).sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
        if (visible.length) setActive(visible[0].target.id);
    }, { rootMargin: '-80px 0px -70% 0px' });
    headings.forEach((h) => observer.observe(h));
    setActive(location.hash ? location.hash.slice(1) : headings[0].id);
});
</script>
@endpush
