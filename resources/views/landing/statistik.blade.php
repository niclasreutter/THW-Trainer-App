@extends('layouts.public')

@section('title', 'Plattform-Statistiken | THW-Trainer.de')
@section('description', 'Anonyme Plattform-Statistiken des THW-Trainers: registrierte Nutzer, beantwortete Fragen, Erfolgsquote der Prüfungssimulation und Fragen pro Lernabschnitt.')
@section('canonical', route('landing.statistics'))

@php
    $fmt = fn ($n) => number_format($n, 0, ',', '.');
    $sectionNames = [1 => 'THW & Zivilschutz', 2 => 'Arbeitssicherheit', 3 => 'Leinen & Anschlagmittel', 4 => 'Leitern', 5 => 'Strom & Licht', 6 => 'Metall, Holz, Stein', 7 => 'Lasten bewegen', 8 => 'Am und auf dem Wasser', 9 => 'Einsatzgrundlagen', 10 => 'Rettung & Bergung'];
    $counts = [];
    foreach ($sectionNames as $nr => $name) { $counts[$nr] = (int) ($stats['section_counts'][$nr] ?? $stats['section_counts'][(string) $nr] ?? 0); }
    $maxCount = max(1, max($counts));

    // Wachstum aus der Nutzer-Historie (bis zu 90 Tage)
    $growthSeries = $stats['user_growth']['total'] ?? [];
    if (count($growthSeries) < 2) { $growthSeries = [$stats['users'], $stats['users']]; }
    $growth = (int) end($growthSeries) - (int) reset($growthSeries);
    $filled = (int) round($stats['pass_rate'] / 100 * 40);

    // Lernrhythmus: stärkster Wochentag der letzten 30 Tage
    $days = $stats['questions_per_day'];
    $weekdayNames = [1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Samstag', 7 => 'Sonntag'];
    $byWeekday = [];
    foreach ($days['dates'] as $i => $date) { $byWeekday[\Carbon\Carbon::parse($date)->isoWeekday()][] = $days['values'][$i]; }
    $avg = array_map(fn ($v) => array_sum($v) / max(1, count($v)), $byWeekday);
    arsort($avg);
    $ranked = array_keys($avg);
    $peakDays = [];
    if (array_sum($days['values']) > 0 && count($ranked)) {
        $peakDays[] = $ranked[0];
        if (isset($ranked[1]) && $avg[$ranked[1]] >= 0.9 * $avg[$ranked[0]]) { $peakDays[] = $ranked[1]; }
        sort($peakDays);
    }
    $peakLabel = collect($peakDays)->map(fn ($d) => $weekdayNames[$d])->join(' und ');
    $dayMax = max(1, max($days['values'] ?: [0]));
@endphp

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => \App\Helpers\DomainHelper::landingUrl('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Statistik', 'item' => route('landing.statistics')],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
<section class="d-band d-pagehero" data-nav-dark>
@include('landing.partials.gear')
<div class="d-wrap">
    <nav aria-label="Brotkrumen"><ol class="d-crumbs d-in"><li><a href="{{ \App\Helpers\DomainHelper::landingHomeUrl() }}">Startseite</a></li><li aria-current="page">Statistik</li></ol></nav>
    <h1 class="d-h1 d-in" style="--i:1;font-size:clamp(2.75rem,6.6vw,5.25rem)"><span class="d-eyebrow">Plattform-Statistik · Stand {{ now()->format('d.m.Y') }}</span> Der <span style="white-space:nowrap">THW-Trainer</span> in Zahlen.</h1>
    <p class="d-sub d-in" style="--i:2">Die Plattform in vier Bildern – anonym und aggregiert.</p>
</div>
</section>

<section class="d-wide" style="padding-top:clamp(40px,6vw,72px)" aria-label="Die Plattform in vier Bildern">
    <div class="d-story" data-morph="stats" data-users="{{ json_encode(array_values($growthSeries)) }}" data-counts="{{ json_encode(array_values($counts)) }}" data-filled="{{ $filled }}">
        <div class="d-morph" aria-hidden="true">
            <div class="d-mheads">
                <div class="d-mh is-on"><span>Helferinnen und Helfer</span><b>{{ $fmt($stats['users']) }}</b></div>
                <div class="d-mh"><span>{{ $growth > 0 ? 'Neue Konten in 90 Tagen' : 'Registrierte Konten' }}</span><b>{{ $growth > 0 ? '+'.$fmt($growth) : $fmt($stats['users']) }}</b></div>
                <div class="d-mh"><span>Bestandene Simulationen</span><b>{{ $stats['pass_rate'] }} %</b></div>
                <div class="d-mh"><span>Fragen im Katalog</span><b>{{ $stats['total_questions'] }} <small>in 10 Lernabschnitten</small></b></div>
            </div>
            <div class="d-field" data-s="0">
                <span class="d-mtag dim" data-k="legend">1 Punkt ≈ {{ $fmt(max(1, round($stats['users'] / 40))) }} Personen</span>
                <span class="d-mtag dim" data-k="start">vor 90 Tagen · {{ $fmt(reset($growthSeries)) }}</span>
                <span class="d-mtag" data-k="end">heute · {{ $fmt(end($growthSeries)) }}</span>
                <span class="d-mtag dim" data-k="center">{{ $filled }} von 40</span>
                @foreach($sectionNames as $nr => $name)<span class="d-mtag dim" data-k="n{{ $nr }}">{{ $nr }}</span>@endforeach
                @for($n = 0; $n < 40; $n++)<i class="d-dot" style="--i:{{ $n }}"></i>@endfor
            </div>
        </div>
        <div class="d-beats">
            @foreach([
                [$fmt($stats['users']).' Menschen lernen hier.', 'Zusammen haben sie '.$fmt($stats['questions_answered']).' Fragen beantwortet. Jeder Punkt steht für rund '.$fmt(max(1, round($stats['users'] / 40))).' Helferinnen und Helfer.'],
                [$growth > 0 ? 'Und es werden mehr.' : 'Stetig im Einsatz.', $growth > 0 ? '+'.$fmt($growth).' neue Konten in den letzten 90 Tagen. Die Punkte folgen der tatsächlichen Kurve.' : 'Die Punkte folgen dem Verlauf der registrierten Konten.'],
                [$stats['pass_rate'].' Prozent bestehen.', 'Von 40 Punkten sind '.$filled.' gefüllt: So viele Prüfungssimulationen enden mit mindestens 80 Prozent richtigen Antworten.'],
                [$stats['total_questions'].' Fragen, ungleich verteilt.', 'Jede Säule zeigt den Anteil eines Lernabschnitts am Katalog.'.($stats['lehrgang_count'] > 0 ? ' Dazu kommen '.$fmt($stats['lehrgang_question_count']).' Fragen in '.$stats['lehrgang_count'].' '.($stats['lehrgang_count'] === 1 ? 'Lehrgang' : 'Lehrgängen').'.' : '')],
            ] as $k => [$t, $d])
            <article @class(['d-beat', 'is-on' => $k === 0]) data-s="{{ $k }}">
                <p class="d-step-n">0{{ $k + 1 }}</p>
                <h2 class="d-h2" style="font-size:clamp(2rem,4vw,3rem)">{{ $t }}</h2>
                <p class="d-copy" style="margin-top:16px">{{ $d }}</p>
            </article>
            @endforeach
        </div>
    </div>
</section>

<section class="d-wrap d-section" aria-labelledby="rhythmus">
    <h2 id="rhythmus" class="d-h2 d-reveal">{{ $peakLabel ? 'Am meisten gelernt wird am '.$peakLabel.'.' : 'Lernaktivität der letzten 30 Tage.' }}</h2>
    <p class="d-copy d-reveal" style="margin:16px 0 40px">Beantwortete Fragen pro Tag, letzte 30 Tage. {{ $stats['avg_hit_rate'] }} Prozent aller Antworten sind richtig.</p>
    <div class="d-bars" data-anim role="img" aria-label="Beantwortete Fragen pro Tag in den letzten 30 Tagen{{ $peakLabel ? ', meiste am '.$peakLabel : '' }}">
        @foreach($days['values'] as $i => $value)
            @php $weekday = \Carbon\Carbon::parse($days['dates'][$i])->isoWeekday(); @endphp
            <span @class(['peak' => in_array($weekday, $peakDays) && ! $loop->last, 'today' => $loop->last]) data-tip="{{ $days['labels'][$i] }} · {{ $fmt($value) }}" style="--h:{{ round($value / $dayMax * 100, 1) }}%"><i style="--i:{{ $loop->index }}"></i></span>
        @endforeach
    </div>
    <p class="d-legend">@if($peakLabel)<span><i style="background:var(--thw)"></i>{{ $peakLabel }}</span>@endif<span><i style="background:#B9C7DF"></i>übrige Tage</span><span><i style="background:#fff;box-shadow:inset 0 0 0 2px var(--thw)"></i>heute</span></p>
</section>

<section class="d-wrap d-section" aria-labelledby="katalog">
    <h2 id="katalog" class="d-h2 d-reveal" style="margin-bottom:40px">{{ $stats['total_questions'] }} Fragen.@if($stats['lehrgang_question_count'] > 0)<br> <span style="color:var(--text-3)">Dazu {{ $fmt($stats['lehrgang_question_count']) }} in Lehrgängen.</span>@endif</h2>
    <ol class="d-list d-cat" data-anim>
        @foreach($sectionNames as $nr => $name)<li><div><span class="n">{{ $nr }}</span><span class="t">{{ $name }}</span><span class="c">{{ $counts[$nr] }}</span><span class="bar" aria-hidden="true"><i style="--w:{{ round($counts[$nr] / $maxCount * 100, 1) }}%;--i:{{ $loop->index }}"></i></span></div></li>@endforeach
    </ol>
</section>

<section class="d-wrap d-section" aria-labelledby="datenschutz">
    <h2 id="datenschutz" class="d-h3 d-reveal">Datenschutz</h2>
    <p class="d-copy d-reveal" style="margin-top:8px;max-width:60ch">Alle Werte sind anonym und aggregiert. Es werden keine personenbezogenen Daten veröffentlicht; Nutzerzahlen werden auf Zehner, Antworten auf Hunderter abgerundet. Details in der <a href="{{ route('landing.datenschutz') }}" style="color:var(--thw)">Datenschutzerklärung</a>.</p>
</section>
@endsection
