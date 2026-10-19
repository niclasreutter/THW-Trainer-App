@extends(isset($isLanding) && $isLanding ? 'layouts.public' : 'layouts.app')
@section('title', 'Prüfungssimulation anonym | THW-Trainer.de')
@section('description', 'THW Prüfungssimulation anonym: Teste dich mit 40 zufälligen Fragen in 30 Minuten ohne Anmeldung. Sofortige Auswertung und Ergebnisanzeige!')
@section('robots', 'noindex, follow')

{{-- Gäste: Startseiten-Layout mit Topleiste, dazu das Stylesheet der Lernseiten --}}
@push('head')
    @vite('resources/css/learn-guest.css')
@endpush

@section('content')
@if(!isset($submitted))
{{-- ============================================
     PRÜFUNG (anonym): blaues Band mit allen Fragen, Bedienung unten
     ============================================ --}}
<div class="l-focus-root l-exam" id="guestExamContainer">
    <section class="l-band" data-nav-dark>
        <span class="l-gearmark" aria-hidden="true">@include('landing.partials.gear')</span>
        <div class="l-band-top">
            <a href="{{ route('landing.guest.practice.menu') }}" class="l-bandbtn" onclick="handleExamExit(event, this.href)" aria-label="Prüfung verlassen">
                <i class="bi bi-x-lg" aria-hidden="true"></i><span>Verlassen</span>
            </a>
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
        <form id="exam-form" method="POST" action="{{ route('landing.guest.exam.submit') }}">
            @csrf

            @foreach($fragen as $index => $frage)
                <input type="hidden" name="fragen_ids[]" value="{{ $frage->id }}">

                @php
                    // Antworten mischen; Mapping Position -> Buchstabe geht mit dem Formular zurück
                    $answers = [
                        ['letter' => 'A', 'text' => $frage->antwort_a],
                        ['letter' => 'B', 'text' => $frage->antwort_b],
                        ['letter' => 'C', 'text' => $frage->antwort_c],
                    ];
                    shuffle($answers);
                    $mappingArray = [];
                    foreach ($answers as $ansIndex => $answer) {
                        $mappingArray[$ansIndex] = $answer['letter'];
                    }
                @endphp

                <input type="hidden" name="answer_mappings[{{ $index }}]" value="{{ json_encode($mappingArray) }}">

                <div class="exam-slide" data-question="{{ $index }}" @if($index === 0) style="display: block;" @endif>
                    <p class="l-qmeta">
                        <span>LA {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}</span>
                        <span class="l-diff is-medium" data-marked-tag hidden>Markiert</span>
                    </p>
                    <h1 class="l-qtext">{{ $frage->frage }}</h1>

                    <div class="l-opts">
                        @foreach($answers as $ansIndex => $answer)
                            <button type="button" class="l-opt l-opt--sq exam-answer" data-position="{{ $ansIndex }}" aria-pressed="false" onclick="toggleAnswer({{ $index }}, {{ $ansIndex }})">
                                <span class="t">{{ $answer['text'] }}</span>
                                <span class="l-check"><i class="bi bi-check-lg" aria-hidden="true"></i></span>
                            </button>
                            <input type="checkbox" name="answer[{{ $index }}][]" value="{{ $ansIndex }}" hidden>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </form>
        <p class="l-small l-rules">Anonym, 40 Fragen, 30 Minuten, 80 % zum Bestehen</p>
    </div>

    <div class="l-bar">
        <div class="l-acts l-acts--three">
            <button type="button" class="l-btn l-btn--lg l-btn--soft" onclick="previousQuestion()" id="prev-btn">Zurück</button>
            <button type="button" class="l-btn l-btn--lg l-btn--soft l-markbtn" onclick="toggleMark()" id="btn-mark" aria-pressed="false">Markieren</button>
            <button type="button" class="l-btn l-btn--lg" onclick="nextQuestion()" id="next-btn">Weiter</button>
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

<script>
    // State Management
    let currentQuestion = 0;
    const totalQuestions = {{ count($fragen) }};
    let answers = new Array(totalQuestions).fill(false); // Ob Frage beantwortet
    let marked = new Array(totalQuestions).fill(false); // Ob Frage markiert
    let timeLeft = 30 * 60;
    let examSubmitting = false;

    const examForm = document.getElementById('exam-form');
    const timerEl = document.getElementById('exam-timer');
    const strip = document.getElementById('exam-strip');
    const btnMark = document.getElementById('btn-mark');

    // Timer (Warnung unter 10, kritisch unter 5 Minuten)
    function updateTimer() {
        const min = Math.floor(timeLeft / 60).toString().padStart(2, '0');
        const sec = (timeLeft % 60).toString().padStart(2, '0');
        timerEl.textContent = `${min}:${sec}`;
        timerEl.classList.toggle('warning', timeLeft < 600 && timeLeft >= 300);
        timerEl.classList.toggle('critical', timeLeft < 300);

        if (timeLeft <= 0) {
            timerEl.textContent = '00:00';
            confirmSubmit();
        } else {
            timeLeft--;
            setTimeout(updateTimer, 1000);
        }
    }

    // Fragenleiste: alle Fragen auf einen Blick
    for (let i = 0; i < totalQuestions; i++) {
        const seg = document.createElement('button');
        seg.type = 'button';
        seg.addEventListener('click', function() { showQuestion(i); });
        strip.appendChild(seg);
    }

    function updateOverview() {
        Array.prototype.forEach.call(strip.children, function(seg, i) {
            seg.className = i === currentQuestion ? 'is-current' : (marked[i] ? 'is-marked' : (answers[i] ? 'is-answered' : ''));
            if (i === currentQuestion) seg.setAttribute('aria-current', 'step'); else seg.removeAttribute('aria-current');
            seg.setAttribute('aria-label', 'Frage ' + (i + 1) + (marked[i] ? ', markiert' : '') + (answers[i] ? ', beantwortet' : ''));
        });
        const answeredCount = answers.filter(Boolean).length;
        document.getElementById('stat-answered').textContent = answeredCount;
        document.getElementById('stat-open').textContent = totalQuestions - answeredCount;
        document.getElementById('stat-marked').textContent = marked.filter(Boolean).length;
    }

    // Navigation
    function showQuestion(index) {
        if (index < 0 || index >= totalQuestions) return;
        document.querySelectorAll('.exam-slide').forEach(slide => { slide.style.display = 'none'; });
        document.querySelector(`.exam-slide[data-question="${index}"]`).style.display = 'block';
        currentQuestion = index;

        document.getElementById('exam-qnum').textContent = index + 1;
        document.getElementById('prev-btn').disabled = index === 0;
        document.getElementById('next-btn').textContent = index === totalQuestions - 1 ? 'Abgeben' : 'Weiter';
        paintMark();
        updateOverview();
        window.scrollTo(0, 0);
    }

    function nextQuestion() {
        if (currentQuestion < totalQuestions - 1) {
            showQuestion(currentQuestion + 1);
        } else {
            showSubmitOverview();
        }
    }

    function previousQuestion() {
        showQuestion(currentQuestion - 1);
    }

    // Antworten
    function toggleAnswer(index, position) {
        const slide = document.querySelector(`.exam-slide[data-question="${index}"]`);
        const btn = slide.querySelector(`.exam-answer[data-position="${position}"]`);
        const checkbox = slide.querySelector(`input[type="checkbox"][value="${position}"]`);
        checkbox.checked = !checkbox.checked;
        btn.setAttribute('aria-pressed', checkbox.checked ? 'true' : 'false');
        answers[index] = slide.querySelectorAll('input[type="checkbox"]:checked').length > 0;
        updateOverview();
    }

    // Frage markieren
    function paintMark() {
        const on = marked[currentQuestion];
        btnMark.classList.toggle('active', on);
        btnMark.setAttribute('aria-pressed', on ? 'true' : 'false');
        btnMark.textContent = on ? 'Markiert' : 'Markieren';
        const tag = document.querySelector(`.exam-slide[data-question="${currentQuestion}"] [data-marked-tag]`);
        if (tag) tag.hidden = !on;
    }

    function toggleMark() {
        marked[currentQuestion] = !marked[currentQuestion];
        paintMark();
        updateOverview();
    }

    // Rückfrage im Seitenstil
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

    // Übersicht vor dem Abgeben: beantwortet, offen, markiert
    function showSubmitOverview() {
        const unanswered = answers.reduce((acc, answered, index) => { if (!answered) acc.push(index + 1); return acc; }, []);
        const markedCount = marked.filter(Boolean).length;
        let text = `${answers.filter(Boolean).length} von ${totalQuestions} Fragen beantwortet.`;
        if (unanswered.length > 0) text += ` Noch offen: ${unanswered.join(', ')}.`;
        if (markedCount > 0) text += ` ${markedCount} ${markedCount === 1 ? 'Frage' : 'Fragen'} markiert.`;
        examConfirm('Prüfung abschließen?', text, 'Prüfung abgeben', 'Zurück zur Prüfung', confirmSubmit);
    }

    function handleExamExit(e, href) {
        e.preventDefault();
        examConfirm('Prüfung wirklich abbrechen?', 'Dein Fortschritt geht verloren.', 'Prüfung abbrechen', 'Weiter prüfen', function() {
            window.location.href = href;
        });
    }

    // Prüfung abgeben
    function confirmSubmit() {
        if (examSubmitting) return;
        examSubmitting = true;
        examForm.submit();
    }

    // Initial state
    showQuestion(0);
    updateTimer();
</script>

@else
{{-- ============================================
     ERGEBNIS (anonym): Balken mit 80-%-Marke, alle Lösungen, Registrierung
     ============================================ --}}
@php
    $percent = $total > 0 ? round($correctCount / $total * 100) : 0;
@endphp
<div class="l-focus-root l-exam">
    <section class="l-band l-band--result {{ $passed ? 'is-pass' : 'is-fail' }}" data-nav-dark>
        <span class="l-gearmark" aria-hidden="true">@include('landing.partials.gear')</span>
        <div class="l-band-top">
            <a href="{{ route('landing.guest.practice.menu') }}" class="l-bandbtn"><i class="bi bi-x-lg" aria-hidden="true"></i><span>Schließen</span></a>
            <p class="l-band-t">Auswertung</p>
            <span></span>
        </div>
        <h1 class="l-band-h">Prüfung {{ $passed ? 'bestanden.' : 'nicht bestanden.' }}</h1>
        <p class="l-band-sub">{{ $correctCount }} von {{ $total }} richtig, {{ $percent }} %. 80 % benötigt.</p>
        <div class="l-meter" role="img" aria-label="{{ $percent }} Prozent, bestanden ab 80 Prozent"><i style="width: {{ $percent }}%;"></i><s></s></div>
        <div class="l-strip" role="group" aria-label="Fragen, antippen springt zur Lösung">
            @foreach($fragen as $nr => $frage)
                <a href="#frage-{{ $nr + 1 }}" class="{{ $results[$nr]['isCorrect'] ? 'is-ok' : 'is-bad' }}" aria-label="Frage {{ $nr + 1 }}, {{ $results[$nr]['isCorrect'] ? 'richtig' : 'falsch' }}"></a>
            @endforeach
        </div>
    </section>

    <div class="l-res">
        <p class="l-small">Anonyme Prüfung, das Ergebnis wird nicht gespeichert.</p>

        <div class="l-group l-cta">
            <h2 class="l-h2">Erstelle einen Account für mehr</h2>
            <p class="l-small">Mit einem kostenlosen Account kannst du Prüfungsergebnisse speichern und deinen Fortschritt verfolgen.</p>
            <a href="{{ route('register') }}" class="l-btn">Kostenlos registrieren</a>
        </div>

        <section aria-labelledby="l-loesungen">
            <h2 id="l-loesungen" class="l-group-h">Lösungen</h2>
            @foreach($fragen as $nr => $frage)
                @php
                    $mappingArray = $results[$nr]['mapping'] ?? [];
                    $answersOriginal = [
                        ['letter' => 'A', 'text' => $frage->antwort_a],
                        ['letter' => 'B', 'text' => $frage->antwort_b],
                        ['letter' => 'C', 'text' => $frage->antwort_c],
                    ];
                    // Reihenfolge wie in der Prüfung
                    if ($mappingArray) {
                        $answers = [];
                        foreach ($mappingArray as $position => $letter) {
                            foreach ($answersOriginal as $ans) {
                                if ($ans['letter'] === $letter) {
                                    $answers[$position] = $ans;
                                    break;
                                }
                            }
                        }
                        ksort($answers);
                    } else {
                        $answers = $answersOriginal;
                    }
                    $solution = collect(explode(',', $frage->loesung))->map(fn($s) => trim($s));
                @endphp
                <article class="l-sol" id="frage-{{ $nr + 1 }}">
                    <p class="l-qmeta">
                        <span>Frage {{ $nr + 1 }}, LA {{ $frage->lernabschnitt ?? '-' }}.{{ $frage->nummer ?? '-' }}</span>
                        <span class="l-diff {{ $results[$nr]['isCorrect'] ? 'is-easy' : 'is-hard' }}">{{ $results[$nr]['isCorrect'] ? 'Richtig beantwortet' : 'Falsch beantwortet' }}</span>
                    </p>
                    <h3 class="l-qtext">{{ $frage->frage }}</h3>
                    <div class="l-opts">
                        @foreach($answers as $answer)
                            @php
                                $isCorrectAnswer = $solution->contains($answer['letter']);
                                $isUserAnswer = $results[$nr]['userAnswer']->contains($answer['letter']);
                                $state = $isCorrectAnswer ? ($isUserAnswer ? 'correct' : 'missed') : ($isUserAnswer ? 'wrong' : 'neutral');
                            @endphp
                            <div class="l-opt l-opt--sq is-{{ $state }}">
                                <span class="t">{{ $answer['text'] }}@if($state === 'missed')<em class="l-tag">Richtig</em>@endif</span>
                                <span class="l-check"><i class="bi {{ $state === 'wrong' ? 'bi-x-lg' : 'bi-check-lg' }}" aria-hidden="true"></i></span>
                            </div>
                        @endforeach
                    </div>
                    @if(!empty(trim($frage->loesungsweg ?? '')))
                        <div class="l-expl">
                            <p class="l-small">Lösungsweg</p>
                            <p>{!! nl2br(e($frage->loesungsweg)) !!}</p>
                        </div>
                    @endif
                </article>
            @endforeach
        </section>

        <div class="l-res-acts">
            <a href="{{ route('landing.guest.exam.index') }}" class="l-btn l-btn--lg">Neue Prüfung</a>
            <a href="{{ route('landing.guest.practice.all') }}" class="l-btn l-btn--lg l-btn--soft">Weiter üben</a>
            <a href="{{ \App\Helpers\DomainHelper::landingHomeUrl() }}" class="l-link">Startseite</a>
        </div>
    </div>
</div>
@endif
@endsection
