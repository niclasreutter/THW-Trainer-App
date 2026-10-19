@extends('layouts.app')
@section('title', 'THW Prüfungssimulation - 40 Fragen in 30 Minuten')
@section('description', 'THW Prüfungssimulation: Teste dein Wissen mit 40 zufälligen Fragen in 30 Minuten. Realistische Prüfungsbedingungen und sofortige Auswertung. Übe jetzt kostenlos!')

@section('content')

@if(!isset($submitted))
{{-- ============================================
     PRÜFUNG: blaues Band mit allen 40 Fragen, Bedienung unten
     ============================================ --}}
<div class="l-focus-root l-exam">
    <section class="l-band">
        <span class="l-gearmark" aria-hidden="true">@include('landing.partials.gear')</span>
        <div class="l-band-top">
            <button type="button" class="l-bandbtn" onclick="handleExamExit(event)" aria-label="Prüfung verlassen">
                <i class="bi bi-x-lg" aria-hidden="true"></i><span>Verlassen</span>
            </button>
            <p class="l-band-t">Frage <b id="exam-qnum">1</b> von {{ count($fragen) }}</p>
            <span class="l-band-r"><span class="l-timer" id="exam-timer" role="timer" aria-label="Restzeit">30:00</span></span>
        </div>
        <div class="l-strip" id="exam-strip" role="group" aria-label="Fragenübersicht"></div>
        <p class="l-legend">
            <span><i class="l-ans" aria-hidden="true"></i><span id="stat-answered">0</span> beantwortet</span>
            <span><i class="l-open" aria-hidden="true"></i><span id="stat-open">{{ count($fragen) }}</span> offen</span>
            <span><i class="l-mark" aria-hidden="true"></i><span id="stat-marked">0</span> markiert</span>
        </p>
    </section>

    <div class="l-focus">
        <form id="exam-form" method="POST" action="{{ route('exam.submit') }}">
            @csrf
            <input type="hidden" name="exam_token" value="{{ $examToken }}">

            @foreach($fragen as $index => $frage)
                <input type="hidden" name="fragen_ids[]" value="{{ $frage->id }}">
                <input type="hidden" name="answer_mappings[{{ $index }}]" value="{{ json_encode($answerMappings[$index] ?? []) }}">

                <div class="exam-slide" data-question="{{ $index }}" @if($index === 0) style="display: block;" @endif>
                    <p class="l-qmeta">
                        <span>LA {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}</span>
                        <span class="l-diff is-medium" data-marked-tag hidden>Markiert</span>
                    </p>
                    <h1 class="l-qtext">{{ $frage->frage }}</h1>

                    <div class="l-opts">
                        @php
                            $mapping = $answerMappings[$index] ?? [0 => 'A', 1 => 'B', 2 => 'C'];
                        @endphp
                        @foreach($mapping as $position => $letter)
                            @php
                                $answerField = 'antwort_' . strtolower($letter);
                            @endphp
                            <button type="button" class="l-opt l-opt--sq exam-answer" data-index="{{ $index }}" data-position="{{ $position }}" aria-pressed="false" onclick="toggleAnswer({{ $index }}, {{ $position }})">
                                <span class="t">{{ $frage->$answerField }}</span>
                                <span class="l-check"><i class="bi bi-check-lg" aria-hidden="true"></i></span>
                            </button>
                            <input type="checkbox" name="answer[{{ $index }}][]" value="{{ $position }}" hidden>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </form>
        <p class="l-small l-rules">40 Fragen, 30 Minuten, 80 % zum Bestehen</p>
    </div>

    <div class="l-bar">
        <div class="l-acts l-acts--three">
            <button type="button" class="l-btn l-btn--lg l-btn--soft" onclick="prevQuestion()" id="btn-back">Zurück</button>
            <button type="button" class="l-btn l-btn--lg l-btn--soft l-markbtn" onclick="toggleMark()" id="btn-mark" aria-pressed="false">Markieren</button>
            <button type="button" class="l-btn l-btn--lg" onclick="nextQuestion()" id="btn-next">Weiter</button>
        </div>
    </div>

    {{-- Rückfrage (Verlassen, Abgeben) --}}
    <div class="l-dlg-wrap" id="exam-dlg" hidden>
        <div class="l-dlg" role="alertdialog" aria-modal="true" aria-labelledby="exam-dlg-t" aria-describedby="exam-dlg-p">
            <h2 id="exam-dlg-t"></h2>
            <p id="exam-dlg-p"></p>
            <div class="l-dlg-acts">
                <button type="button" class="l-btn l-btn--soft" id="exam-dlg-no"></button>
                <button type="button" class="l-btn" id="exam-dlg-yes"></button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ============================================
// EXAM - State Management & Core Logic
// ============================================

const TOTAL_QUESTIONS = {{ count($fragen) }};

// State
let currentQuestion = 0;
let answers = new Array(TOTAL_QUESTIONS).fill(false);
let marked = new Array(TOTAL_QUESTIONS).fill(false);
let timeLeft = {{ $timeLeft }};
let examSubmitting = false;

// DOM refs
const examForm = document.getElementById('exam-form');
const timerEl = document.getElementById('exam-timer');
const qnumEl = document.getElementById('exam-qnum');
const strip = document.getElementById('exam-strip');
const btnBack = document.getElementById('btn-back');
const btnMark = document.getElementById('btn-mark');
const btnNext = document.getElementById('btn-next');
const statAnswered = document.getElementById('stat-answered');
const statOpen = document.getElementById('stat-open');
const statMarked = document.getElementById('stat-marked');

// ── Timer (Warnung unter 10, kritisch unter 5 Minuten) ──
function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
}

function updateTimer() {
    if (timeLeft <= 0) {
        submitExam();
        return;
    }
    timeLeft--;
    if (timerEl) {
        timerEl.textContent = formatTime(timeLeft);
        timerEl.classList.toggle('warning', timeLeft < 600 && timeLeft >= 300);
        timerEl.classList.toggle('critical', timeLeft < 300);
    }
}

const timerInterval = setInterval(updateTimer, 1000);

// ── Fragenleiste: alle Fragen auf einen Blick ──
for (let i = 0; i < TOTAL_QUESTIONS; i++) {
    const seg = document.createElement('button');
    seg.type = 'button';
    seg.addEventListener('click', function() { showQuestion(i); });
    strip.appendChild(seg);
}

function updateStrip() {
    Array.prototype.forEach.call(strip.children, function(seg, i) {
        seg.className = i === currentQuestion ? 'is-current' : (marked[i] ? 'is-marked' : (answers[i] ? 'is-answered' : ''));
        if (i === currentQuestion) seg.setAttribute('aria-current', 'step'); else seg.removeAttribute('aria-current');
        seg.setAttribute('aria-label', 'Frage ' + (i + 1) + (marked[i] ? ', markiert' : '') + (answers[i] ? ', beantwortet' : ''));
    });
}

// ── Question Navigation ──────────────────
function showQuestion(index) {
    if (index < 0 || index >= TOTAL_QUESTIONS) return;
    currentQuestion = index;

    document.querySelectorAll('.exam-slide').forEach(s => s.style.display = 'none');
    const slide = document.querySelector('.exam-slide[data-question="' + index + '"]');
    if (slide) slide.style.display = 'block';

    if (qnumEl) qnumEl.textContent = index + 1;
    if (btnBack) btnBack.disabled = index === 0;
    paintMark();

    if (btnNext) btnNext.textContent = index === TOTAL_QUESTIONS - 1 ? 'Abgeben' : 'Weiter';

    updateStrip();
    updateStats();
    window.scrollTo(0, 0);
}

function nextQuestion() {
    if (currentQuestion === TOTAL_QUESTIONS - 1) {
        examConfirm(
            'Prüfung abgeben?',
            'Du hast ' + answers.filter(Boolean).length + ' von ' + TOTAL_QUESTIONS + ' Fragen beantwortet.',
            'Abgeben', 'Weiter prüfen', submitExam
        );
        return;
    }
    showQuestion(currentQuestion + 1);
}

function prevQuestion() {
    showQuestion(currentQuestion - 1);
}

// ── Answer Handling ──────────────────────
function toggleAnswer(questionIndex, position) {
    const slide = document.querySelector('.exam-slide[data-question="' + questionIndex + '"]');
    const btn = slide.querySelector('.exam-answer[data-position="' + position + '"]');
    const checkbox = slide.querySelector('input[type="checkbox"][value="' + position + '"]');

    checkbox.checked = !checkbox.checked;
    btn.setAttribute('aria-pressed', checkbox.checked ? 'true' : 'false');

    answers[questionIndex] = slide.querySelectorAll('input[type="checkbox"]:checked').length > 0;

    updateStrip();
    updateStats();
}

// ── Mark Handling ──────────────────────
function paintMark() {
    const on = marked[currentQuestion];
    if (btnMark) {
        btnMark.classList.toggle('active', on);
        btnMark.setAttribute('aria-pressed', on ? 'true' : 'false');
        btnMark.textContent = on ? 'Markiert' : 'Markieren';
    }
    const tag = document.querySelector('.exam-slide[data-question="' + currentQuestion + '"] [data-marked-tag]');
    if (tag) tag.hidden = !on;
}

function toggleMark() {
    marked[currentQuestion] = !marked[currentQuestion];
    paintMark();
    updateStrip();
    updateStats();
}

// ── Stats Update ──────────────────────
function updateStats() {
    const answeredCount = answers.filter(Boolean).length;
    if (statAnswered) statAnswered.textContent = answeredCount;
    if (statOpen) statOpen.textContent = TOTAL_QUESTIONS - answeredCount;
    if (statMarked) statMarked.textContent = marked.filter(Boolean).length;
}

// ── Submit ──────────────────────────
function submitExam() {
    if (examSubmitting) return;
    examSubmitting = true;
    clearInterval(timerInterval);
    examForm.submit();
}

// ── Rückfrage im Seitenstil statt Browser-Dialog ──
const examDlg = document.getElementById('exam-dlg');
function closeExamDlg() { examDlg.hidden = true; }
function examConfirm(title, text, yesLabel, noLabel, onYes) {
    document.getElementById('exam-dlg-t').textContent = title;
    document.getElementById('exam-dlg-p').textContent = text;
    const yes = document.getElementById('exam-dlg-yes');
    const no = document.getElementById('exam-dlg-no');
    yes.textContent = yesLabel;
    no.textContent = noLabel;
    yes.onclick = function() { closeExamDlg(); onYes(); };
    no.onclick = closeExamDlg;
    examDlg.hidden = false;
    no.focus();
}
examDlg.addEventListener('click', function(e) { if (e.target === examDlg) closeExamDlg(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && !examDlg.hidden) closeExamDlg(); });

// ── Exit Protection ──────────────────
function handleExamExit(e) {
    e.preventDefault();
    examConfirm('Prüfung wirklich abbrechen?', 'Dein Fortschritt geht verloren.', 'Prüfung abbrechen', 'Weiter prüfen', function() {
        examSubmitting = true; // kein zweiter Browser-Hinweis beim Verlassen
        window.location.href = '/';
    });
}

// Prevent accidental navigation
window.addEventListener('beforeunload', function(e) {
    if (!examSubmitting) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Prevent back button
history.pushState(null, '', location.href);
window.addEventListener('popstate', function() {
    history.pushState(null, '', location.href);
});

// ── Initialize ──────────────────────
showQuestion(0);
updateTimer();
</script>
@endpush

@else
{{-- ============================================
     ERGEBNIS: Balken mit 80-%-Marke, Fragenleiste, Nachbesprechung
     ============================================ --}}
@php
    $examUser = Auth::user();
    $examBookmarked = $examUser
        ? (is_array($examUser->bookmarked_questions ?? null)
            ? $examUser->bookmarked_questions
            : json_decode($examUser->bookmarked_questions ?? '[]', true))
        : [];
    $wrongQuestionIds = collect($results)
        ->filter(fn($r) => !$r['isCorrect'])
        ->map(fn($r) => $r['frage']->id)
        ->values()
        ->all();
    $wrongNotYetBookmarked = array_values(array_diff($wrongQuestionIds, $examBookmarked));

    $percentage = $total > 0 ? round(($correctCount / $total) * 100) : 0;
    $wrongCount = $total - $correctCount;

    $sectionResults = [];
    foreach ($results as $r) {
        $la = $r['frage']->lernabschnitt;
        if (!isset($sectionResults[$la])) {
            $sectionResults[$la] = ['correct' => 0, 'total' => 0];
        }
        $sectionResults[$la]['total']++;
        if ($r['isCorrect']) $sectionResults[$la]['correct']++;
    }
    $weakSections = collect($sectionResults)
        ->map(fn($v, $k) => ['la' => $k, 'correct' => $v['correct'], 'total' => $v['total'], 'pct' => $v['total'] > 0 ? round($v['correct'] / $v['total'] * 100) : 100])
        ->filter(fn($v) => $v['pct'] < 75)
        ->sortBy('pct');
@endphp
<div class="l-focus-root l-exam" id="exam-result-container">
    {{-- ── Auswertung ── --}}
    <div id="result-summary">
        <section class="l-band l-band--result {{ $passed ? 'is-pass' : 'is-fail' }}">
            <span class="l-gearmark" aria-hidden="true">@include('landing.partials.gear')</span>
            <div class="l-band-top">
                <a href="{{ route('exam.history') }}" class="l-bandbtn"><i class="bi bi-x-lg" aria-hidden="true"></i><span>Schließen</span></a>
                <p class="l-band-t">Auswertung</p>
                <span></span>
            </div>
            <h1 class="l-band-h">Prüfung {{ $passed ? 'bestanden.' : 'nicht bestanden.' }}</h1>
            <p class="l-band-sub">
                {{ $correctCount }} von {{ $total }} richtig, {{ $percentage }} %. 30:00 Min, 80 % benötigt.
                @if(isset($gamification_result) && $gamification_result && isset($gamification_result['points_awarded']))
                    <b>+{{ $gamification_result['points_awarded'] }} XP{{ !empty($gamification_result['level_up']) ? ', Level Up!' : '' }}</b>
                @endif
            </p>
            <div class="l-meter" role="img" aria-label="{{ $percentage }} Prozent, bestanden ab 80 Prozent"><i style="width: {{ $percentage }}%;"></i><s></s></div>
            <div class="l-strip" role="group" aria-label="Fragen, antippen zum Durchgehen">
                @foreach($results as $index => $result)
                    <button type="button" class="{{ $result['isCorrect'] ? 'is-ok' : 'is-bad' }}" onclick="startReview('all', {{ $index }})" aria-label="Frage {{ $index + 1 }}, {{ $result['isCorrect'] ? 'richtig' : 'falsch' }}"></button>
                @endforeach
            </div>
        </section>

        <div class="l-res">
            <dl class="l-figs l-figs--two">
                <div><dt>Richtig</dt><dd>{{ $correctCount }}</dd></div>
                <div><dt>Falsch</dt><dd class="{{ $wrongCount > 0 ? 'l-tone-bad' : '' }}">{{ $wrongCount }}</dd></div>
            </dl>
            @if($wrongCount > 0)
                <p class="l-small">Falsch beantwortete Fragen landen unter „Fehler“ im Übungsmenü. Vor der nächsten Prüfung wiederholst du sie dort.</p>
            @endif

            @if($weakSections->isNotEmpty())
                <section aria-labelledby="l-weak">
                    <h2 id="l-weak" class="l-group-h">Schwachstellen</h2>
                    <div class="l-group">
                        @foreach($weakSections as $ws)
                            <div class="l-row">
                                <span class="l-row-t">
                                    <b>LA {{ $ws['la'] }}</b>
                                    <span>{{ \App\Http\Controllers\LandingController::SECTIONS[(int) $ws['la']][0] ?? '' }}</span>
                                    <span class="l-line {{ $ws['pct'] < 50 ? 'is-bad' : 'is-warn' }}"><i style="width: {{ $ws['pct'] }}%;"></i></span>
                                </span>
                                <span class="l-row-v">{{ $ws['correct'] }}/{{ $ws['total'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <div class="l-res-acts">
                <button type="button" class="l-btn l-btn--lg" onclick="startReview('all')">Alle Fragen durchgehen</button>
                @if($wrongCount > 0)
                    <button type="button" class="l-btn l-btn--lg l-btn--soft" onclick="startReview('wrong')">Nur falsche Fragen ({{ $wrongCount }})</button>
                    @auth
                        @if(count($wrongNotYetBookmarked) > 0)
                            <button type="button" class="l-btn l-btn--lg l-btn--soft" id="save-wrong-btn" onclick="saveAllWrongQuestions(this)">
                                <span id="save-wrong-btn-label">Alle falschen Fragen merken ({{ count($wrongNotYetBookmarked) }})</span>
                            </button>
                        @else
                            <div class="l-btn l-btn--lg is-done" id="save-wrong-btn">
                                <span id="save-wrong-btn-label">Alle falschen Fragen sind gemerkt</span>
                            </div>
                        @endif
                    @endauth
                @endif
                <a href="{{ route('exam.history') }}" class="l-link">Zur Prüfungshistorie</a>
            </div>
        </div>
    </div>

    {{-- ── Nachbesprechung (anfangs ausgeblendet) ── --}}
    <div id="result-review" style="display: none;">
        <section class="l-band">
            <span class="l-gearmark" aria-hidden="true">@include('landing.partials.gear')</span>
            <div class="l-band-top">
                <button type="button" class="l-bandbtn" onclick="closeReview()"><i class="bi bi-chevron-left" aria-hidden="true"></i><span>Auswertung</span></button>
                <p class="l-band-t" id="review-title"></p>
                <span></span>
            </div>
            <div class="l-strip l-strip--fit" id="review-strip" role="group" aria-label="Fragen"></div>
        </section>

        <div class="l-focus">
            @foreach($results as $index => $result)
                @php
                    $reviewQuestionId = $result['frage']->id;
                    $reviewIsBookmarked = in_array($reviewQuestionId, $examBookmarked);
                    $mapping = $result['mapping'] ?? [0 => 'A', 1 => 'B', 2 => 'C'];
                    $solution = $result['solution'];
                    $userAnswer = $result['userAnswer'];
                @endphp
                <div class="exam-slide" data-review="{{ $index }}">
                    <p class="l-qmeta">
                        <span>Frage {{ $index + 1 }}, LA {{ $result['frage']->lernabschnitt ?? '-' }}.{{ $result['frage']->nummer ?? '-' }}</span>
                        <span class="l-diff {{ $result['isCorrect'] ? 'is-easy' : 'is-hard' }}">{{ $result['isCorrect'] ? 'Richtig' : 'Falsch' }}</span>
                        @auth
                            <button type="button"
                                    class="l-tool exam-bookmark-btn {{ $reviewIsBookmarked ? 'active' : '' }}"
                                    style="margin-left: auto;"
                                    data-bookmark-btn="{{ $reviewQuestionId }}"
                                    data-bookmarked="{{ $reviewIsBookmarked ? 'true' : 'false' }}"
                                    aria-pressed="{{ $reviewIsBookmarked ? 'true' : 'false' }}"
                                    onclick="toggleExamBookmark({{ $reviewQuestionId }}, this)"
                                    title="Frage merken">
                                <i class="bi {{ $reviewIsBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark' }}" aria-hidden="true"></i><span>{{ $reviewIsBookmarked ? 'Gemerkt' : 'Merken' }}</span>
                            </button>
                        @endauth
                    </p>
                    <h2 class="l-qtext">{{ $result['frage']->frage }}</h2>

                    <div class="l-opts">
                        @foreach($mapping as $position => $letter)
                            @php
                                $answerField = 'antwort_' . strtolower($letter);
                                $isCorrectAnswer = $solution->contains($letter);
                                $wasSelected = $userAnswer && $userAnswer->contains($letter);
                                $state = $isCorrectAnswer ? ($wasSelected ? 'correct' : 'missed') : ($wasSelected ? 'wrong' : 'neutral');
                            @endphp
                            <div class="l-opt l-opt--sq is-{{ $state }}">
                                <span class="t">{{ $result['frage']->$answerField }}@if($state === 'missed')<em class="l-tag">Richtig</em>@endif</span>
                                <span class="l-check"><i class="bi {{ $state === 'wrong' ? 'bi-x-lg' : 'bi-check-lg' }}" aria-hidden="true"></i></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="l-bar">
            <div class="l-acts l-acts--two">
                <button type="button" class="l-btn l-btn--lg l-btn--soft" onclick="prevReview()" id="review-btn-back">Zurück</button>
                <button type="button" class="l-btn l-btn--lg" onclick="nextReview()" id="review-btn-next">Weiter</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ============================================
// RESULT REVIEW MODE
// ============================================

const reviewResults = @json(collect($results)->map(fn($r, $i) => ['index' => $i, 'isCorrect' => $r['isCorrect']])->values());
let reviewMode = null; // 'all' or 'wrong'
let reviewQuestions = []; // indices to show
let reviewCurrent = 0;

const reviewContainer = document.getElementById('result-review');
const summaryContainer = document.getElementById('result-summary');
const reviewStrip = document.getElementById('review-strip');
const reviewTitle = document.getElementById('review-title');

function startReview(mode, startAt) {
    reviewMode = mode;
    if (mode === 'all') {
        reviewQuestions = reviewResults.map(function(_, i) { return i; });
    } else {
        reviewQuestions = reviewResults.filter(function(r) { return !r.isCorrect; }).map(function(r) { return r.index; });
    }
    reviewCurrent = Math.max(0, typeof startAt === 'number' ? reviewQuestions.indexOf(startAt) : 0);

    summaryContainer.style.display = 'none';
    reviewContainer.style.display = 'block';
    showReviewQuestion(reviewCurrent);
}

function closeReview() {
    reviewContainer.style.display = 'none';
    summaryContainer.style.display = 'block';
    window.scrollTo(0, 0);
}

function showReviewQuestion(idx) {
    if (idx < 0 || idx >= reviewQuestions.length) return;
    reviewCurrent = idx;

    document.querySelectorAll('[data-review]').forEach(function(s) { s.style.display = 'none'; });
    var slide = document.querySelector('[data-review="' + reviewQuestions[idx] + '"]');
    if (slide) slide.style.display = 'block';

    if (reviewTitle) {
        reviewTitle.innerHTML = (reviewMode === 'wrong' ? 'Falsche Fragen' : 'Nachbesprechung') + ', <b>' + (idx + 1) + '</b> von ' + reviewQuestions.length;
    }

    var btnBack = document.getElementById('review-btn-back');
    var btnNext = document.getElementById('review-btn-next');
    if (btnBack) btnBack.disabled = idx === 0;
    if (btnNext) btnNext.textContent = idx === reviewQuestions.length - 1 ? 'Zur Auswertung' : 'Weiter';

    updateReviewStrip();
    window.scrollTo(0, 0);
}

function nextReview() {
    if (reviewCurrent === reviewQuestions.length - 1) {
        closeReview();
        return;
    }
    showReviewQuestion(reviewCurrent + 1);
}

function prevReview() {
    showReviewQuestion(reviewCurrent - 1);
}

function updateReviewStrip() {
    if (!reviewStrip) return;
    reviewStrip.innerHTML = '';
    reviewQuestions.forEach(function(originalIndex, j) {
        var seg = document.createElement('button');
        seg.type = 'button';
        seg.className = (reviewResults[originalIndex].isCorrect ? 'is-ok' : 'is-bad') + (j === reviewCurrent ? ' is-current' : '');
        seg.setAttribute('aria-label', 'Frage ' + (originalIndex + 1));
        if (j === reviewCurrent) seg.setAttribute('aria-current', 'step');
        seg.addEventListener('click', function() { showReviewQuestion(j); });
        reviewStrip.appendChild(seg);
    });
}

// ── Bookmark Handling in Result Review ───────
@auth
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    csrfToken = csrfToken ? csrfToken.getAttribute('content') : '';

    // IDs der falschen Fragen für Bulk-Action
    var wrongQuestionIds = @json($wrongQuestionIds);

    window.toggleExamBookmark = function(questionId, btn) {
        if (!btn || btn.disabled) return;

        var formData = new FormData();
        formData.append('question_id', questionId);
        formData.append('_token', csrfToken);

        btn.disabled = true;

        fetch('{{ route("bookmarks.toggle") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data || !data.success) return;
            updateBookmarkButtons(questionId, data.is_bookmarked);
            refreshSaveWrongButton();
        })
        .catch(function(err) { console.error('Bookmark error:', err); })
        .finally(function() { btn.disabled = false; });
    };

    window.saveAllWrongQuestions = function(btn) {
        if (!btn || btn.disabled || btn.classList.contains('is-done')) return;

        // Finde alle noch nicht gespeicherten falschen Fragen aus dem aktuellen DOM-Status
        var pending = wrongQuestionIds.filter(function(id) {
            var el = document.querySelector('[data-bookmark-btn="' + id + '"]');
            return el && el.getAttribute('data-bookmarked') !== 'true';
        });

        if (pending.length === 0) {
            markSaveWrongDone(0);
            return;
        }

        var formData = new FormData();
        pending.forEach(function(id) { formData.append('question_ids[]', id); });
        formData.append('_token', csrfToken);

        btn.disabled = true;
        var label = document.getElementById('save-wrong-btn-label');
        var originalText = label ? label.textContent : '';
        if (label) label.textContent = 'Speichere …';

        fetch('{{ route("bookmarks.addMany") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data || !data.success) {
                if (label) label.textContent = originalText;
                btn.disabled = false;
                return;
            }
            pending.forEach(function(id) { updateBookmarkButtons(id, true); });
            markSaveWrongDone(data.added || pending.length);
        })
        .catch(function(err) {
            console.error('Bulk bookmark error:', err);
            if (label) label.textContent = originalText;
            btn.disabled = false;
        });
    };

    function updateBookmarkButtons(questionId, isBookmarked) {
        var buttons = document.querySelectorAll('[data-bookmark-btn="' + questionId + '"]');
        buttons.forEach(function(b) {
            b.setAttribute('data-bookmarked', isBookmarked ? 'true' : 'false');
            b.setAttribute('aria-pressed', isBookmarked ? 'true' : 'false');
            b.classList.toggle('active', !!isBookmarked);
            var icon = b.querySelector('.bi');
            if (icon) icon.className = 'bi ' + (isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark');
            var text = b.querySelector('span');
            if (text) text.textContent = isBookmarked ? 'Gemerkt' : 'Merken';
        });
    }

    function refreshSaveWrongButton() {
        var btn = document.getElementById('save-wrong-btn');
        if (!btn) return;
        var label = document.getElementById('save-wrong-btn-label');
        if (!label) return;

        var pending = wrongQuestionIds.filter(function(id) {
            var el = document.querySelector('[data-bookmark-btn="' + id + '"]');
            return el && el.getAttribute('data-bookmarked') !== 'true';
        });

        if (pending.length === 0) {
            markSaveWrongDone(0);
        } else {
            btn.classList.remove('is-done');
            btn.disabled = false;
            label.textContent = 'Alle falschen Fragen merken (' + pending.length + ')';
        }
    }

    function markSaveWrongDone(addedCount) {
        var btn = document.getElementById('save-wrong-btn');
        if (!btn) return;
        var label = document.getElementById('save-wrong-btn-label');
        btn.classList.add('is-done');
        btn.disabled = true;
        if (label) {
            label.textContent = addedCount > 0
                ? 'Gespeichert (' + addedCount + ')'
                : 'Alle falschen Fragen sind gemerkt';
        }
    }
})();
@endauth
</script>
@endpush

@endif

@endsection
