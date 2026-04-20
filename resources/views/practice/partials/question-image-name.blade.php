{{-- Practice Partial: Bild benennen (image_name)
     Zeigt ein Bild und darunter Text-Optionen als Checkboxes.
     Nutzt name="answer[]" damit der bestehende updateSubmitButton() greift. --}}

@php
    $options = $question->options;
    $userAnswerIds = collect([]);
    if (isset($userAnswer)) {
        // userAnswer kann Array, Collection oder CSV sein – robust normalisieren
        $raw = is_string($userAnswer) ? explode(',', $userAnswer) : (array) $userAnswer;
        $userAnswerIds = collect($raw)->map(fn($v) => (int) $v)->filter();
    }
    $imageUrl = $question->image_path ? \Illuminate\Support\Facades\Storage::url($question->image_path) : null;
@endphp

<style>
    .iname-wrap { display: flex; flex-direction: column; gap: 1rem; }

    .iname-frame {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        max-height: 420px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    html.light-mode .iname-frame {
        background: #f1f5f9;
        border-color: rgba(0, 51, 127, 0.1);
        box-shadow: 0 4px 12px rgba(0, 51, 127, 0.08);
    }
    .iname-frame::before {
        content: '';
        position: absolute; inset: 0;
        pointer-events: none;
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.12), transparent 40%);
        mix-blend-mode: overlay;
    }
    .iname-frame img {
        display: block;
        max-width: 100%;
        max-height: 420px;
        width: auto;
        height: auto;
        object-fit: contain;
    }
    .iname-frame-missing {
        padding: 3rem 1rem;
        text-align: center;
        color: var(--text-muted);
        font-size: 0.875rem;
    }

    .iname-options .answer-opt { /* nutzt bestehendes answer-opt */
        align-items: center;
    }
</style>

<div class="iname-wrap">
    {{-- Mobile Badges --}}
    <div class="practice-mobile-badges">
        @if(isset($isSpacedRepetition) && $isSpacedRepetition)
            <span class="pm-badge pm-badge--sr"><i class="bi bi-arrow-repeat"></i> SR</span>
        @endif
        @if(isset($difficultyInfo) && $difficultyInfo['level'] !== 'unknown')
            <span class="pm-badge pm-badge--{{ $difficultyInfo['level'] }}">{{ $difficultyInfo['label'] }}</span>
        @endif
    </div>

    <div class="question-meta">
        <span class="question-meta-left">
            ID {{ $question->id }} &middot; LA {{ $question->lernabschnitt ?? '-' }} &middot; Bild benennen
        </span>
    </div>

    <p class="question-text">{{ $question->frage }}</p>

    {{-- Bild --}}
    <div class="iname-frame">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="Frage-Bild" loading="lazy">
        @else
            <div class="iname-frame-missing">Kein Bild hinterlegt</div>
        @endif
    </div>

    {{-- Ergebnis-Banner --}}
    @if(isset($isCorrect))
        <div class="result-banner {{ $isCorrect ? 'result-banner--correct' : 'result-banner--wrong' }}">
            <div class="result-banner-left">
                <span class="result-banner-icon">
                    <i class="bi {{ $isCorrect ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" style="color:{{ $isCorrect ? '#22c55e' : '#ef4444' }};"></i>
                </span>
                <span class="result-banner-text">{{ $isCorrect ? 'Richtig benannt' : 'Leider falsch' }}</span>
            </div>
        </div>
    @endif

    {{-- Antwort-Optionen --}}
    <div class="answers-grid iname-options">
        @foreach($options as $opt)
            @php
                $isUserPicked = $userAnswerIds->contains($opt->id);
                $isCorrectOpt = (bool) $opt->is_correct;

                $stateClass = '';
                $icon = '';
                if (isset($isCorrect)) {
                    if ($isCorrectOpt && $isUserPicked) {
                        $stateClass = 'answer-opt--correct';
                        $icon = '✓';
                    } elseif ($isCorrectOpt && !$isUserPicked) {
                        $stateClass = 'answer-opt--correct-missed';
                        $icon = '✓';
                    } elseif (!$isCorrectOpt && $isUserPicked) {
                        $stateClass = 'answer-opt--wrong';
                        $icon = '✗';
                    } else {
                        $stateClass = 'answer-opt--neutral';
                    }
                }
            @endphp

            @if(isset($isCorrect))
                <div class="answer-opt {{ $stateClass }}">
                    <span class="result-icon {{ $isUserPicked ? ($isCorrectOpt ? 'result-icon--correct' : 'result-icon--wrong') : '' }}">
                        @if($isUserPicked) {{ $icon }} @endif
                    </span>
                    <span class="answer-text">{{ $opt->text }}</span>
                    @if($isCorrectOpt && !$isUserPicked)
                        <span style="font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;padding:0.15rem 0.4rem;border-radius:0.25rem;background:rgba(34,197,94,0.15);color:#22c55e;flex-shrink:0;">
                            Richtig
                        </span>
                    @endif
                </div>
            @else
                <label class="answer-opt" onclick="updateSelectionStyle(this)">
                    <input type="checkbox" name="answer[]" value="{{ $opt->id }}"
                           class="answer-check"
                           onchange="updateSubmitButton()">
                    <span class="answer-text">{{ $opt->text }}</span>
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
                {{ $options->where('is_correct', true)->pluck('text')->join(', ') }}
            </span>
        </div>
    @endif
</div>
