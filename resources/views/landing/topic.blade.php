@extends('layouts.public')

@section('title', $topic['title'])
@section('description', $topic['description'])
@section('canonical', route('landing.'.$slug))

@php
    $kind = $topic['kind'];
    $labels = ['thw-theorie' => 'Die 10 Lernabschnitte', 'thw-grundausbildung' => 'THW Grundausbildung', 'thw-pruefungsfragen' => 'THW Prüfungsfragen', 'thw-theoriepruefung' => 'THW Theorieprüfung'];
    $relatedText = ['thw-theorie' => "Alle {$totalQuestions} Fragen nach Lernabschnitten", 'thw-grundausbildung' => 'Ablauf vom Eintritt bis zur Prüfung', 'thw-pruefungsfragen' => 'Prüfungsfragen mit Lösung üben', 'thw-theoriepruefung' => 'Ablauf, Bestehensgrenze, Simulation'];
    $ctaUrl = route($topic['cta']['route']);
    $home = \App\Helpers\DomainHelper::landingHomeUrl();
@endphp

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn ($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]], $topic['faq']),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => \App\Helpers\DomainHelper::landingUrl('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => $topic['crumb'], 'item' => route('landing.'.$slug)],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@if(in_array($kind, ['catalog', 'overview']))
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'Course',
    'name' => 'THW Grundausbildung Theorie',
    'description' => $topic['description'],
    'provider' => ['@type' => 'Organization', 'name' => 'THW-Trainer', 'url' => \App\Helpers\DomainHelper::landingUrl('/')],
    'isAccessibleForFree' => true,
    'inLanguage' => 'de',
    'hasCourseInstance' => ['@type' => 'CourseInstance', 'courseMode' => 'online', 'courseWorkload' => 'PT10H'],
    'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'EUR', 'category' => 'Free'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif
@endpush

@section('localnav')
<nav class="d-local" aria-label="{{ $topic['crumb'] }}">
    <div class="d-wide">
        <b>{{ $topic['crumb'] }}</b>
        <a href="#ueberblick">Überblick</a>
        <a href="#details">Details</a>
        <a href="#faq">Fragen</a>
        <a class="d-pill d-pill--sm" href="{{ $ctaUrl }}">{{ $topic['cta']['label'] }}</a>
    </div>
</nav>
@endsection

@section('content')
<section class="d-band d-pagehero" id="ueberblick">
@include('landing.partials.gear')
<div class="d-wrap">
    <nav aria-label="Brotkrumen"><ol class="d-crumbs d-in"><li><a href="{{ $home }}">Startseite</a></li><li aria-current="page">{{ $topic['crumb'] }}</li></ol></nav>
    <h1 class="d-h1 d-in" style="--i:1;font-size:clamp(2.75rem,6.6vw,5.25rem)"><span class="d-eyebrow">{{ $topic['eyebrow'] }}</span> {{ $topic['h1'] }}.<br> <span style="color:var(--text-3)">{{ $topic['h1Accent'] }}.</span></h1>
    <p class="d-sub d-in" style="--i:2;max-width:40ch">{{ $topic['lead'] }}</p>
    <div class="d-ctas d-in" style="--i:3">
        <a class="d-pill" href="{{ $ctaUrl }}">{{ $topic['cta']['label'] }}</a>
        @if($topic['cta']['route'] === 'register')<a class="d-more" href="{{ route('landing.guest.practice.menu') }}">Ohne Konto üben</a>@else<a class="d-more" href="{{ route('register') }}">Kostenloses Konto</a>@endif
    </div>
</div>
</section>

@if($kind === 'catalog' || $kind === 'overview')
@php
    $beats = $kind === 'catalog'
        ? [
            [$totalQuestions.' Fragen.', 'Der komplette Fragenkatalog der Grundausbildung, Version '.$catalog['version'].'. Jeder Punkt steht für rund '.max(1, round($totalQuestions / 40)).' Fragen.'],
            ['Zehn Lernabschnitte, ungleich groß.', 'Jeder Lernabschnitt hat einen anderen Umfang. Plane deine Lernzeit nach dem Anteil am Katalog.'],
            ['Gemeistert nach drei richtigen Antworten.', 'Jede Frage durchläuft drei Stufen. Erst nach drei richtigen Antworten in Folge gilt sie als gemeistert – ein Fehler setzt die Serie zurück.'],
        ]
        : array_map(fn ($s) => [$s['t'].'.', $s['d']], $topic['steps']);
    $heads = $kind === 'catalog'
        ? [['Fragen im Katalog', $totalQuestions, ''], ['Lernabschnitte', '10', 'unterschiedlich groß'], ['Richtig in Folge', '3×', 'dann gemeistert']]
        : [['Schritt 1 von 4', 'Ortsverband', ''], ['Schritt 2 von 4', 'Dienstabende', ''], ['Schritt 3 von 4', 'Theorie', round($totalQuestions * 0.75).' von '.$totalQuestions], ['Schritt 4 von 4', 'Prüfung', 'bestanden']];
@endphp
<section class="d-wide" style="padding-top:clamp(48px,7vw,88px)" aria-label="{{ $kind === 'catalog' ? 'Der Fragenkatalog in drei Bildern' : 'Der Weg durch die Grundausbildung' }}">
    <div class="d-story" data-morph="{{ $kind }}" data-counts="{{ json_encode(array_column($sections, 'count')) }}">
        <div class="d-morph" aria-hidden="true">
            <div class="d-mheads">
                @foreach($heads as $k => [$label, $value, $small])
                    <div @class(['d-mh', 'is-on' => $k === 0])><span>{{ $label }}</span><b>{{ $value }}@if($small) <small>{{ $small }}</small>@endif</b></div>
                @endforeach
            </div>
            <div class="d-field" data-s="0">
                @if($kind === 'catalog')
                    <span class="d-mtag dim" data-k="legend">1 Punkt ≈ {{ max(1, round($totalQuestions / 40)) }} Fragen</span>
                    @foreach($sections as $sec)<span class="d-mtag dim" data-k="n{{ $sec['nr'] }}">{{ $sec['nr'] }}</span>@endforeach
                    <span class="d-mtag dim" data-k="s1">1× richtig</span>
                    <span class="d-mtag dim" data-k="s2">2× richtig</span>
                    <span class="d-mtag" data-k="s3">gemeistert</span>
                @else
                    <span class="d-mtag" data-k="you">du</span>
                    <span class="d-mtag dim" data-k="ov">dein Ortsverband</span>
                    @foreach($sections as $sec)<span class="d-mtag dim" data-k="n{{ $sec['nr'] }}">{{ $sec['nr'] }}</span>@endforeach
                    <span class="d-mtag dim" data-k="bar">75 % gemeistert</span>
                    <span class="d-mtag" data-k="pass">bestanden</span>
                @endif
                @for($n = 0; $n < 40; $n++)<i class="d-dot" style="--i:{{ $n }}"></i>@endfor
            </div>
        </div>
        <div class="d-beats">
            @foreach($beats as $k => [$t, $d])
            <article @class(['d-beat', 'is-on' => $k === 0]) data-s="{{ $k }}">
                <p class="d-step-n">0{{ $k + 1 }}</p>
                <h2 class="d-h2" style="font-size:clamp(2rem,4vw,3rem)">{{ $t }}</h2>
                <p class="d-copy" style="margin-top:16px">{{ $d }}</p>
            </article>
            @endforeach
        </div>
    </div>
</section>
@elseif($kind === 'exam')
<section class="d-sim" data-pass="{{ $exam['passCount'] }}" data-minutes="{{ $exam['minutes'] }}" style="margin-top:clamp(48px,7vw,88px)" aria-label="Prüfungssimulation im Zeitraffer">
    <div class="d-sim-pin">
        <div class="d-wide d-sim-grid">
            <div class="d-scaps" aria-live="polite">
                @foreach([
                    [$exam['questions'].' Fragen, zufällig aus '.$totalQuestions.'.', 'Jede Simulation stellt andere Fragen – bevorzugt solche, die du länger nicht gesehen hast.'],
                    [$exam['minutes'].' Minuten Zeit.', 'Rund 45 Sekunden pro Frage. Die Uhr läuft sichtbar mit.'],
                    ['Ab '.$exam['passCount'].' richtigen bestanden.', $exam['percent'].' Prozent, keine Teilpunkte. Ab hier ist die Prüfung geschafft.'],
                    ['Bestanden.', 'Falsche Antworten landen automatisch in deiner Wiederholung.'],
                ] as $k => [$t, $d])
                    <p @class(['d-scap', 'is-on' => $k === 0])><span class="step">0{{ $k + 1 }} / 04</span><b>{{ $t }}</b><span class="sub">{{ $d }}</span></p>
                @endforeach
            </div>
            <div class="d-card d-sheet" aria-hidden="true">
                <div class="d-sheet-head"><span>Prüfungssimulation</span><b class="d-sim-time">{{ $exam['minutes'] }}:00</b></div>
                <div class="d-sheet-grid">@for($n = 0; $n < $exam['questions']; $n++)<i></i>@endfor</div>
                <div class="d-meter"><i></i><s style="left:{{ $exam['percent'] }}%"></s></div>
                <div class="d-sheet-foot"><span><b class="d-sim-right">0</b> richtig · Frage <b class="d-sim-n">1</b> von {{ $exam['questions'] }}</span><span>Grenze {{ $exam['passCount'] }}</span></div>
                <div class="d-result"><p><span>Bestanden mit 36 von {{ $exam['questions'] }}. Vier Fragen kommen in die Wiederholung.</span></p></div>
            </div>
        </div>
    </div>
</section>
@elseif(count($questions))
<section class="d-wide" style="padding-top:clamp(56px,8vw,96px)" aria-label="Prüfungsfragen zum Ausprobieren">
    <div class="d-stage d-grow" style="padding:clamp(32px,7vw,96px) clamp(16px,4vw,48px);display:grid;justify-items:center">
        <div style="width:100%;max-width:620px">@include('landing.partials.question-card', ['items' => $questions])</div>
    </div>
</section>
@endif

<section class="d-wrap d-section" id="details">
    @if(in_array($kind, ['catalog', 'overview']))
        <p class="d-eyebrow d-reveal">Die 10 Lernabschnitte</p>
        <h2 class="d-h2 d-reveal" style="margin-bottom:48px">Was du lernst.</h2>
        <ol class="d-list d-reveal">
            @foreach($sections as $s)
                <li id="la-{{ $s['nr'] }}"><a href="{{ route('landing.thw-theorie.abschnitt', $s['slug']) }}"><span class="n">{{ $s['nr'] }}</span><span class="t">{{ $s['name'] }}</span><span class="c">{{ $s['count'] }} Fragen</span></a></li>
            @endforeach
        </ol>
    @elseif($kind === 'exam')
        <p class="d-eyebrow d-reveal">Am Prüfungstag</p>
        <h2 class="d-h2 d-reveal" style="margin-bottom:48px">Vier Dinge, die helfen.</h2>
        <ol class="d-timeline d-reveal">
            @foreach($topic['tips'] as $k => $tip)
                <li><p class="d-small" style="margin:0 0 8px">0{{ $k + 1 }}</p><h3 class="d-h3">{{ $tip['t'] }}</h3><p class="d-copy" style="font-size:16px">{{ $tip['d'] }}</p></li>
            @endforeach
        </ol>
    @else
        <p class="d-eyebrow d-reveal">Aufbau der Prüfungsfragen</p>
        <h2 class="d-h2 d-reveal" style="margin-bottom:48px">Drei Antworten.<br>Alles oder nichts.</h2>
        <ol class="d-timeline d-reveal" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
            <li><h3 class="d-h3">Drei Optionen</h3><p class="d-copy" style="font-size:16px">Jede Frage hat genau die Antworten A, B und C.</p></li>
            <li><h3 class="d-h3">Eine bis drei richtig</h3><p class="d-copy" style="font-size:16px">Lies immer alle Antworten, bevor du dich festlegst.</p></li>
            <li><h3 class="d-h3">Keine Teilpunkte</h3><p class="d-copy" style="font-size:16px">Gezählt wird nur die vollständig richtige Auswahl.</p></li>
        </ol>
        @if(count($questions))
        <h2 class="d-h2 d-reveal" style="margin:clamp(72px,10vw,120px) 0 40px">Beispielfragen<br>mit Lösung.</h2>
        <div class="d-faq d-samples d-reveal">
            @foreach($questions as $q)
            <details>
                <summary><span><small>Lernabschnitt {{ $q['section'] }}</small>{{ $q['text'] }}</span></summary>
                <ol type="A">@foreach($q['answers'] as $n => $a)<li @class(['ok' => in_array($n, $q['correct'])])>{{ $a }}</li>@endforeach</ol>
                <p><b>Richtig: {{ collect($q['correct'])->map(fn ($c) => 'ABC'[$c])->join(' und ') }}.</b> {{ $q['explain'] }}</p>
                @isset($sections[$q['section'] - 1])<p><a class="d-more" href="{{ route('landing.thw-theorie.abschnitt', $sections[$q['section'] - 1]['slug']) }}">Alle Fragen aus Lernabschnitt {{ $q['section'] }}</a></p>@endisset
            </details>
            @endforeach
        </div>
        @endif
    @endif
</section>

<section class="d-wrap d-section" aria-labelledby="hintergrund">
    <h2 id="hintergrund" class="d-h2 d-reveal" style="margin-bottom:48px">{{ $topic['blocksTitle'] }}</h2>
    <div class="d-blocks">
        @foreach($topic['blocks'] as $k => $block)
            <article class="d-reveal" style="--i:{{ $k }}"><h3 class="d-h3" style="font-size:clamp(1.375rem,2.2vw,1.75rem)">{{ $block['h'] }}</h3><p class="d-copy" style="margin-top:12px">{{ $block['p'] }}</p></article>
        @endforeach
    </div>
</section>

<section class="d-wrap d-section" id="faq" aria-labelledby="faq-h">
    <h2 id="faq-h" class="d-h2 d-reveal" style="margin-bottom:40px">Häufig gestellte Fragen.</h2>
    <div class="d-faq d-reveal">@foreach($topic['faq'] as $f)<details><summary>{{ $f['q'] }}</summary><p>{{ $f['a'] }}</p></details>@endforeach</div>
</section>

<section class="d-wrap d-section" aria-labelledby="weiter">
    <h2 id="weiter" class="d-eyebrow d-reveal">Weiterlesen</h2>
    <nav class="d-related d-reveal" aria-label="Verwandte Themen">
        @foreach($topic['related'] as $rel)<a href="{{ route('landing.'.$rel) }}"><b>{{ $labels[$rel] }}</b><span class="d-small" style="font-size:17px">{{ $relatedText[$rel] }}</span></a>@endforeach
    </nav>
</section>

<section class="d-closing d-band" data-nav-dark>
    <div class="d-wrap">
        <h2 class="d-h1 d-reveal" style="font-size:clamp(2.5rem,5.6vw,4.5rem)">Bereit für die<br>Theorieprüfung?</h2>
        <p class="d-sub d-reveal">Kostenlos, werbefrei und auf allen Geräten.</p>
        <div class="d-ctas d-reveal" style="--i:1">
            <a class="d-pill" href="{{ $ctaUrl }}">{{ $topic['cta']['label'] }}</a>
            @if($topic['cta']['route'] === 'register')<a class="d-more" href="{{ route('landing.guest.practice.menu') }}">Ohne Konto üben</a>@else<a class="d-more" href="{{ route('register') }}">Kostenloses Konto</a>@endif
        </div>
    </div>
</section>
@endsection
