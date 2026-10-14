@extends('layouts.public')

@section('title', 'Anonym üben ohne Anmeldung | THW-Trainer.de')
@section('description', 'THW Theorie anonym üben ohne Anmeldung: alle Prüfungsfragen der Grundausbildung oder eine Prüfungssimulation mit 40 Fragen. Kostenlos und ohne Registrierung.')
@section('canonical', route('landing.guest.practice.menu'))

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => \App\Helpers\DomainHelper::landingUrl('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Anonym üben', 'item' => route('landing.guest.practice.menu')],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
<section class="d-band d-pagehero" data-nav-dark>
@include('landing.partials.gear')
<div class="d-wrap">
    <nav aria-label="Brotkrumen"><ol class="d-crumbs d-in"><li><a href="{{ \App\Helpers\DomainHelper::landingHomeUrl() }}">Startseite</a></li><li aria-current="page">Anonym üben</li></ol></nav>
    <h1 class="d-h1 d-in" style="--i:1;font-size:clamp(2.75rem,6.6vw,5.25rem)"><span class="d-eyebrow">THW Theorie üben ohne Anmeldung</span> Einfach anfangen.</h1>
    <p class="d-sub d-in" style="--i:2">Ohne Konto, ohne gespeicherte Daten. Wähle, wie du üben möchtest.</p>
</div>
</section>

<section class="d-wide" style="padding-top:clamp(48px,7vw,80px)">
    <div class="d-choice">
        <div class="d-stage d-in" style="--i:3">
            <p class="d-eyebrow">Üben</p>
            <h2 class="d-h2" style="font-size:clamp(2rem,4vw,3rem)">Frage für Frage.</h2>
            <p class="d-copy" style="margin-top:14px">Alle Fragen der Grundausbildung in zufälliger Reihenfolge, die Lösung direkt nach jeder Antwort.</p>
            <dl><dt>Fragen</dt><dd>{{ $totalQuestions }}</dd><dt>Zeitlimit</dt><dd>keins</dd></dl>
            <div><a class="d-pill" href="{{ route('landing.guest.practice.all') }}">Üben starten</a></div>
        </div>
        <div class="d-stage d-in" style="--i:4">
            <p class="d-eyebrow">Simulieren</p>
            <h2 class="d-h2" style="font-size:clamp(2rem,4vw,3rem)">Wie in der Prüfung.</h2>
            <p class="d-copy" style="margin-top:14px">{{ $exam['questions'] }} zufällige Fragen unter Zeitdruck, Auswertung am Ende.</p>
            <dl><dt>Zeit</dt><dd>{{ $exam['minutes'] }} Minuten</dd><dt>Bestanden</dt><dd>ab {{ $exam['passCount'] }} richtigen</dd></dl>
            <div><a class="d-pill d-pill--ghost" href="{{ route('landing.guest.exam.index') }}">Simulation starten</a></div>
        </div>
    </div>
</section>

<section class="d-wrap d-section" aria-labelledby="mehr">
    <h2 id="mehr" class="d-h2 d-reveal">Mit Konto geht mehr.</h2>
    <p class="d-copy d-reveal" style="margin:16px 0 48px">Genauso kostenlos. Dein Lernstand bleibt erhalten – auf jedem Gerät.</p>
    <ul class="d-cmp d-reveal">
        <li class="d-small" style="font-size:13px;border-top:0" aria-hidden="true"><span></span><span>Ohne Konto</span><span>Mit Konto</span></li>
        @foreach([['Fragen üben', true], ['Prüfungssimulation', true], ['Lernstand speichern', false], ['Spaced Repetition', false], ['Fehlerliste und Lesezeichen', false], ['Ortsverband: Lernpools und Live-Sessions', false]] as [$feature, $guest])
            <li><span>{{ $feature }}</span><span @class(['y' => $guest])>{{ $guest ? 'Ja' : 'Nein' }}</span><span class="y">Ja</span></li>
        @endforeach
    </ul>
    <div class="d-ctas d-reveal"><a class="d-pill" href="{{ route('register') }}">Kostenlos registrieren</a></div>
</section>
@endsection
