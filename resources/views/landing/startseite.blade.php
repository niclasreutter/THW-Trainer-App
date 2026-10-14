@extends('layouts.public')

@section('title', 'THW-Trainer — Kostenlose Prüfungsvorbereitung für die Grundausbildung 2026')
@section('description', 'THW-Trainer: kostenlos für die THW-Grundausbildung lernen. Alle Prüfungsfragen online, Prüfungssimulation und Lernfortschritt. Werbefrei & als App.')
@section('canonical', \App\Helpers\DomainHelper::landingUrl('/'))

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn ($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]], $faq),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'WebApplication',
    'name' => 'THW-Trainer',
    'alternateName' => ['THW Training App', 'THW Trainer App', 'THW Prüfungsfragen App'],
    'url' => \App\Helpers\DomainHelper::landingUrl('/'),
    'description' => 'Kostenlose THW Training App mit allen Prüfungsfragen für die Theorieprüfung der Grundausbildung (GA). Prüfungssimulation, Spaced Repetition und Lernfortschritt.',
    'applicationCategory' => 'EducationalApplication',
    'operatingSystem' => 'Web Browser, iOS, Android',
    'inLanguage' => 'de',
    'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'EUR', 'availability' => 'https://schema.org/InStock'],
    'author' => ['@type' => 'Person', 'name' => 'Niclas Reutter'],
    'featureList' => ['THW Grundausbildung Prüfungsfragen', 'THW Theorieprüfung Simulation', 'Spaced Repetition Lernsystem', 'Ortsverband-Lernpools', 'Lernfortschritt Tracking', 'Progressive Web App', 'Kostenlos und werbefrei'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
@php
    $fmt = fn ($n) => number_format($n, 0, ',', '.');
    $check = '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8.5l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2.4"/></svg>';
    $mastered = (int) round($totalQuestions * 0.75);
    $sectionShort = collect($sections)->pluck('short', 'nr');
@endphp

@if (session('status') == 'account-deleted')
    <p class="d-flash" role="status">Dein Account wurde erfolgreich gelöscht. Alle deine Daten wurden permanent entfernt.</p>
@endif

{{-- 1. Vollbild-Hero bis zu den Buttons --}}
<section class="d-hero d-band" data-nav-dark>
    @include('landing.partials.gear')
    <div class="d-wrap d-hero-inner">
        <h1 class="d-h1 d-in" style="--i:1"><span class="d-eyebrow">THW-Theorie lernen · Grundausbildung 2026</span> Die <span style="white-space:nowrap">THW-Theorie</span>.<br> Klar gelernt.</h1>
        <p class="d-sub d-in" style="--i:2">Alle {{ $totalQuestions }} Prüfungsfragen. Wiederholung im richtigen Moment. Eine Simulation wie die echte Prüfung. Kostenlos und werbefrei.</p>
        <div class="d-ctas d-in" style="--i:3">
            <a class="d-pill" href="{{ route('register') }}">Kostenlos starten</a>
            <a class="d-more" href="{{ route('landing.guest.practice.menu') }}">Ohne Konto üben</a>
        </div>
    </div>
    @if(count($demo))<a class="d-cue-in d-in" style="--i:5" href="#frage"><i aria-hidden="true"></i>So funktioniert eine Frage</a>@endif
</section>

{{-- 2. Gepinnte Sequenz mit zwei echten Fragen: erscheinen, auswählen, auflösen, wiederholen, gemeistert --}}
@if(count($demo))
<section id="frage" class="d-quiz" aria-label="So funktioniert eine Frage im THW-Trainer">
    <div class="d-quiz-pin">
        <div class="d-wide d-quiz-grid">
            <div class="d-caps" aria-live="polite">
                @foreach([
                    ['Eine echte Frage aus dem Katalog.', $totalQuestions.' davon gibt es – aus allen zehn Lernabschnitten.'],
                    ['Drei Antworten. Eine oder mehrere sind richtig.', 'Wie in der Prüfung. Teilpunkte gibt es nicht.'],
                    ['Du wählst aus.', 'So viele Antworten, wie du für richtig hältst.'],
                    ['Sofort aufgelöst.', 'Mit Erklärung, damit du verstehst, warum.'],
                    ['Später kommt sie wieder.', 'Spaced Repetition holt jede Frage zurück, kurz bevor du sie vergisst.'],
                    ['Dreimal in Folge richtig: gemeistert.', 'Eine Frage weniger bis zur Prüfung.'],
                ] as $k => [$t, $d])
                    <p @class(['d-cap', 'is-on' => $k === 0])><span class="step">0{{ $k + 1 }} / 06</span><b>{{ $t }}</b><span class="sub">{{ $d }}</span></p>
                @endforeach
            </div>
            <div class="d-card d-qc">
                <div class="d-qc-bar" aria-hidden="true"><i></i></div>
                <div class="d-faces">
                    @foreach([[$demo[0], 'Neu'], [$demo[1], 'Wiederholung · heute fällig']] as $f => [$q, $tag])
                    <div @class(['d-face', 'is-off is-next' => $f > 0]) @if($f > 0) aria-hidden="true" @endif>
                        <div class="d-qmeta"><span>Lernabschnitt {{ $q['section'] }} · {{ $sectionShort[$q['section']] ?? '' }}</span><b>{{ $tag }}</b></div>
                        <p class="d-qtext">{{ $q['text'] }}</p>
                        @foreach($q['answers'] as $n => $answer)
                            <div @class(['d-qopt', 'is-correct' => in_array($n, $q['correct'])]) style="--i:{{ $n }}"><span class="k"><em>{{ 'ABC'[$n] }}</em>{!! $check !!}</span><span>{{ $answer }}</span></div>
                        @endforeach
                        <div class="d-qres"><div><p><b>Richtig: {{ collect($q['correct'])->map(fn ($c) => 'ABC'[$c])->join(' und ') }}.</b> {{ $q['explain'] }}</p></div></div>
                        <div class="d-mastery"><i></i><i></i><i></i><span>richtig in Folge</span><span class="done">Gemeistert</span></div>
                    </div>
                    @endforeach
                </div>
                <span class="d-pointer" aria-hidden="true"></span>
            </div>
        </div>
    </div>
</section>
@endif

<section class="d-wrap d-section" aria-label="Kurz gesagt">
    <p class="d-statement d-reveal"><b>{{ $totalQuestions }} Fragen.</b> Zehn Lernabschnitte. <b>{{ $exam['questions'] }} davon in der Simulation.</b> Und eine App, die dafür sorgt, dass jede einzelne sitzt.</p>
</section>

{{-- 3. Morph: Lernen → Wiederholen → Simulieren → Bestehen --}}
<section class="d-wide d-section" aria-labelledby="weg">
    <h2 id="weg" class="d-h2 d-reveal" style="margin-bottom:clamp(24px,4vw,48px)">Vier Schritte.<br>Kein Rätselraten.</h2>
    <div class="d-story" data-morph="home">
        <div class="d-morph" aria-hidden="true">
            <div class="d-mheads">
                <div class="d-mh is-on"><span>Gemeistert</span><b>{{ $mastered }} <small>von {{ $totalQuestions }}</small></b></div>
                <div class="d-mh"><span>Heute fällig</span><b>12 <small>Fragen</small></b></div>
                <div class="d-mh"><span>Simulation · Frage 27 von {{ $exam['questions'] }}</span><b class="d-timer">11:42</b></div>
                <div class="d-mh"><span>Bestandene Simulationen</span><b>{{ $exam['readyAfter'] }} <small>von {{ $exam['readyAfter'] }} · prüfungsbereit</small></b></div>
            </div>
            <div class="d-field" data-s="0">
                <svg class="d-cycle" viewBox="-100 -100 200 200"><defs><marker id="d-arrow" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="currentColor"/></marker></defs><path d="M 0 -100 A 100 100 0 1 1 -86.6 -50" fill="none" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 5" marker-end="url(#d-arrow)" vector-effect="non-scaling-stroke"/></svg>
                <span class="d-orbit"></span>
                <span class="d-mtag" data-k="heute">heute</span>
                <span class="d-mtag" data-k="tag1">nach 1 Tag</span>
                <span class="d-mtag" data-k="tag3">nach 3 Tagen</span>
                <span class="d-mtag" data-k="selten">dann seltener</span>
                @for($n = 0; $n < 40; $n++)<i class="d-dot" style="--i:{{ $n }}"></i>@endfor
            </div>
        </div>
        <div class="d-beats">
            @foreach([
                ['Lernen.', 'Du arbeitest dich durch die zehn Lernabschnitte. Nach jeder Antwort siehst du die Lösung – und wie oft du die Frage schon richtig hattest.'],
                ['Wiederholen.', 'Richtig beantwortet kommt eine Frage nach einem Tag wieder, dann nach drei Tagen, danach immer seltener. Was wackelt, kommt früher. Der Kreis läuft, bis sie sitzt.'],
                ['Simulieren.', $exam['questions'].' Fragen, '.$exam['minutes'].' Minuten, keine Teilpunkte. Die Simulation öffnet sich, sobald du alles gemeistert hast. Fehler landen sofort in deiner Wiederholung.'],
                ['Bestehen.', 'Nach '.$exam['readyAfter'].' bestandenen Simulationen bist du prüfungsbereit. Der Rest ist Routine.'],
            ] as $k => [$t, $d])
            <article @class(['d-beat', 'is-on' => $k === 0]) data-s="{{ $k }}">
                <p class="d-step-n">0{{ $k + 1 }}</p>
                <h3 class="d-h2" style="font-size:clamp(2rem,4vw,3rem)">{{ $t }}</h3>
                <p class="d-copy" style="margin-top:16px">{{ $d }}</p>
            </article>
            @endforeach
        </div>
    </div>
</section>

<section class="d-wrap d-section" aria-labelledby="abschnitte">
    <p class="d-eyebrow d-reveal">Der Fragenkatalog</p>
    <h2 id="abschnitte" class="d-h2 d-reveal">Zehn Lernabschnitte.</h2>
    <p class="d-copy d-reveal" style="margin:16px 0 48px">Vom Auftrag des THW bis zur Rettung und Bergung – genau so gegliedert wie deine Grundausbildung.</p>
    <ol class="d-list d-reveal">
        @foreach($sections as $s)
            <li><a href="{{ route('landing.thw-theorie') }}#la-{{ $s['nr'] }}"><span class="n">{{ $s['nr'] }}</span><span class="t">{{ $s['short'] }}</span><span class="c">{{ $s['count'] }}</span></a></li>
        @endforeach
    </ol>
    <p style="margin:28px 0 0"><a class="d-more" href="{{ route('landing.thw-theorie') }}">Alle Lernabschnitte der THW-Theorie</a></p>
</section>

<section class="d-wide d-section" aria-label="Weitere Vorteile">
    <div class="d-tiles">
        <div class="wide d-band d-reveal">
            <p class="d-eyebrow">Für Ortsverbände</p>
            <h2 class="d-h2" style="font-size:clamp(2rem,4vw,3rem)">Der ganze OV.<br>Ein Lernstand.</h2>
            <p class="d-copy" style="margin-top:16px">Eigene Lernpools, Live-Lernsessions am Dienstabend mit Echtzeit-Rangliste und eine Checkliste der Lernabschnitte für jede Helferin und jeden Helfer. Dazu Fragenkataloge für weitere Lehrgänge.</p>
            <p style="margin:28px 0 0"><a class="d-more" href="{{ route('landing.wiki.show', 'ortsverband') }}">Mehr für Ausbildungsbeauftragte</a></p>
        </div>
        <div class="d-stage d-reveal" style="--i:1">
            <p class="d-big d-num">0 €</p>
            <p class="d-copy">Kostenlos, ohne Werbung, ohne In-App-Käufe. DSGVO-konform, Server in Deutschland.</p>
        </div>
        <div class="d-stage d-reveal" style="--i:2">
            <h3 class="d-h3">Auf jedem Gerät.</h3>
            <p class="d-copy">Als App auf iPhone, Android und Desktop installieren. Ein Konto, ein Lernstand – ohne App Store.</p>
        </div>
    </div>
</section>

<section class="d-wrap d-section" aria-label="Stimme aus dem Ortsverband">
    <figure style="margin:0" class="d-reveal">
        <blockquote style="margin:0;font:600 clamp(1.75rem,3.6vw,2.75rem)/1.2 var(--font);letter-spacing:-0.025em;max-width:26ch">„Besonders die Prüfungssimulation war Gold wert – ich wusste genau, was mich erwartet. Prüfung beim ersten Versuch bestanden!“</blockquote>
        <figcaption class="d-small" style="margin-top:24px;font-size:17px">Stefan R., THW-Helfer, OV München</figcaption>
    </figure>
    @if($stats['users'] > 0)
    <p class="d-small d-reveal" style="margin:48px 0 0;font-size:17px"><b style="color:var(--thw);font-weight:600">{{ $fmt($stats['users']) }}+</b> Helferinnen und Helfer lernen mit dem THW-Trainer @if($stats['pass_rate'] > 0)· <b style="color:var(--thw);font-weight:600">{{ $stats['pass_rate'] }} %</b> bestandene Simulationen @endif· <a class="d-more" style="font-size:inherit" href="{{ route('landing.statistics') }}">Statistik</a></p>
    @endif
</section>

<section class="d-wrap d-section" aria-labelledby="faq">
    <h2 id="faq" class="d-h2 d-reveal" style="margin-bottom:40px">Häufige Fragen.</h2>
    <div class="d-faq d-reveal">@foreach($faq as $f)<details><summary>{{ $f['q'] }}</summary><p>{{ $f['a'] }}</p></details>@endforeach</div>
</section>

<section class="d-closing d-band" data-nav-dark>
    <div class="d-wrap">
        <h2 class="d-h1 d-reveal" style="font-size:clamp(2.75rem,6.4vw,5rem)">Bereit,<br>wenn du es bist.</h2>
        <p class="d-sub d-reveal">Ein Konto, ein Lernstand – egal ob Handy, Tablet oder Laptop.</p>
        <div class="d-ctas d-reveal" style="--i:1">
            <a class="d-pill" href="{{ route('register') }}">Kostenlos starten</a>
            <a class="d-more" href="{{ route('landing.guest.practice.menu') }}">Ohne Konto üben</a>
        </div>
    </div>
</section>
@endsection
