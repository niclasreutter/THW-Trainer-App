@extends('layouts.public')

@php
    $nr = $section['nr'];
    $count = count($questions);
    $share = $totalQuestions > 0 ? (int) round($section['count'] / $totalQuestions * 100) : 0;
    $multi = collect($questions)->filter(fn ($q) => count($q['correct']) > 1)->count();
    $practiceUrl = route('landing.guest.practice.section', $nr);
    $hubUrl = route('landing.thw-theorie');
    $home = \App\Helpers\DomainHelper::landingHomeUrl();
@endphp

@section('title', "THW Lernabschnitt {$nr}: {$section['short']} | Fragen & Lösungen")
@section('description', "Alle {$count} Prüfungsfragen aus Lernabschnitt {$nr} „{$section['name']}“ der THW-Grundausbildung mit Lösung und Erklärung. Fragenkatalog Version {$catalog['version']}.")
@section('canonical', route('landing.thw-theorie.abschnitt', $section['slug']))

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => \App\Helpers\DomainHelper::landingUrl('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Lernabschnitte', 'item' => $hubUrl],
        ['@type' => 'ListItem', 'position' => 3, 'name' => "Lernabschnitt {$nr}: {$section['short']}", 'item' => route('landing.thw-theorie.abschnitt', $section['slug'])],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('localnav')
<nav class="d-local" aria-label="Lernabschnitt {{ $nr }}">
    <div class="d-wide">
        <b>Lernabschnitt {{ $nr }}</b>
        <a href="#ueberblick">Überblick</a>
        <a href="#fragen">Alle Fragen</a>
        <a class="d-pill d-pill--sm" href="{{ $practiceUrl }}">Abschnitt üben</a>
    </div>
</nav>
@endsection

@section('content')
<section class="d-band d-pagehero" id="ueberblick">
@include('landing.partials.gear')
<div class="d-wrap">
    <nav aria-label="Brotkrumen"><ol class="d-crumbs d-in"><li><a href="{{ $home }}">Startseite</a></li><li><a href="{{ $hubUrl }}">Lernabschnitte</a></li><li aria-current="page">Lernabschnitt {{ $nr }}</li></ol></nav>
    <h1 class="d-h1 d-in" style="--i:1;font-size:clamp(2.5rem,5.6vw,4.5rem)"><span class="d-eyebrow">THW Theorie · Lernabschnitt {{ $nr }} von 10</span> {{ $section['name'] }}.</h1>
    <p class="d-sub d-in" style="--i:2;max-width:40ch">{{ $section['intro'] }}</p>
    <div class="d-ctas d-in" style="--i:3">
        <a class="d-pill" href="{{ $practiceUrl }}">Abschnitt üben</a>
        @if($count)<a class="d-more" href="#fragen">Alle {{ $count }} Fragen mit Lösung</a>@endif
    </div>
</div>
</section>

{{-- Größe des Abschnitts: drei echte Kennzahlen, darunter sein Anteil am Katalog --}}
<section class="d-wrap" style="padding-top:clamp(64px,9vw,112px)" aria-label="Lernabschnitt {{ $nr }} in Zahlen">
    <ul class="d-timeline d-reveal" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr))">
        <li><p class="d-big d-num">{{ $count }}</p><p class="d-copy">Prüfungsfragen in diesem Abschnitt</p></li>
        <li><p class="d-big d-num">{{ $share }} %</p><p class="d-copy">des gesamten Fragenkatalogs</p></li>
        <li><p class="d-big d-num">{{ $multi }}</p><p class="d-copy">Fragen mit mehr als einer richtigen Antwort</p></li>
    </ul>
    <div class="d-share" data-anim aria-hidden="true">
        @foreach($sections as $s)<i @class(['is-on' => $s['nr'] === $nr]) style="--n:{{ max(1, $s['count']) }};--i:{{ $loop->index }}"></i>@endforeach
    </div>
    <p class="d-small" style="margin:12px 0 0">Lernabschnitt {{ $nr }} im Fragenkatalog, Version {{ $catalog['version'] }}</p>
</section>

@if($count)
<section class="d-wide" style="padding-top:clamp(56px,8vw,96px)" aria-label="Fragen aus Lernabschnitt {{ $nr }} zum Ausprobieren">
    <div class="d-stage d-grow" style="padding:clamp(32px,7vw,96px) clamp(16px,4vw,48px);display:grid;justify-items:center">
        <div style="width:100%;max-width:620px">@include('landing.partials.question-card', ['items' => collect($questions)->nth(max(1, intdiv($count, 5)))->take(5)->values()->all()])</div>
    </div>
</section>
@endif

<section class="d-wrap d-section" id="fragen" aria-labelledby="fragen-h">
    <h2 id="fragen-h" class="d-h2 d-reveal">Alle {{ $count }} Fragen<br>mit Lösung.</h2>
    <p class="d-copy d-reveal" style="margin:16px 0 48px">Öffne eine Frage, um Lösung und Erklärung zu sehen. Stand: Fragenkatalog Version {{ $catalog['version'] }}, {{ $catalog['date'] }}.</p>
    @if($count)
    <div class="d-faq d-samples d-reveal">
        @foreach($questions as $q)
        <details id="frage-{{ $nr }}-{{ $q['number'] }}">
            <summary><span><small>Frage {{ $nr }}.{{ $q['number'] }}</small>{{ $q['text'] }}</span></summary>
            <ol type="A">@foreach($q['answers'] as $n => $a)<li @class(['ok' => in_array($n, $q['correct'])])>{{ $a }}</li>@endforeach</ol>
            <p><b>Richtig: {{ collect($q['correct'])->map(fn ($c) => 'ABC'[$c])->join(' und ') }}.</b> {{ $q['explain'] }}</p>
        </details>
        @endforeach
    </div>
    @else
    <p class="d-copy">Für diesen Lernabschnitt sind gerade keine Fragen veröffentlicht.</p>
    @endif
</section>

<section class="d-wrap" aria-label="Weitere Lernabschnitte">
    <nav class="d-pager" aria-label="Benachbarte Lernabschnitte" style="border-top:0;padding-top:0">
        <div>@if($prev)<a href="{{ route('landing.thw-theorie.abschnitt', $prev['slug']) }}"><span class="d-small">Lernabschnitt {{ $prev['nr'] }}</span><b>{{ $prev['short'] }}</b></a>@endif</div>
        <div style="text-align:right">@if($next)<a href="{{ route('landing.thw-theorie.abschnitt', $next['slug']) }}"><span class="d-small">Lernabschnitt {{ $next['nr'] }}</span><b>{{ $next['short'] }}</b></a>@endif</div>
    </nav>
    <p style="margin:28px 0 0"><a class="d-more" href="{{ $hubUrl }}">Alle 10 Lernabschnitte</a></p>
</section>

<section class="d-closing d-band" data-nav-dark>
    <div class="d-wrap">
        <h2 class="d-h1 d-reveal" style="font-size:clamp(2.5rem,5.6vw,4.5rem)">Lernabschnitt {{ $nr }}.<br>Sicher gelernt.</h2>
        <p class="d-sub d-reveal">Mit Konto wiederholt der THW-Trainer jede Frage, bis sie sitzt. Kostenlos und werbefrei.</p>
        <div class="d-ctas d-reveal" style="--i:1">
            <a class="d-pill" href="{{ $practiceUrl }}">Abschnitt üben</a>
            <a class="d-more" href="{{ route('register') }}">Kostenloses Konto</a>
        </div>
    </div>
</section>
@endsection
