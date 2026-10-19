{{-- Practice Partial: Bild auswählen (image_select)
     Text-Frage + Grid von Bild-Optionen; Auswahl über versteckte Checkbox (name="answer[]"),
     damit updateSubmitButton() greift. Markierung per CSS (:has(:checked)).
     inShell = true: Kopf (Meta, Fragetext) und Ergebnis rendert practice.blade.php. --}}

@php
    $options = $question->options;
    $userAnswerIds = collect([]);
    if (isset($userAnswer)) {
        $raw = is_string($userAnswer) ? explode(',', $userAnswer) : (array) $userAnswer;
        $userAnswerIds = collect($raw)->map(fn($v) => (int) $v)->filter();
    }
    $inShell = $inShell ?? false;
@endphp

<div class="isel-wrap">
    @unless($inShell)
        <div class="question-meta">
            <span class="question-meta-left">
                ID {{ $question->id }} &middot; LA {{ $question->lernabschnitt ?? '-' }} &middot; Bild auswählen
            </span>
        </div>
        <p class="question-text">{{ $question->frage }}</p>
        @if(isset($isCorrect))
            <p class="l-small" style="font-weight: 600; color: {{ $isCorrect ? 'var(--ok)' : 'var(--bad)' }}; margin: 0 0 12px;">{{ $isCorrect ? 'Richtig ausgewählt' : 'Auswahl prüfen' }}</p>
        @endif
    @endunless

    <div class="isel-grid">
        @foreach($options as $opt)
            @php
                $optImage = $opt->image_path ? \Illuminate\Support\Facades\Storage::url($opt->image_path) : null;
                $optAlt = $opt->text ?? 'Bildoption';
                $optCaption = trim(($opt->text ?? '') . (!empty($opt->image_source) ? ', Quelle: ' . $opt->image_source : ''));
                $state = '';
                if (isset($isCorrect)) {
                    $isUserPicked = $userAnswerIds->contains($opt->id);
                    $isCorrectOpt = (bool) $opt->is_correct;
                    $state = $isCorrectOpt ? ($isUserPicked ? 'correct' : 'correct-missed') : ($isUserPicked ? 'wrong' : 'neutral');
                }
            @endphp

            <div class="isel-item">
                @if(isset($isCorrect))
                    <div class="isel-card isel-card--{{ $state }}">
                @else
                    <label class="isel-card">
                        <input type="checkbox" name="answer[]" value="{{ $opt->id }}" onchange="updateSubmitButton()">
                @endif
                        <span class="isel-media">
                            @if($optImage)
                                <img src="{{ $optImage }}" alt="{{ $optAlt }}" loading="lazy">
                                <button type="button"
                                        class="img-zoom-btn"
                                        data-lightbox-src="{{ $optImage }}"
                                        data-lightbox-alt="{{ $optAlt }}"
                                        data-lightbox-caption="{{ $optCaption }}"
                                        aria-label="Bild in Vollansicht öffnen"
                                        title="Vollansicht"
                                        onclick="event.stopPropagation(); event.preventDefault();">
                                    <i class="bi bi-arrows-fullscreen" aria-hidden="true"></i>
                                </button>
                            @else
                                <span class="isel-card-missing">Kein Bild</span>
                            @endif
                        </span>
                        <span class="isel-card-l">
                            <span>{{ $opt->text }}</span>
                            <span class="l-check"><i class="bi {{ $state === 'wrong' ? 'bi-x-lg' : 'bi-check-lg' }}" aria-hidden="true"></i></span>
                        </span>
                @if(isset($isCorrect))
                    </div>
                @else
                    </label>
                @endif

                @if(!empty($opt->image_source))
                    <p class="isel-source">Bildquelle: {{ $opt->image_source }}</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
