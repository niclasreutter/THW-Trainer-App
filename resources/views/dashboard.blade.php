@extends('layouts.app')

@section('title', 'Dashboard - Dein Lernfortschritt')
@section('description', 'Dein persönliches THW-Trainer Dashboard: Verfolge deinen Lernfortschritt, wiederhole falsche Fragen und bereite dich optimal auf deine THW-Prüfung vor.')

@section('content')
@php
    $user = Auth::user();

    /* ── Time-based greeting ─────────────────────── */
    $hour = (int) now()->format('H');
    $greeting = $hour < 12 ? 'Guten Morgen' : ($hour < 18 ? 'Guten Tag' : 'Guten Abend');

    /* ── League info ─────────────────────────────── */
    $userLeague = $user->league ?? 'bronze';
    $leagueInfo = \App\Services\LeagueService::getLeagueInfo($userLeague);

    /* ── Ausbilder check ─────────────────────────── */
    $userOV = $user->ortsverbände->first();
    $isAusbilder = false;
    if ($userOV) {
        $memberPivot = $userOV->members()->where('user_id', $user->id)->first();
        $isAusbilder = $memberPivot && $memberPivot->pivot->role === 'ausbildungsbeauftragter';
    }

    /* ── OV Card Data ───────────────────────────── */
    $ovCardData = null;
    if ($userOV) {
        if ($isAusbilder) {
            $ovCardData = [
                'type' => 'ausbilder',
                'stats' => $userOV->getAverageStats(),
            ];
        } else {
            $ovEnrolledLernpools = $user->enrolledLernpools()
                ->where('ortsverband_id', $userOV->id)
                ->take(3)
                ->get()
                ->map(fn($lp) => [
                    'id' => $lp->id,
                    'name' => $lp->name,
                    'progress' => $lp->enrollments()
                        ->where('user_id', $user->id)
                        ->first()?->getProgress() ?? 0,
                ]);

            $ovTotalEnrolled = $user->enrolledLernpools()
                ->where('ortsverband_id', $userOV->id)
                ->count();

            $ovRanking = null;
            $ovUserRank = null;
            if ($userOV->ranking_visible) {
                $ovMemberProgress = $userOV->getMemberProgress();
                $ovSorted = collect($ovMemberProgress)->sortByDesc('points')->values();
                $ovRanking = $ovSorted->take(3);
                $ovUserRank = $ovSorted->search(fn($m) => $m['user']->id === $user->id);
                $ovUserRank = $ovUserRank !== false ? $ovUserRank + 1 : null;
            }

            $ovCardData = [
                'type' => 'member',
                'lernpools' => $ovEnrolledLernpools,
                'totalEnrolled' => $ovTotalEnrolled,
                'ranking' => $ovRanking,
                'userRank' => $ovUserRank,
            ];
        }
    }

    /* ── Weekly activity data ────────────────────── */
    $weekStart = \Carbon\Carbon::now()->startOfWeek();
    $days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
    $maxCount = max((int) ($weeklyActivity->max('count') ?? 0), 1);
    $weekTotal = $weeklyActivity->sum('count');
    $weekCorrect = $weeklyActivity->sum('correct');
    $weekHitRate = $weekTotal > 0 ? round(($weekCorrect / $weekTotal) * 100) : 0;

    /* ── Gemeistert (3× in Folge richtig) für „Alle meistern“ ── */
    $masteredCount = \App\Models\UserQuestionProgress::where('user_id', $user->id)
        ->where('consecutive_correct', '>=', \App\Models\UserQuestionProgress::MASTERY_THRESHOLD)->count();

    /* ── Wiederholungsplan der nächsten Tage (nur Anzeige) ── */
    $srSchedule = collect(app(\App\Services\SpacedRepetitionService::class)->getUpcomingSchedule($user->id))
        ->filter(fn ($sr) => (int) $sr['days_from_now'] >= 1) // „heute“ steht schon in „Heute fällig“
        ->take(3);

    /* ── Bühne: genau eine Empfehlung ── */
    $stageError = !empty($smartAction['route']) ? session('error') : null; // wie bisher: Info-Karte zeigt keinen Fehler
    $stageType = $stageError ? 'error' : ($smartAction['type'] ?? 'action');

    $forYouCount = ($unopenedLootboxes > 0 ? 1 : 0) + ($showSurveyNudge ? 1 : 0) + ($user->extras_enabled ? 1 : 0);
@endphp

<div class="l-page">
    <header class="l-head">
        <p class="l-eyebrow">{{ $greeting }}</p>
        <h1 class="l-title">{{ $user->name }}</h1>
        <p class="l-meta">Level {{ $user->level ?? 1 }}, {{ number_format($user->points ?? 0, 0, ',', '.') }} Punkte{!! $user->leaderboard_consent ? ', <b>'.e($leagueInfo['name']).'-Liga</b>' : '' !!}</p>
        @if($nextLevelPoints > 0)
            <div class="l-xp">
                <span class="l-line"><i style="width: {{ $levelProgress }}%;"></i></span>
                <span class="l-small">noch {{ number_format($nextLevelPoints, 0, ',', '.') }} XP bis Level {{ ($user->level ?? 1) + 1 }}</span>
            </div>
        @endif
    </header>

    <div class="l-cols">
        {{-- ═══ Hauptspalte ═══ --}}
        <div class="l-col">
            {{-- Bühne: die nächste Aufgabe --}}
            @if($stageType === 'info')
                <div class="l-stage l-stage--calm l-o1" data-tour-step="practice">
                    <span class="l-stage-label">{{ $smartAction['label'] }}</span>
                    <span class="l-stage-title">{{ \App\Helpers\LearnText::wd($smartAction['title']) }}</span>
                    <span class="l-stage-desc">{{ \App\Helpers\LearnText::wd($smartAction['desc']) }}</span>
                    <span class="l-stage-status"><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Komm später wieder</span>
                </div>
            @else
                <a href="{{ $smartAction['route'] }}" class="l-stage l-o1 is-{{ $stageType }}" data-tour-step="practice">
                    <span class="l-gearmark" aria-hidden="true">@include('landing.partials.gear')</span>
                    <span class="l-stage-label">
                        @if($stageError)
                            Nicht möglich
                        @elseif($stageType === 'live')
                            <i class="l-live" aria-hidden="true"></i>{{ $smartAction['label'] }}
                        @else
                            {{ $smartAction['label'] }}
                        @endif
                    </span>
                    @if($stageError)
                        <span class="l-stage-title l-stage-title--msg">{{ $stageError }}</span>
                    @else
                        <span class="l-stage-title">{{ \App\Helpers\LearnText::wd($smartAction['title']) }}</span>
                        <span class="l-stage-desc">{{ \App\Helpers\LearnText::wd($smartAction['desc']) }}</span>
                    @endif
                    <span class="l-btn l-btn--white">{{ $stageError ? 'Jetzt weiterlernen' : $smartAction['btn'] }}</span>
                </a>
            @endif

            @if(isset($streakAtRisk) && $streakAtRisk && ($dailyStreakGoal ?? 0) > 0)
                <p class="l-note l-o2">
                    <i class="bi bi-fire" aria-hidden="true"></i>
                    <span><b>{{ $user->streak_days }} Tage Streak in Gefahr!</b> Beantworte heute mind. {{ $dailyStreakGoal }} Fragen.@if(($user->streak_freezes_available ?? 0) > 0) {{ $user->streak_freezes_available }} Freeze{{ $user->streak_freezes_available > 1 ? 's' : '' }} verfügbar.@endif</span>
                </p>
            @endif

            {{-- Dein Fortschritt: Linie über der Zahl --}}
            <section class="l-o5" aria-labelledby="l-fort">
                <h2 id="l-fort" class="l-h2">Dein Fortschritt</h2>
                <ol class="l-steps">
                    <li class="{{ $solvedPercent >= 100 ? 'is-done' : '' }}" style="--p: {{ min(100, $solvedPercent) }}%;">
                        <p class="l-step-l">Fragen lernen</p>
                        <p class="l-step-v">{{ $solvedPercent }} %</p>
                        <p class="l-small">bearbeitet</p>
                    </li>
                    <li class="{{ $masteryPercent >= 100 ? 'is-done' : '' }}" style="--p: {{ min(100, $masteryPercent) }}%;">
                        <p class="l-step-l">Alle meistern</p>
                        <p class="l-step-v">{{ $masteryPercent }} %</p>
                        <p class="l-small">3× in Folge richtig, {{ $masteredCount }} von {{ $totalQuestions }}</p>
                    </li>
                    <li class="{{ $exams >= 5 ? 'is-done' : '' }}" style="--p: {{ min(100, $exams * 20) }}%;" data-tour-step="exam">
                        <p class="l-step-l">Prüfung</p>
                        <p class="l-step-v">{{ $exams }}/5</p>
                        <p class="l-small">{{ $canStartExam || $exams > 0 ? 'bestanden' : 'öffnet, wenn alle Fragen gemeistert sind' }}</p>
                    </li>
                </ol>
            </section>

            <div class="l-duo l-o8">
                {{-- Diese Woche --}}
                <section aria-labelledby="l-wo" data-tour-step="stats">
                    <div class="l-sec-h">
                        <h2 id="l-wo" class="l-h2">Diese Woche</h2>
                        <a href="{{ route('statistics') }}" class="l-link">Statistiken</a>
                    </div>
                    @if($weekTotal > 0)
                        <p class="l-small">{{ $weekTotal }} Fragen, davon {{ $weekHitRate }} % richtig.@if($todayAnswered > 0) Heute {{ $todayAnswered }}.@endif</p>
                        <div class="l-bars" role="img" aria-label="Fragen pro Tag in dieser Woche">
                            @for($d = 0; $d < 7; $d++)
                                @php
                                    $dayDate = $weekStart->copy()->addDays($d);
                                    $dayData = $weeklyActivity->firstWhere('date', $dayDate->format('Y-m-d'));
                                    $count = $dayData->count ?? 0;
                                @endphp
                                <span class="{{ $dayDate->isToday() ? 'is-today' : '' }}" title="{{ $count }} Fragen am {{ $dayDate->format('d.m.') }}">
                                    <i style="--f: {{ round($count / $maxCount, 3) }};">@if($count > 0)<b>{{ $count }}</b>@endif</i>
                                    <em>{{ $days[$d] }}</em>
                                </span>
                            @endfor
                        </div>
                    @else
                        <div class="l-empty">
                            <p>Noch keine Aktivität diese Woche</p>
                            <a href="{{ route('practice.all') }}" class="l-link">Erste Frage beantworten</a>
                        </div>
                    @endif
                </section>

                {{-- Ausbildungsfortschritt --}}
                <section aria-labelledby="l-au">
                    <div class="l-sec-h">
                        <h2 id="l-au" class="l-h2">Ausbildungsfortschritt</h2>
                        <a href="{{ route('training-progress.index') }}" class="l-link">Module ansehen</a>
                    </div>
                    <p class="l-big">{{ $trainingOverview['percent'] }} %</p>
                    <p class="l-small">{{ $trainingOverview['done'] }} / {{ $trainingOverview['total'] }} Module erledigt</p>
                    <div class="l-mini" aria-label="Module je Lernabschnitt">
                        @foreach($trainingLaSummary as $la)
                            <a href="{{ route('training-progress.index', ['open' => $la['key']]) }}"
                               class="{{ $la['percent'] >= 100 ? 'is-done' : '' }}"
                               title="{{ $la['nr'] }}: {{ $la['title'] }}, {{ $la['done'] }}/{{ $la['total'] }}"
                               aria-label="{{ $la['nr'] }}, {{ $la['percent'] }} Prozent">
                                <i style="--f: {{ round($la['percent'] / 100, 2) }};"></i>
                                <em>{{ \Illuminate\Support\Str::after($la['nr'], 'LA ') }}</em>
                            </a>
                        @endforeach
                    </div>
                    <p class="l-small l-split">
                        <span>Lernabschnitte {{ $trainingOverview['la_done'] }}/{{ $trainingOverview['la_total'] }}</span>
                        <span>Zusatz {{ $trainingOverview['zusatz_done'] }}/{{ $trainingOverview['zusatz_total'] }}</span>
                    </p>
                </section>
            </div>

            {{-- Lehrgänge --}}
            <section class="l-o9" aria-labelledby="l-lg">
                <div class="l-sec-h">
                    <h2 id="l-lg" class="l-h2">Lehrgänge</h2>
                    <a href="{{ route('lehrgaenge.index') }}" class="l-link">Alle anzeigen</a>
                </div>
                <div class="l-group">
                    @forelse($enrolledLehrgaenge->take(3) as $lehrgang)
                        @php
                            $lgTotal = $lehrgang->questions()->count();
                            $lgSolved = $lgTotal > 0
                                ? \App\Models\UserLehrgangProgress::where('user_id', $user->id)
                                    ->whereIn('lehrgang_question_id', $lehrgang->questions()->pluck('id'))
                                    ->where('consecutive_correct', '>=', 3)->count()
                                : 0;
                            $lgPct = $lgTotal > 0 ? round(($lgSolved / $lgTotal) * 100) : 0;
                            $lgDone = $lgPct >= 100 && $lgSolved > 0;
                        @endphp
                        <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="l-row">
                            <span class="l-row-t">
                                <b>{{ $lehrgang->lehrgang }}</b>
                                <span>{{ $lgSolved }}/{{ $lgTotal }} gemeistert</span>
                                <span class="l-line {{ $lgDone ? 'is-ok' : '' }}"><i style="width: {{ $lgPct }}%;"></i></span>
                            </span>
                            <span class="l-row-v">{{ $lgPct }} %</span>
                            <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                        </a>
                    @empty
                        <a href="{{ route('lehrgaenge.index') }}" class="l-row">
                            <span class="l-row-t"><b>Lehrgänge entdecken</b><span>Spezialisiere dein Wissen</span></span>
                            <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                        </a>
                    @endforelse
                </div>
            </section>

            {{-- Ortsverband --}}
            @if($userOV && $ovCardData)
                <section class="l-o10" aria-labelledby="l-ov">
                    @if($ovCardData['type'] === 'ausbilder')
                        <div class="l-sec-h">
                            <h2 id="l-ov" class="l-h2">{{ $userOV->name }}</h2>
                            <span class="l-small">Ausbilder</span>
                        </div>
                        <div class="l-group l-ovstats">
                            <dl class="l-figs">
                                <div><dt>Mitglieder</dt><dd>{{ $ovCardData['stats']['total_members'] ?? 0 }}</dd></div>
                                <div><dt>Aktiv (7 Tage)</dt><dd>{{ $ovCardData['stats']['active_members_7days'] ?? 0 }}</dd></div>
                                <div><dt>Ø Fortschritt</dt><dd>{{ $ovCardData['stats']['avg_theory'] ?? 0 }} %</dd></div>
                            </dl>
                            <a href="{{ route('ortsverband.index') }}" class="l-btn l-btn--sm l-btn--soft">Verwalten</a>
                        </div>
                    @else
                        <div class="l-sec-h">
                            <h2 id="l-ov" class="l-h2">{{ $userOV->name }}</h2>
                            @if($ovCardData['totalEnrolled'] > 3)
                                <a href="{{ route('ortsverband.show', $userOV) }}" class="l-link">Alle anzeigen</a>
                            @else
                                <span class="l-small">Ortsverband</span>
                            @endif
                        </div>
                        @if($ovCardData['lernpools']->isNotEmpty())
                            <div class="l-group">
                                @foreach($ovCardData['lernpools'] as $lp)
                                    <a href="{{ route('ortsverband.lernpools.show', [$userOV, $lp['id']]) }}" class="l-row">
                                        <span class="l-row-t">
                                            <b>{{ $lp['name'] }}</b>
                                            <span class="l-line {{ $lp['progress'] >= 100 ? 'is-ok' : '' }}"><i style="width: {{ $lp['progress'] }}%;"></i></span>
                                        </span>
                                        <span class="l-row-v">{{ $lp['progress'] }} %</span>
                                        <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        @if($ovCardData['ranking'] && $ovCardData['ranking']->isNotEmpty())
                            <ol class="l-rank" aria-label="Ranking im Ortsverband">
                                @foreach($ovCardData['ranking'] as $idx => $member)
                                    @php $isMe = $member['user']->id === $user->id; @endphp
                                    <li class="{{ $isMe ? 'is-me' : '' }}">
                                        <span>{{ $idx + 1 }}.</span>
                                        <span>{{ $isMe ? 'Du' : \Illuminate\Support\Str::limit($member['user']->name, 18) }}</span>
                                        <span>{{ number_format($member['points'] ?? 0, 0, ',', '.') }} Pkt</span>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    @endif
                </section>
            @endif
        </div>

        {{-- ═══ Seitenspalte ═══ --}}
        <div class="l-col">
            {{-- Prüfung --}}
            <section class="l-o3" aria-labelledby="l-cd" data-tour-step="countdown">
                <h2 id="l-cd" class="l-group-h">Prüfung</h2>
                @if($examCountdown)
                    @php
                        $cdTarget = $examCountdown['dailyTarget'];
                        $cdToday = $examCountdown['todayAnswered'];
                        $cdDone = $cdToday >= $cdTarget;
                    @endphp
                    <div class="l-group l-cd">
                        <p class="l-big">{{ $examCountdown['daysLeft'] }} Tag{{ $examCountdown['daysLeft'] != 1 ? 'e' : '' }}</p>
                        <p class="l-small">bis zur Prüfung</p>
                        @if($cdTarget > 0)
                            @if($cdTarget <= 24)
                                <span class="l-segs" aria-hidden="true">
                                    @for($k = 0; $k < $cdTarget; $k++)<i class="{{ $k < $cdToday ? 'is-on' : '' }}"></i>@endfor
                                </span>
                            @else
                                <span class="l-line" style="margin: 14px 0 8px;"><i style="width: {{ min(100, round($cdToday / $cdTarget * 100)) }}%;"></i></span>
                            @endif
                            <p class="l-small">
                                @if($cdDone)
                                    Tagesziel geschafft: {{ $cdToday }} von {{ $cdTarget }} Fragen.
                                @else
                                    Tagesziel {{ $cdTarget }} Fragen, heute {{ $cdToday }}. Noch {{ $cdTarget - $cdToday }} übrig.
                                @endif
                            </p>
                        @endif
                    </div>
                @else
                    <div class="l-group">
                        <a href="{{ route('einstellungen') }}" class="l-row">
                            <span class="l-row-t"><b>Prüfungsdatum eintragen</b><span>Erhalte ein tägliches Lernziel</span></span>
                            <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                        </a>
                    </div>
                @endif
            </section>

            {{-- Wiederholung: was heute fällig ist, was kommt, und wie es funktioniert --}}
            @if($spacedRepetitionDue > 0 || $srSchedule->isNotEmpty())
                <section class="l-o4" aria-labelledby="l-wh">
                    <h2 id="l-wh" class="l-group-h">Wiederholung</h2>
                    <div class="l-group">
                        @if($spacedRepetitionDue > 0)
                            <a href="{{ route('practice.spaced-repetition') }}" class="l-row">
                                <span class="l-row-t"><b>Heute fällig</b><span>Jetzt wiederholen</span></span>
                                <b class="l-count">{{ $spacedRepetitionDue }}</b>
                            </a>
                        @else
                            <div class="l-row is-done">
                                <span class="l-row-t"><b>Heute</b><span>Alles wiederholt</span></span>
                                <span class="l-row-v">0</span>
                            </div>
                        @endif
                        @foreach($srSchedule as $sr)
                            <div class="l-row">
                                <span class="l-row-t"><b>{{ ucfirst(\App\Helpers\LearnText::when((int) $sr['days_from_now'])) }}</b></span>
                                <span class="l-row-v">{{ $sr['count'] }} {{ $sr['count'] == 1 ? 'Frage' : 'Fragen' }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="l-small l-foot">{{ \App\Helpers\LearnText::SR_RULE }}</p>
                </section>
            @endif

            {{-- Für dich --}}
            @if($forYouCount > 0)
                <section class="l-o6" aria-labelledby="l-fd">
                    <h2 id="l-fd" class="l-group-h">Für dich</h2>
                    <div class="l-group">
                        @if($unopenedLootboxes > 0)
                            <a href="{{ route('gamification.lootboxes') }}" class="l-row">
                                <span class="l-row-t"><b>{{ $unopenedLootboxes }} Belohnung{{ $unopenedLootboxes > 1 ? 'en' : '' }}</b><span>Wochenbelohnung wartet</span></span>
                                <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                            </a>
                        @endif
                        @if($showSurveyNudge)
                            <a href="{{ route('umfrage.index') }}" class="l-row">
                                <span class="l-row-t"><b>Deine Meinung zählt</b><span>Kurze Umfrage (2 Min.)</span></span>
                                <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                            </a>
                        @endif
                        @if($user->extras_enabled)
                            <a href="{{ route('practice.standard-only') }}" class="l-row">
                                <span class="l-row-t"><b>Nur Standardfragen</b><span>Ohne Zusatz-Fragen üben</span></span>
                                <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('practice.extras-only') }}" class="l-row">
                                <span class="l-row-t"><b>Nur Zusatz-Fragen</b><span>Zuordnungen &amp; Bilder</span></span>
                                <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Stand --}}
            <section class="l-o7" aria-labelledby="l-st">
                <h2 id="l-st" class="l-group-h">Stand</h2>
                <div class="l-group">
                    @php $streakDays = $user->streak_days ?? 0; @endphp
                    <div class="l-row {{ isset($streakAtRisk) && $streakAtRisk ? 'is-risk' : '' }}" data-tour-step="streak">
                        <span class="l-row-t">
                            <b>Streak</b>
                            @if($streakDays >= 1)
                                <span>{{ $dailyStreakGoal > 0 ? 'heute '.min($todayAnswered, $dailyStreakGoal).' von '.$dailyStreakGoal : 'Keine Fragen fällig' }}</span>
                            @endif
                        </span>
                        <span class="l-row-v">{{ $streakDays }} {{ $streakDays == 1 ? 'Tag' : 'Tage' }}</span>
                    </div>
                    <div class="l-row" data-tour-step="achievements">
                        <span class="l-row-t"><b>Gelöst</b></span>
                        <span class="l-row-v">{{ $solvedTotal }}</span>
                    </div>
                    <a href="{{ route('gamification.leaderboard') }}" class="l-row">
                        <span class="l-row-t"><b>{{ $leagueInfo['name'] }}-Liga</b></span>
                        <span class="l-row-v">{{ $leagueRank ? '#'.$leagueRank : 'Teilnehmen' }}</span>
                        <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                    </a>
                    @if($extrasEnabled)
                        <a href="{{ route('practice.extras-only') }}" class="l-row">
                            <span class="l-row-t"><b>Zusatz</b></span>
                            <span class="l-row-v">{{ $extraMastered }}/{{ $extraTotal }}</span>
                            <i class="bi bi-chevron-right l-chev" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>

{{-- Liga-Einladung --}}
@if(!$user->leaderboard_banner_dismissed && !$user->leaderboard_consent)
    <div class="l-dlg-wrap" id="leaderboard-modal">
        <div class="l-dlg" role="dialog" aria-modal="true" aria-labelledby="l-liga-t">
            <h2 id="l-liga-t">Liga</h2>
            <p>Vergleiche dich mit anderen. Dein Name und deine Punkte werden in der Liga angezeigt. Das kannst du jederzeit in den Einstellungen ändern.</p>
            <div class="l-dlg-acts">
                <form action="{{ route('profile.dismiss.leaderboard.banner') }}" method="POST" id="lb-decline-form">
                    @csrf
                    <input type="hidden" name="accept" value="0">
                    <button type="submit" class="l-btn l-btn--soft">Nein</button>
                </form>
                <form action="{{ route('profile.dismiss.leaderboard.banner') }}" method="POST">
                    @csrf
                    <input type="hidden" name="accept" value="1">
                    <button type="submit" class="l-btn">Ja, mitmachen</button>
                </form>
            </div>
        </div>
    </div>
@endif

<x-onboarding-tour />

@push('scripts')
<script>
function dismissLeaderboardModal(accept) {
    const modal = document.getElementById('leaderboard-modal');
    if (modal) modal.remove();
    if (accept === false) {
        document.getElementById('lb-decline-form').submit();
    }
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('leaderboard-modal')) dismissLeaderboardModal(false);
});
</script>
@endpush

@endsection
