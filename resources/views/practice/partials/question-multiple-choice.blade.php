                @php
                    $answersOriginal = [
                        ['letter' => 'A', 'text' => $question->antwort_a],
                        ['letter' => 'B', 'text' => $question->antwort_b],
                        ['letter' => 'C', 'text' => $question->antwort_c],
                    ];

                    if (isset($isCorrect) && isset($answerResult['answer_mapping'])) {
                        $mappingArray = $answerResult['answer_mapping'];
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
                        shuffle($answers);
                        $mappingArray = [];
                        foreach ($answers as $index => $answer) {
                            $mappingArray[$index] = $answer['letter'];
                        }
                    }

                    $mappingJson = json_encode($mappingArray);
                    $solution = collect(explode(',', $question->loesung))->map(fn($s) => strtoupper(trim($s)));
                @endphp

                <input type="hidden" name="answer_mapping" value="{{ $mappingJson }}">

                <!-- Mobile Badges -->
                <div class="practice-mobile-badges">
                    @if(isset($isSpacedRepetition) && $isSpacedRepetition)
                        <span class="pm-badge pm-badge--sr"><i class="bi bi-arrow-repeat"></i> SR</span>
                    @endif
                    @if(isset($difficultyInfo) && $difficultyInfo['level'] !== 'unknown')
                        <span class="pm-badge pm-badge--{{ $difficultyInfo['level'] }}">{{ $difficultyInfo['label'] }}</span>
                    @endif
                </div>

                <!-- Question Meta -->
                <div class="question-meta">
                    <span class="question-meta-left">
                        ID {{ $question->id }} &middot; LA {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}
                    </span>
                </div>

                <!-- Question Text -->
                <p class="question-text">{{ $question->frage }}</p>

                {{-- Inline Result Banner (nur wenn KEIN Special-Event) --}}
                @if(isset($isCorrect))
                    @php
                        $showGamification = $isCorrect && $gamificationResult && isset($gamificationResult['points_awarded']);
                        $celebrations = ['Grandios!', 'Fantastisch!', 'Super!', 'Stark!', 'Mega!', 'Klasse!', 'Volltreffer!', 'Genial!'];
                        $celebrationText = $celebrations[$question->id % count($celebrations)];
                        $pointsAwarded = $showGamification ? ($gamificationResult['points_awarded'] ?? 0) : 0;
                        $reason = $showGamification ? ($gamificationResult['reason'] ?? '') : '';
                        if ($pointsAwarded >= 20) {
                            $reasonText = str_contains($reason, 'Häufig falsche') ? 'Schwere Frage gelöst' : 'Mit Streak-Bonus';
                        } else {
                            $reasonText = $reason;
                        }
                        $masteryThreshold = $isGuest ? 3 : \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;
                        $showMastered = !$isGuest && isset($questionProgress) && $questionProgress->consecutive_correct >= $masteryThreshold;
                        $remaining = isset($questionProgress) ? $masteryThreshold - $questionProgress->consecutive_correct : $masteryThreshold;
                        $showAlmostMastered = !$isGuest && isset($questionProgress) && $questionProgress->consecutive_correct > 0 && $questionProgress->consecutive_correct < $masteryThreshold;

                        // Special-Events prüfen - wenn vorhanden, zeigt das Layout
                        // den Fullscreen-Overlay, also hier kein Banner nötig
                        $hasSpecialEvent = $gamificationResult && (
                            (isset($gamificationResult['level_up']) && $gamificationResult['level_up']) ||
                            isset($gamificationResult['achievement']) ||
                            isset($gamificationResult['streak_milestone'])
                        );
                    @endphp
                    @if(!$hasSpecialEvent)
                        <div class="result-banner {{ $isCorrect ? 'result-banner--correct' : 'result-banner--wrong' }}">
                            <div class="result-banner-left">
                                <span class="result-banner-icon">
                                    <i class="bi {{ $isCorrect ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" style="color:{{ $isCorrect ? '#22c55e' : '#ef4444' }};"></i>
                                </span>
                                <span class="result-banner-text">{{ $isCorrect ? $celebrationText : 'Falsch' }}</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                @if($isCorrect && $showGamification && $pointsAwarded > 0)
                                    <span class="result-banner-xp">+{{ $pointsAwarded }} XP</span>
                                @endif
                                @if($showMastered)
                                    <span class="result-banner-mastery">Gemeistert!</span>
                                @elseif($showAlmostMastered)
                                    <span class="result-banner-mastery">Noch {{ $remaining }}x</span>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Answers -->
                <div class="answers-grid">
                    @foreach($answers as $index => $answer)
                        @php
                            $originalLetter = $answer['letter'];
                            $isCorrectAnswer = $solution->contains($originalLetter);
                            $isUserAnswer = isset($userAnswer) && $userAnswer->contains($originalLetter);

                            $stateClass = '';
                            $icon = '';

                            if (isset($isCorrect)) {
                                if ($isCorrectAnswer && $isUserAnswer) {
                                    $stateClass = 'answer-opt--correct';
                                    $icon = '✓';
                                } elseif ($isCorrectAnswer && !$isUserAnswer) {
                                    $stateClass = 'answer-opt--correct-missed';
                                    $icon = '✓';
                                } elseif (!$isCorrectAnswer && $isUserAnswer) {
                                    $stateClass = 'answer-opt--wrong';
                                    $icon = '✗';
                                } else {
                                    $stateClass = 'answer-opt--neutral';
                                }
                            }
                        @endphp

                        @if(isset($isCorrect))
                            <div class="answer-opt {{ $stateClass }}">
                                <span class="result-icon {{ $isUserAnswer ? ($isCorrectAnswer ? 'result-icon--correct' : 'result-icon--wrong') : '' }}">
                                    @if($isUserAnswer) {{ $icon }} @endif
                                </span>
                                <span class="answer-text">{{ $answer['text'] }}</span>
                                @if($isCorrectAnswer && !$isUserAnswer)
                                    <span style="font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;padding:0.15rem 0.4rem;border-radius:0.25rem;background:rgba(34,197,94,0.15);color:#22c55e;flex-shrink:0;">
                                        Richtig
                                    </span>
                                @endif
                            </div>
                        @else
                            <label class="answer-opt" onclick="updateSelectionStyle(this)">
                                <input type="checkbox" name="answer[]" value="{{ $index }}"
                                       class="answer-check"
                                       onchange="updateSubmitButton()">
                                <span class="answer-text">{{ $answer['text'] }}</span>
                            </label>
                        @endif
                    @endforeach
                </div>

                @if(isset($isCorrect))
                    <div class="result-summary">
                        <span class="{{ $isCorrect ? 'result-label--correct' : 'result-label--wrong' }}">
                            {{ $isCorrect ? 'Richtig beantwortet' : 'Falsch beantwortet' }}
                        </span>
                        <span style="font-size:0.75rem;color:var(--text-muted);font-family:'IBM Plex Mono',monospace;">
                            {{ $solution->join(', ') }}
                        </span>
                    </div>
                @endif
