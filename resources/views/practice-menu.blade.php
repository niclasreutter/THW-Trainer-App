@extends('layouts.app')

@section('title', 'Theorie lernen - THW Trainer')

@section('content')
@php
    $wd = fn ($t) => \App\Helpers\LearnText::wd($t);
    $unavailable = session('practice_unavailable');
    $extrasEnabled = auth()->check() && auth()->user()->extras_enabled;
@endphp

<div class="l-page">
    <header class="l-head">
        <h1 class="l-title">Theorie lernen</h1>
        <p class="l-meta">Wähle deinen Lernmodus</p>
    </header>

    <div class="l-stack">
        {{-- Kennzahlen und Fragensuche --}}
        <div class="l-pm-top">
            <dl class="l-figs">
                <div><dt>Fehler</dt><dd class="{{ $failedCount > 0 ? 'l-tone-bad' : '' }}">{{ $failedCount }}</dd></div>
                <div><dt>Ungelöst</dt><dd>{{ $unsolvedCount }}</dd></div>
                <div><dt>Gemeistert</dt><dd>{{ $solvedCount }}</dd></div>
                <div><dt>Gesamt</dt><dd>{{ $totalQuestions }}</dd></div>
            </dl>
            <form action="{{ route('practice.search') }}" method="GET" class="l-search" role="search">
                <label class="l-small" for="l-search-q">Fragensuche</label>
                <span>
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="text" name="search" id="l-search-q" placeholder="Frage Nr. eingeben...">
                </span>
            </form>
        </div>

        {{-- Hinweis: Zusatz-Fragen (nur wenn deaktiviert) --}}
        @auth
            @if(!$extrasEnabled)
                <div class="l-group">
                    <a href="{{ route('einstellungen') }}#extras-enabled" class="l-row">
                        <span class="l-row-t">
                            <b>Mehr Lern-Methoden verfügbar</b>
                            <span>Zuordnungs- und Bild-Fragen als Lern-Ergänzung. Nicht Teil der Prüfung.</span>
                        </span>
                        <span class="l-link">In Einstellungen aktivieren</span>
                    </a>
                </div>
            @endif
        @endauth

        {{-- Bühne: Empfehlung, oder der Hinweis „nicht verfügbar“ (blendet sich nach 10 Sekunden aus) --}}
        @if($unavailable)
            <div class="l-warn" id="practice-unavailable-card" role="status">
                <p class="l-warn-l">Nicht verfügbar</p>
                <p class="l-warn-t">{{ $unavailable['title'] }}</p>
                <p class="l-small">{{ $wd($unavailable['message']) }}</p>
                <p class="l-warn-n">{{ $wd($unavailable['next_review']) }}</p>
            </div>
            <div id="smart-action-normal" style="display: none;">
        @endif

        @if(($smartAction['type'] ?? 'action') === 'info')
            <div class="l-stage l-stage--calm l-stage--sm">
                <span class="l-stage-label">{{ $smartAction['label'] }}</span>
                <span class="l-stage-title">{{ $wd($smartAction['title']) }}</span>
                <span class="l-stage-desc">{{ $wd($smartAction['desc']) }}</span>
                <span class="l-stage-status"><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Komm später wieder</span>
            </div>
        @else
            <a href="{{ $smartAction['route'] }}" class="l-stage l-stage--sm">
                <span class="l-gearmark" aria-hidden="true">@include('landing.partials.gear')</span>
                <span class="l-stage-label">{{ $smartAction['label'] }}</span>
                <span class="l-stage-title">{{ $wd($smartAction['title']) }}</span>
                <span class="l-stage-desc">{{ $wd($smartAction['desc']) }}</span>
                <span class="l-btn l-btn--white">Jetzt starten</span>
            </a>
        @endif

        @if($unavailable)
            </div>
        @endif

        {{-- Lernmodus --}}
        <section aria-labelledby="l-modi">
            <h2 id="l-modi" class="l-group-h">Lernmodus</h2>
            <div class="l-group">
                <a href="{{ route('practice.all') }}" class="l-row">
                    <span class="l-row-t"><b>Alle Fragen</b><span>Ungelöste werden bevorzugt</span></span>
                    <span class="l-row-v">{{ $totalQuestions }}</span>
                    <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                </a>
                @if($unsolvedCount > 0)
                    <a href="{{ route('practice.unsolved') }}" class="l-row">
                        <span class="l-row-t"><b>Ungelöste</b><span>Neue Fragen lernen</span></span>
                        <span class="l-row-v">{{ $unsolvedCount }}</span>
                        <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                    </a>
                @else
                    <div class="l-row is-off">
                        <span class="l-row-t"><b>Ungelöste</b><span>Erledigt</span></span>
                        <span class="l-row-v">0</span>
                    </div>
                @endif
                @if($failedCount > 0)
                    <a href="{{ route('failed.index') }}" class="l-row">
                        <span class="l-row-t"><b>Fehler</b><span>Fehlerfragen wiederholen</span></span>
                        <span class="l-row-v">{{ $failedCount }}</span>
                        <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                    </a>
                @else
                    <div class="l-row is-off">
                        <span class="l-row-t"><b>Fehler</b><span>Keine</span></span>
                        <span class="l-row-v">0</span>
                    </div>
                @endif
                @if($spacedRepetitionDue > 0)
                    <a href="{{ route('practice.spaced-repetition') }}" class="l-row">
                        <span class="l-row-t"><b>Wiederholung</b><span>Fragen kommen nach 1 Tag, dann nach 3 Tagen wieder</span></span>
                        <b class="l-count">{{ $spacedRepetitionDue }}</b>
                    </a>
                @elseif($nextSrLabel)
                    <div class="l-row">
                        <span class="l-row-t"><b>Wiederholung</b><span>Aktuell keine fällig</span></span>
                        <span class="l-row-v">{{ $nextSrCount }} {{ $nextSrLabel === 'In 1 Tagen' ? 'morgen' : lcfirst($nextSrLabel) }}</span>
                    </div>
                @endif
                @if($extrasEnabled)
                    <a href="{{ route('practice.standard-only') }}" class="l-row">
                        <span class="l-row-t"><b>Nur Standardfragen</b><span>Offizielle Fragen ohne Zusatz-Fragen üben</span></span>
                        <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('practice.extras-only') }}" class="l-row">
                        <span class="l-row-t"><b>Nur Zusatz-Fragen</b><span>Zuordnungen und Bild-Fragen üben</span></span>
                        <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </section>

        {{-- Lernabschnitte: zwei Gruppen nebeneinander --}}
        <section aria-labelledby="l-la">
            <h2 id="l-la" class="l-group-h">Lernabschnitte <span>10 Abschnitte</span></h2>
            <div class="l-la">
                @foreach([[1, 5], [6, 10]] as [$from, $to])
                    <div class="l-group">
                        @for($i = $from; $i <= $to; $i++)
                            @php
                                $total = $sectionStats[$i]['total'];
                                $solved = $sectionStats[$i]['solved'];
                                $pct = $total > 0 ? round(($solved / $total) * 100) : 0;
                            @endphp
                            <a href="{{ route('practice.section', $i) }}" class="l-row">
                                <span class="l-row-n">{{ $i }}</span>
                                <span class="l-row-t">
                                    <b>{{ $sectionNames[$i] }}</b>
                                    <span>{{ $solved }} von {{ $total }} gemeistert</span>
                                    <span class="l-line {{ $pct >= 100 ? 'is-ok' : '' }}"><i style="width: {{ $pct }}%;"></i></span>
                                </span>
                                <span class="l-row-v">{{ $pct }} %</span>
                            </a>
                        @endfor
                    </div>
                @endforeach
            </div>
        </section>

        @auth
            <div class="l-group">
                <a href="{{ route('zusatzfragen-vorschlagen.index') }}" class="l-row">
                    <span class="l-row-t"><b>Zusatz-Frage vorschlagen</b><span>Eigene Frage für den Katalog einreichen</span></span>
                    <span class="l-link">Einreichen</span>
                </a>
            </div>
        @endauth
    </div>
</div>
@endsection

@if(session('practice_unavailable'))
@push('scripts')
<script>
    setTimeout(function() {
        var warning = document.getElementById('practice-unavailable-card');
        var normal = document.getElementById('smart-action-normal');
        if (warning && normal) {
            warning.style.opacity = '0';
            setTimeout(function() {
                warning.style.display = 'none';
                normal.style.display = 'block';
            }, 600);
        }
    }, 10000);
</script>
@endpush
@endif
