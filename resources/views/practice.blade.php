@extends(isset($isLanding) && $isLanding ? 'layouts.public' : 'layouts.app')
@section('title', isset($isLanding) && $isLanding ? 'Anonym üben | THW-Trainer.de' : 'THW Theorie üben - Interaktive Fragen mit Lernfortschritt')
@section('description', isset($isLanding) && $isLanding ? 'Übe THW Theoriefragen sofort und anonym ohne Anmeldung. Kostenlos verfügbar!' : 'Übe THW Theoriefragen mit deinem persönlichen Lernfortschritt. Markiere schwierige Fragen, filtere nach Lernabschnitten und verfolge deinen Erfolg. Kostenlos und effektiv!')

@section('content')
@php
    $isGuest = ($context ?? 'global') === 'guest';
    $ctx = $context ?? 'global';

    // Hole Antwort-Details aus View-Variable oder Session (Backward-Compat)
    $answerResultFromView = isset($answerResult);
    $answerResult = $answerResult ?? session('answer_result');
    $hasAnswerResult = $answerResult && isset($answerResult['question_id']) && isset($question) && $question && $answerResult['question_id'] == $question->id;

    // Hole Gamification Result aus View-Variable oder Session (Backward-Compat)
    $gamificationResult = $gamificationResult ?? ($isGuest ? null : session('gamification_result'));

    if ($hasAnswerResult) {
        $isCorrect = $answerResult['is_correct'];
        // Bei Matching-Fragen liegt die Antwort unter 'assignments', sonst unter 'user_answer'.
        $userAnswer = isset($answerResult['user_answer'])
            ? collect($answerResult['user_answer'])
            : null;
        $questionProgress = (object)['consecutive_correct' => $answerResult['question_progress'] ?? 0];

        // Lösche Sessions nur wenn alte Session-basierte Approach genutzt wird
        if (!$answerResultFromView) {
            session()->forget(['answer_result', 'gamification_result']);
        }
    } else {
        $isCorrect = $isCorrect ?? null;
        $userAnswer = $userAnswer ?? null;
        $questionProgress = null;
    }
@endphp

@if(isset($isLanding) && $isLanding)
    {{-- Gäste: Startseiten-Layout mit Topleiste, dazu das Stylesheet der Lernseiten --}}
    @push('head')
        @vite('resources/css/learn-guest.css')
    @endpush
@endif

<div class="l-focus-root" id="practiceContainer">
    @if(isset($registrationPrompt))
        {{-- Hinweis für Gäste: Registrieren oder weiter üben --}}
        <div class="l-dlg-wrap">
            <div class="l-dlg" role="dialog" aria-modal="true" aria-labelledby="l-guest-t">
                <p class="l-eyebrow">{{ \App\Helpers\LearnText::wd($registrationPrompt['benefit']) }}</p>
                <h2 id="l-guest-t">{{ $registrationPrompt['title'] }}</h2>
                <p>{{ $registrationPrompt['description'] }}</p>
                <p class="l-small">{{ $questionsAnswered ?? 0 }} Fragen beantwortet</p>
                <div class="l-dlg-acts l-dlg-acts--stack">
                    <a href="{{ route('register') }}" class="l-btn">Kostenlos registrieren</a>
                    <a href="{{ ($showUrl ?? route('landing.guest.practice.index')) . '?continue=1' }}" class="l-btn l-btn--soft">Weiter üben</a>
                </div>
            </div>
        </div>
    @endif

    @if($question)
        @auth
            @if($ctx === 'global')
                @php
                    $user = Auth::user();
                    $bookmarked = is_array($user->bookmarked_questions ?? null)
                        ? $user->bookmarked_questions
                        : json_decode($user->bookmarked_questions ?? '[]', true);
                    $isBookmarked = in_array($question->id, $bookmarked);
                @endphp
            @else
                @php $isBookmarked = false; @endphp
            @endif
        @endauth
        @guest
            @php $isBookmarked = false; @endphp
        @endguest

        @php
            $isExtra = $question instanceof \App\Models\ExtraQuestion;
            $qType = $isExtra ? ($question->typ ?? 'multiple_choice') : 'multiple_choice';
            $typeLabels = ['matching' => 'Zuordnung', 'pair_matching' => 'Wortpaare', 'image_name' => 'Bild benennen', 'image_select' => 'Bild auswählen'];

            $eyebrowText = $isGuest
                ? 'Anonym üben'
                : (isset($mode)
                    ? [
                        'unsolved' => 'Ungelöste Fragen',
                        'failed' => 'Fehlerwiederholung',
                        'section' => 'Lernabschnitt ' . session('practice_parameter'),
                        'search' => 'Suche: "' . session('practice_parameter') . '"',
                        'bookmarked' => 'Gespeicherte Fragen',
                        'spaced_repetition' => 'Wiederholung',
                    ][$mode] ?? 'Alle Fragen'
                    : ($contextLabel ?? 'Theorie üben'));
            $qCurrent = $currentInMode ?? (($progress ?? 0) + 1);
            $qTotal   = $totalInMode   ?? ($total ?? 0);
            $laStr = ($question->lernabschnitt ?? '-') . (isset($question->nummer) ? '.' . $question->nummer : '');
            $backLabel = $isGuest ? 'Übungsmenü' : ($ctx === 'global' ? 'Theorie lernen' : 'Zurück');
            $hasDifficulty = isset($difficultyInfo) && ($difficultyInfo['level'] ?? 'unknown') !== 'unknown';
        @endphp

        {{-- Kopfzeile: zurück, Modus, Werkzeuge; darunter der Fortschritt (gemeistert) --}}
        <header class="l-fbar">
            <div class="l-fbar-in">
                <a href="{{ $menuUrl ?? route('practice.menu') }}" class="l-back"><i class="bi bi-chevron-left" aria-hidden="true"></i><span>{{ $backLabel }}</span></a>
                <p class="l-fbar-t">{{ $isExtra && !isset($mode) ? 'Zusatz-Fragen' : $eyebrowText }}</p>
                <span class="l-tools">
                    @auth
                        <button type="button" onclick="openReportModal()" class="l-tool" title="Fehler melden">
                            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i><span>Fehler melden</span>
                        </button>
                        @if($ctx === 'global')
                            <button type="button" class="l-tool {{ $isBookmarked ? 'active' : '' }}" id="bookmarkBtn"
                                    aria-pressed="{{ $isBookmarked ? 'true' : 'false' }}"
                                    title="{{ $isBookmarked ? 'Aus Lesezeichen entfernen' : 'Zu Lesezeichen hinzufügen' }}"
                                    aria-label="{{ $isBookmarked ? 'Aus Lesezeichen entfernen' : 'Zu Lesezeichen hinzufügen' }}"
                                    onclick="toggleBookmark({{ $question->id }}, {{ $isBookmarked ? 'true' : 'false' }})">
                                <i class="bi {{ $isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark' }}" aria-hidden="true"></i>
                            </button>
                        @endif
                    @endauth
                </span>
            </div>
            <div class="l-fprog" title="Gemeisterte Fragen: 3× in Folge richtig beantwortet"><i style="width: {{ $progressPercent ?? 0 }}%;"></i></div>
        </header>

        <main class="l-focus {{ isset($isCorrect) ? 'is-checked' : '' }}" id="questionContent">
            <p class="l-qmeta">
                @if($isExtra)
                    <span>Zusatz-Frage, LA {{ $question->lernabschnitt ?? '-' }}, {{ $typeLabels[$qType] ?? '' }}</span>
                @else
                    <span>Frage {{ $qCurrent }} von {{ $qTotal }}, LA {{ $laStr }}</span>
                @endif
                @if(isset($isSpacedRepetition) && $isSpacedRepetition)
                    <span class="l-sr">Wiederholung</span>
                @endif
                @if($hasDifficulty)
                    <span class="l-diff is-{{ $difficultyInfo['level'] }}">{{ $difficultyInfo['label'] }}@if($difficultyInfo['percent'] !== null), {{ $difficultyInfo['percent'] }} % Fehlerquote @endif</span>
                @endif
                <span class="l-id">ID {{ $question->id }}</span>
            </p>
            <h1 class="l-qtext">{{ $question->frage }}</h1>

            <form method="POST" action="{{ $submitUrl ?? route('practice.submit') }}" id="practiceForm">
                @csrf
                <input type="hidden" name="question_id" value="{{ $question->id }}">
                <input type="hidden" name="question_kind" value="{{ $isExtra ? 'extra' : 'official' }}">
                <input type="hidden" name="answer_time_ms" id="answerTimeMs" value="0">

                @if(!$isExtra || $qType === 'multiple_choice')
                    @include('practice.partials.question-multiple-choice', [
                        'question' => $question,
                        'isCorrect' => $isCorrect ?? null,
                        'answerResult' => $answerResult ?? null,
                        'userAnswer' => $userAnswer ?? null,
                    ])
                @elseif($qType === 'matching')
                    @include('practice.partials.question-matching', [
                        'question' => $question,
                        'isCorrect' => $isCorrect ?? null,
                        'answerResult' => $answerResult ?? null,
                        'isSpacedRepetition' => $isSpacedRepetition ?? false,
                        'difficultyInfo' => $difficultyInfo ?? null,
                        'inShell' => true,
                    ])
                @elseif($qType === 'pair_matching')
                    @include('practice.partials.question-pair-matching', [
                        'question' => $question,
                        'isCorrect' => $isCorrect ?? null,
                        'answerResult' => $answerResult ?? null,
                        'isSpacedRepetition' => $isSpacedRepetition ?? false,
                        'difficultyInfo' => $difficultyInfo ?? null,
                        'inShell' => true,
                    ])
                @elseif($qType === 'image_name')
                    @include('practice.partials.question-image-name', [
                        'question' => $question,
                        'isCorrect' => $isCorrect ?? null,
                        'answerResult' => $answerResult ?? null,
                        'userAnswer' => $userAnswer ?? null,
                        'isSpacedRepetition' => $isSpacedRepetition ?? false,
                        'difficultyInfo' => $difficultyInfo ?? null,
                        'inShell' => true,
                    ])
                @elseif($qType === 'image_select')
                    @include('practice.partials.question-image-select', [
                        'question' => $question,
                        'isCorrect' => $isCorrect ?? null,
                        'answerResult' => $answerResult ?? null,
                        'userAnswer' => $userAnswer ?? null,
                        'isSpacedRepetition' => $isSpacedRepetition ?? false,
                        'difficultyInfo' => $difficultyInfo ?? null,
                        'inShell' => true,
                    ])
                @endif

                {{-- Fortschrittsstufen: 1× / 2× richtig, gemeistert = 3× in Folge --}}
                <p class="l-tiers">
                    @isset($progressOnce)<span>1× richtig <b>{{ $progressOnce }}</b></span>@endisset
                    @isset($progressTwice)<span>2× richtig <b>{{ $progressTwice }}</b></span>@endisset
                    <span title="Gemeistert = 3× hintereinander richtig beantwortet">Gemeistert <b>{{ $progress ?? 0 }}/{{ $total ?? 0 }}</b> ({{ $progressPercent ?? 0 }} %)</span>
                </p>

                @if(!isset($isCorrect))
                    <div class="l-bar">
                        <div class="l-acts">
                            <a href="{{ $summaryUrl ?? route('practice.summary') }}" class="l-end">Lernen beenden</a>
                            <button type="submit" id="submitBtn" class="l-btn l-btn--lg" disabled>Antwort absenden</button>
                        </div>
                    </div>
                @endif
            </form>
        </main>

        {{-- Auflösung als Sheet: Ergebnis, wann die Frage wiederkommt, Lösungsweg, weiter --}}
        @if(isset($isCorrect))
            @php
                $verdicts = [
                    'matching' => ['Alle richtig zugeordnet', 'Zuordnung prüfen'],
                    'pair_matching' => ['Alle Paare korrekt', 'Einige Paare passen nicht'],
                    'image_name' => ['Richtig benannt', 'Leider falsch'],
                    'image_select' => ['Richtig ausgewählt', 'Auswahl prüfen'],
                ];
                $celebrations = ['Grandios!', 'Fantastisch!', 'Super!', 'Stark!', 'Mega!', 'Klasse!', 'Volltreffer!', 'Genial!'];
                $verdict = isset($verdicts[$qType])
                    ? $verdicts[$qType][$isCorrect ? 0 : 1]
                    : ($isCorrect ? $celebrations[$question->id % count($celebrations)] : 'Falsch');

                $pointsAwarded = ($isCorrect && $gamificationResult && isset($gamificationResult['points_awarded'])) ? (int) $gamificationResult['points_awarded'] : 0;
                $masteryThreshold = \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;
                $showMastery = !$isGuest && !$isExtra && $questionProgress !== null;
                $streakAfter = $showMastery ? min($masteryThreshold, (int) $questionProgress->consecutive_correct) : 0;

                // Wann kommt die Frage wieder? Liest den gespeicherten Termin (SpacedRepetitionService), ändert nichts.
                $srLine = null;
                if (!$isGuest && $ctx === 'global' && !$isExtra && auth()->check()) {
                    $srProgress = \App\Models\UserQuestionProgress::where('user_id', auth()->id())
                        ->where('question_id', $question->id)
                        ->first();
                    if ($srProgress) {
                        if (!$srProgress->next_review_at && $srProgress->isMastered()) {
                            $srLine = 'Gemeistert. Diese Frage muss nicht mehr wiederholt werden.';
                        } elseif ($srProgress->next_review_at) {
                            $srDays = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($srProgress->next_review_at)->startOfDay(), false);
                            $srLine = 'Kommt ' . \App\Helpers\LearnText::when($srDays) . ' wieder. '
                                . ($isCorrect
                                    ? 'Noch ' . max(1, $masteryThreshold - (int) $srProgress->consecutive_correct) . '× richtig in Folge, dann gemeistert.'
                                    : 'Der Zähler beginnt neu.');
                        }
                    }
                }
                $explanation = trim($question->loesungsweg ?? '');
            @endphp
            <section class="l-sheet {{ $isCorrect ? 'l-sheet--ok' : 'l-sheet--bad' }}" id="l-sheet" aria-live="polite">
                <div class="l-sheet-in">
                    <div class="l-sheet-h">
                        <span class="l-verdict"><i class="bi {{ $isCorrect ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" aria-hidden="true"></i></span>
                        <p>
                            <b>{{ $verdict }}</b>
                            @if($showMastery)
                                <span class="l-mastery" role="img" aria-label="{{ $streakAfter }} von {{ $masteryThreshold }} richtig in Folge">
                                    @for($k = 0; $k < $masteryThreshold; $k++)<i class="{{ $k < $streakAfter ? 'is-on' : '' }}"></i>@endfor
                                </span>
                            @endif
                        </p>
                        @if($pointsAwarded > 0 && !$isGuest)
                            <span class="l-gain">+{{ $pointsAwarded }} XP</span>
                        @endif
                    </div>
                    @if($srLine)
                        <p class="l-srline">{{ $srLine }}</p>
                    @endif
                    @if($explanation !== '')
                        <div class="l-expl">
                            <p class="l-small">Lösungsweg</p>
                            <p>{!! nl2br(e($explanation)) !!}</p>
                        </div>
                    @endif
                    <div class="l-acts">
                        <a href="{{ $summaryUrl ?? route('practice.summary') }}" class="l-end">Lernen beenden</a>
                        <a href="{{ $showUrl ?? route('practice.index') }}" class="l-btn l-btn--lg">Nächste Frage</a>
                    </div>
                </div>
            </section>
        @endif

        {{-- Lightbox für Bild-Fragen (image_name + image_select) --}}
        @include('practice.partials.image-lightbox')

        @auth
            {{-- Fehler melden --}}
            <div id="reportModal" class="l-dlg-wrap" style="display: none;" onclick="if(event.target===this)closeReportModal()">
                <div class="l-dlg" role="dialog" aria-modal="true" aria-labelledby="l-report-t">
                    <h2 id="l-report-t">Fehler melden</h2>
                    <p>Frage {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</p>
                    <textarea id="reportMessage" rows="3" maxlength="500"
                        aria-label="Beschreibung des Fehlers"
                        placeholder="Optionale Beschreibung des Fehlers (max. 500 Zeichen)"></textarea>
                    <p id="reportFeedback" class="l-fb" style="display: none;"></p>
                    <div class="l-dlg-acts">
                        <button type="button" onclick="closeReportModal()" class="l-btn l-btn--soft">Abbrechen</button>
                        <button type="button" onclick="submitReport()" id="reportSubmitBtn" class="l-btn">Melden</button>
                    </div>
                </div>
            </div>
        @endauth

        {{-- Fullscreen-Overlays (Level-Up, Achievement, Streak) werden global über
             layouts/app.blade.php gerendert. Hier NICHT nochmal rendern! --}}

        <script>
            // Answer time measurement
            (function() {
                var questionStart = Date.now();
                var form = document.getElementById('practiceForm');
                if (form) {
                    form.addEventListener('submit', function() {
                        var elapsed = Date.now() - questionStart;
                        document.getElementById('answerTimeMs').value = elapsed;
                    });
                }
            })();

            // Auswahl wird per CSS (:has(:checked)) dargestellt; Funktion bleibt für Partials bestehen
            function updateSelectionStyle(label) {}

            // Submit button state
            function updateSubmitButton() {
                var submitBtn = document.getElementById('submitBtn');
                if (!submitBtn) return;
                var checked = Array.from(document.querySelectorAll('input[name="answer[]"]')).some(function(cb) { return cb.checked; });
                submitBtn.disabled = !checked;
            }

            // Sheet-Höhe als Abstand unter der Frage, damit nichts verdeckt wird
            (function() {
                var sheet = document.getElementById('l-sheet');
                var focus = document.getElementById('questionContent');
                if (!sheet || !focus || !('ResizeObserver' in window)) return;
                new ResizeObserver(function() {
                    focus.style.setProperty('--sheet-h', sheet.offsetHeight + 'px');
                }).observe(sheet);
            })();

            // Shake on wrong answer + Floating points
            document.addEventListener('DOMContentLoaded', function() {
                @if(isset($isCorrect) && !$isCorrect)
                    var qc = document.getElementById('questionContent');
                    if (qc) {
                        qc.classList.add('l-shake');
                        setTimeout(function() { qc.classList.remove('l-shake'); }, 400);
                    }
                @endif

                @if(isset($isCorrect) && $isCorrect && $gamificationResult && isset($gamificationResult['points_awarded']) && $gamificationResult['points_awarded'] > 0)
                    setTimeout(function() {
                        if (typeof window.showFloatingPoints === 'function') {
                            window.showFloatingPoints(window.innerWidth / 2, window.innerHeight / 2 - 50, {{ $gamificationResult['points_awarded'] }});
                        }
                    }, 600);
                @endif
            });

            @auth
            // Report modal
            function openReportModal() {
                var modal = document.getElementById('reportModal');
                modal.style.display = 'grid';
                document.getElementById('reportMessage').value = '';
                document.getElementById('reportFeedback').style.display = 'none';
                document.getElementById('reportSubmitBtn').disabled = false;
                setTimeout(function() { document.getElementById('reportMessage').focus(); }, 50);
            }

            function closeReportModal() {
                document.getElementById('reportModal').style.display = 'none';
            }

            document.addEventListener('keydown', function(e) {
                var modal = document.getElementById('reportModal');
                if (e.key === 'Escape' && modal && modal.style.display !== 'none') closeReportModal();
            });

            function submitReport() {
                var btn = document.getElementById('reportSubmitBtn');
                var feedback = document.getElementById('reportFeedback');
                var message = document.getElementById('reportMessage').value.trim();

                btn.disabled = true;

                @php
                    $reportRoute = ($context ?? null) === 'lehrgang'
                        ? route('lehrgaenge.report-issue', $question->id)
                        : route('practice.report-issue', $question->id);
                @endphp
                fetch('{{ $reportRoute }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ message: message || null }),
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    feedback.style.display = 'block';
                    if (data.success) {
                        feedback.style.color = 'var(--ok)';
                        feedback.textContent = data.message;
                        setTimeout(closeReportModal, 1500);
                    } else {
                        feedback.style.color = 'var(--bad)';
                        feedback.textContent = data.error || 'Fehler beim Senden.';
                        btn.disabled = false;
                    }
                })
                .catch(function() {
                    feedback.style.display = 'block';
                    feedback.style.color = 'var(--bad)';
                    feedback.textContent = 'Verbindungsfehler. Bitte erneut versuchen.';
                    btn.disabled = false;
                });
            }

            @if($ctx === 'global')
            // Bookmark toggle
            function toggleBookmark(questionId, currentlyBookmarked) {
                var btn = document.getElementById('bookmarkBtn');
                if (!btn) return;

                var formData = new FormData();
                formData.append('question_id', questionId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                btn.style.opacity = '0.5';
                btn.disabled = true;

                fetch('{{ route("bookmarks.toggle") }}', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        var label = data.is_bookmarked ? 'Aus Lesezeichen entfernen' : 'Zu Lesezeichen hinzufügen';
                        btn.setAttribute('aria-pressed', data.is_bookmarked ? 'true' : 'false');
                        btn.setAttribute('title', label);
                        btn.setAttribute('aria-label', label);
                        btn.setAttribute('onclick', 'toggleBookmark(' + questionId + ', ' + data.is_bookmarked + ')');
                        btn.classList.toggle('active', data.is_bookmarked);
                        var icon = btn.querySelector('.bi');
                        if (icon) icon.className = 'bi ' + (data.is_bookmarked ? 'bi-bookmark-fill' : 'bi-bookmark');
                    }
                })
                .catch(function(error) {
                    console.error('Bookmark error:', error);
                })
                .finally(function() {
                    btn.style.opacity = '1';
                    btn.disabled = false;
                });
            }
            @endif
            @endauth
        </script>
    @else
        {{-- Keine Fragen mehr in diesem Modus --}}
        <header class="l-fbar">
            <div class="l-fbar-in">
                <a href="{{ $menuUrl ?? route('practice.menu') }}" class="l-back"><i class="bi bi-chevron-left" aria-hidden="true"></i><span>{{ $isGuest ? 'Übungsmenü' : (($context ?? 'global') === 'global' ? 'Theorie lernen' : 'Zurück') }}</span></a>
                <span></span>
                <span></span>
            </div>
        </header>
        <main class="l-focus">
            <div class="l-done">
                <h1 class="l-title">Geschafft!</h1>
                <p class="l-meta">Du hast alle Fragen in diesem Modus bearbeitet</p>
                <div class="l-done-acts">
                    <a href="{{ $menuUrl ?? route('practice.menu') }}" class="l-btn l-btn--lg">Zurück zum Menü</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="l-btn l-btn--lg l-btn--soft">Dashboard</a>
                    @endauth
                    @guest
                        <a href="{{ route('register') }}" class="l-btn l-btn--lg l-btn--soft">Kostenlos registrieren</a>
                    @endguest
                </div>
            </div>
        </main>
    @endif
</div>
@endsection
