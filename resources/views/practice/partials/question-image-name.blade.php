{{-- Practice Partial: Bild benennen (image_name)
     Zeigt ein Bild und darunter Text-Optionen als Checkboxes.
     Nutzt name="answer[]" damit der bestehende updateSubmitButton() greift.
     inShell = true: Kopf (Meta, Fragetext) und Ergebnis rendert practice.blade.php. --}}

@php
    $options = $question->options;
    $userAnswerIds = collect([]);
    if (isset($userAnswer)) {
        // userAnswer kann Array, Collection oder CSV sein – robust normalisieren
        $raw = is_string($userAnswer) ? explode(',', $userAnswer) : (array) $userAnswer;
        $userAnswerIds = collect($raw)->map(fn($v) => (int) $v)->filter();
    }
    $imageUrl = $question->image_path ? \Illuminate\Support\Facades\Storage::url($question->image_path) : null;
    $inShell = $inShell ?? false;
@endphp

<div class="iname-wrap">
    @unless($inShell)
        <div class="question-meta">
            <span class="question-meta-left">
                ID {{ $question->id }} &middot; LA {{ $question->lernabschnitt ?? '-' }} &middot; Bild benennen
            </span>
        </div>
        <p class="question-text">{{ $question->frage }}</p>
        @if(isset($isCorrect))
            <p class="l-small" style="font-weight: 600; color: {{ $isCorrect ? 'var(--ok)' : 'var(--bad)' }}; margin: 0 0 12px;">{{ $isCorrect ? 'Richtig benannt' : 'Leider falsch' }}</p>
        @endif
    @endunless

    {{-- Bild: antippen öffnet die Vollansicht --}}
    <figure class="l-img" style="margin-bottom: {{ empty($question->image_source) ? '18px' : '12px' }};">
        @if($imageUrl)
            <img src="{{ $imageUrl }}"
                 alt="Frage-Bild"
                 loading="lazy"
                 class="img-zoom-trigger"
                 data-lightbox-src="{{ $imageUrl }}"
                 data-lightbox-alt="Frage-Bild"
                 data-lightbox-caption="{{ $question->frage }}">
            <button type="button"
                    class="img-zoom-btn"
                    data-lightbox-src="{{ $imageUrl }}"
                    data-lightbox-alt="Frage-Bild"
                    data-lightbox-caption="{{ $question->frage }}"
                    aria-label="Bild in Vollansicht öffnen"
                    title="Vollansicht">
                <i class="bi bi-arrows-fullscreen" aria-hidden="true"></i>
            </button>
        @else
            <span class="l-img-missing">Kein Bild hinterlegt</span>
        @endif
    </figure>
    @if(!empty($question->image_source))
        <p class="l-small l-src">Bildquelle: {{ $question->image_source }}</p>
    @endif

    {{-- Antwort-Optionen --}}
    <div class="l-opts">
        @foreach($options as $opt)
            @if(isset($isCorrect))
                @php
                    $isUserPicked = $userAnswerIds->contains($opt->id);
                    $isCorrectOpt = (bool) $opt->is_correct;
                    $state = $isCorrectOpt ? ($isUserPicked ? 'correct' : 'missed') : ($isUserPicked ? 'wrong' : 'neutral');
                @endphp
                <div class="l-opt is-{{ $state }}">
                    <span class="t">{{ $opt->text }}@if($state === 'missed')<em class="l-tag">Richtig</em>@endif</span>
                    <span class="l-check"><i class="bi {{ $state === 'wrong' ? 'bi-x-lg' : 'bi-check-lg' }}" aria-hidden="true"></i></span>
                </div>
            @else
                <label class="l-opt">
                    <input type="checkbox" name="answer[]" value="{{ $opt->id }}" class="l-hide" onchange="updateSubmitButton()">
                    <span class="t">{{ $opt->text }}</span>
                    <span class="l-check"><i class="bi bi-check-lg" aria-hidden="true"></i></span>
                </label>
            @endif
        @endforeach
    </div>
</div>
