{{-- Practice Partial: Bild auswählen (image_select)
     Text-Frage + Grid von Bild-Optionen mit Checkbox-Overlay.
     Nutzt name="answer[]" damit updateSubmitButton() greift. --}}

@php
    $options = $question->options;
    $userAnswerIds = collect([]);
    if (isset($userAnswer)) {
        $raw = is_string($userAnswer) ? explode(',', $userAnswer) : (array) $userAnswer;
        $userAnswerIds = collect($raw)->map(fn($v) => (int) $v)->filter();
    }
@endphp

<style>
    .isel-wrap { display: flex; flex-direction: column; gap: 1rem; }

    .isel-grid {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: repeat(2, 1fr);
    }
    @media (min-width: 768px) {
        .isel-grid { grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    }

    .isel-card {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.875rem;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.03);
        transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
        aspect-ratio: 4 / 3;
        min-height: 44px;
        padding: 0.35rem;
    }
    html.light-mode .isel-card {
        background: #f8fafc;
        border-color: rgba(0, 51, 127, 0.12);
    }
    .isel-card:hover {
        transform: translateY(-2px);
        border-color: rgba(0, 85, 204, 0.45);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.3);
    }
    .isel-card:active { transform: scale(0.98); }
    .isel-card.is-selected {
        border-color: var(--gold-start);
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.25), 0 8px 18px rgba(0, 0, 0, 0.35);
    }

    .isel-card img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
    }

    .isel-card-missing {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .isel-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        border: 2px solid rgba(255, 255, 255, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 700;
        transition: all 0.18s ease;
        pointer-events: none;
    }
    .isel-card.is-selected .isel-badge {
        background: linear-gradient(135deg, var(--gold-start), #f59e0b);
        border-color: var(--gold-start);
        color: #1a1a1a;
    }

    .isel-card-label {
        position: absolute;
        left: 0; right: 0; bottom: 0;
        padding: 0.5rem 0.625rem;
        background: linear-gradient(180deg, transparent, rgba(0, 0, 0, 0.65));
        color: #fff;
        font-size: 0.75rem;
        line-height: 1.2;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.6);
    }

    /* Result states */
    .isel-card--correct {
        border-color: rgba(34, 197, 94, 0.6) !important;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25) !important;
    }
    .isel-card--correct-missed {
        border-color: rgba(34, 197, 94, 0.35) !important;
    }
    .isel-card--wrong {
        border-color: rgba(239, 68, 68, 0.6) !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25) !important;
    }
    .isel-card--wrong::after {
        content: '';
        position: absolute; inset: 0;
        background: rgba(239, 68, 68, 0.2);
        pointer-events: none;
    }
    .isel-card--neutral { opacity: 0.45; }

    .isel-badge--correct { background: rgba(34, 197, 94, 0.95) !important; border-color: #22c55e !important; color: #fff !important; }
    .isel-badge--wrong { background: rgba(239, 68, 68, 0.95) !important; border-color: #ef4444 !important; color: #fff !important; }

    .isel-card:focus-visible {
        outline: 3px solid var(--gold-start);
        outline-offset: 2px;
    }
    .isel-card input {
        position: absolute;
        opacity: 0;
        width: 0; height: 0;
        pointer-events: none;
    }

    .isel-item {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }
    .isel-source {
        font-size: 0.6875rem;
        line-height: 1.25;
        color: var(--text-muted);
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        letter-spacing: 0.02em;
        padding: 0 0.125rem;
        word-break: break-word;
    }
    .isel-source strong {
        color: var(--text-muted);
        font-weight: 600;
        margin-right: 0.2rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.625rem;
    }
</style>

<div class="isel-wrap">
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
            ID {{ $question->id }} &middot; LA {{ $question->lernabschnitt ?? '-' }} &middot; Bild auswählen
        </span>
    </div>

    <p class="question-text">{{ $question->frage }}</p>

    {{-- Ergebnis-Banner --}}
    @if(isset($isCorrect))
        <div class="result-banner {{ $isCorrect ? 'result-banner--correct' : 'result-banner--wrong' }}">
            <div class="result-banner-left">
                <span class="result-banner-icon">
                    <i class="bi {{ $isCorrect ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" style="color:{{ $isCorrect ? '#22c55e' : '#ef4444' }};"></i>
                </span>
                <span class="result-banner-text">{{ $isCorrect ? 'Richtig ausgewählt' : 'Auswahl prüfen' }}</span>
            </div>
        </div>
    @endif

    {{-- Grid --}}
    <div class="isel-grid">
        @foreach($options as $opt)
            @php
                $optImage = $opt->image_path ? \Illuminate\Support\Facades\Storage::url($opt->image_path) : null;
                $isUserPicked = $userAnswerIds->contains($opt->id);
                $isCorrectOpt = (bool) $opt->is_correct;

                $stateClass = '';
                $badgeClass = '';
                if (isset($isCorrect)) {
                    if ($isCorrectOpt && $isUserPicked) {
                        $stateClass = 'isel-card--correct';
                        $badgeClass = 'isel-badge--correct';
                    } elseif ($isCorrectOpt && !$isUserPicked) {
                        $stateClass = 'isel-card--correct-missed';
                        $badgeClass = 'isel-badge--correct';
                    } elseif (!$isCorrectOpt && $isUserPicked) {
                        $stateClass = 'isel-card--wrong';
                        $badgeClass = 'isel-badge--wrong';
                    } else {
                        $stateClass = 'isel-card--neutral';
                    }
                }
            @endphp

            <div class="isel-item">
            @if(isset($isCorrect))
                <div class="isel-card {{ $stateClass }}" aria-label="Option">
                    @if($optImage)
                        <img src="{{ $optImage }}" alt="{{ $opt->text ?? 'Bildoption' }}" loading="lazy">
                        <button type="button"
                                class="img-zoom-btn"
                                data-lightbox-src="{{ $optImage }}"
                                data-lightbox-alt="{{ $opt->text ?? 'Bildoption' }}"
                                data-lightbox-caption="{{ trim(($opt->text ?? '') . (!empty($opt->image_source) ? ' · Quelle: ' . $opt->image_source : '')) }}"
                                aria-label="Bild in Vollansicht öffnen"
                                title="Vollansicht"
                                onclick="event.stopPropagation(); event.preventDefault();">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                    @else
                        <div class="isel-card-missing">Kein Bild</div>
                    @endif
                    <span class="isel-badge {{ $badgeClass }}" aria-hidden="true">
                        @if($isCorrectOpt)
                            <i class="bi bi-check-lg"></i>
                        @elseif($isUserPicked)
                            <i class="bi bi-x-lg"></i>
                        @endif
                    </span>
                    @if(!empty($opt->text))
                        <span class="isel-card-label">{{ $opt->text }}</span>
                    @endif
                </div>
            @else
                <label class="isel-card" onclick="updateImageSelectStyle(this)">
                    <input type="checkbox" name="answer[]" value="{{ $opt->id }}" onchange="updateSubmitButton()">
                    @if($optImage)
                        <img src="{{ $optImage }}" alt="{{ $opt->text ?? 'Bildoption' }}" loading="lazy">
                        <button type="button"
                                class="img-zoom-btn"
                                data-lightbox-src="{{ $optImage }}"
                                data-lightbox-alt="{{ $opt->text ?? 'Bildoption' }}"
                                data-lightbox-caption="{{ trim(($opt->text ?? '') . (!empty($opt->image_source) ? ' · Quelle: ' . $opt->image_source : '')) }}"
                                aria-label="Bild in Vollansicht öffnen"
                                title="Vollansicht"
                                onclick="event.stopPropagation(); event.preventDefault();">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                    @else
                        <div class="isel-card-missing">Kein Bild</div>
                    @endif
                    <span class="isel-badge" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                    @if(!empty($opt->text))
                        <span class="isel-card-label">{{ $opt->text }}</span>
                    @endif
                </label>
            @endif

                @if(!empty($opt->image_source))
                    <div class="isel-source">
                        <strong>Bildquelle</strong> {{ $opt->image_source }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if(isset($isCorrect))
        <div class="result-summary">
            <span class="{{ $isCorrect ? 'result-label--correct' : 'result-label--wrong' }}">
                {{ $isCorrect ? 'Richtig beantwortet' : 'Falsch beantwortet' }}
            </span>
        </div>
    @endif
</div>

<script>
    (function () {
        if (window._iselStyleRegistered) return;
        window._iselStyleRegistered = true;
        window.updateImageSelectStyle = function (label) {
            const cb = label.querySelector('input[type="checkbox"]');
            if (!cb) return;
            label.classList.toggle('is-selected', cb.checked);
        };
    })();
</script>
